<?php

if (!function_exists('wptools_enqueue_scripts_with_nonce')) {
    function wptools_enqueue_scripts_with_nonce()
    {
        // Enfileirar script no frontend
        wp_enqueue_script('wptools-loading-time-admin-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);

        // Gerar nonce
        $nonce = substr(NONCE_SALT, 0, 10);

        // Localizar script
        wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));

        do_action('wptools_enqueue_additional_scripts');
    }
}
add_action('wp_enqueue_scripts', 'wptools_enqueue_scripts_with_nonce');

/*
if (!function_exists('wptools_enqueue_admin_scripts_with_nonce')) {
    function wptools_enqueue_admin_scripts_with_nonce()
    {
        wp_enqueue_script('wptools-loading-time-admin-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);

        // Gerar nonce
        $nonce = substr(NONCE_SALT, 0, 10);

        // Localizar script
        wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));

        do_action('wptools_enqueue_additional_admin_scripts');
    }
}
//add_action('admin_enqueue_scripts', 'wptools_enqueue_admin_scripts_with_nonce');
*/

if (!function_exists('wptools_register_loading_time')) {
    function wptools_register_loading_time()
    {
        global $wpdb;

        // Verificar nonce
        $nonce = sanitize_text_field($_POST['nonce']);
        if ($nonce !== substr(NONCE_SALT, 0, 10)) {
            wp_send_json_error('Invalid nonce.');
            wp_die();
        }

        // Verificar dados necessários
        if (isset($_POST['page_url']) && isset($_POST['loading_time'])) {
            $page_url = sanitize_text_field($_POST['page_url']);
            $loading_time = floatval($_POST['loading_time']); // Certifica-se de que é um número

            // Nome da tabela
            $table_name = $wpdb->prefix . 'wptools_page_load_times';

            // Verificar ou criar tabela
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    page_url VARCHAR(255) NOT NULL,
                    load_time FLOAT NOT NULL,
                    timestamp DATETIME NOT NULL
                ) $charset_collate;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            // Inserir dados
            $data = array(
                'page_url' => $page_url,
                'load_time' => $loading_time,
                'timestamp' => current_time('mysql', 1) // Usa o horário UTC
            );
            $wpdb->insert($table_name, $data);

            wp_send_json_success('Success');
        } else {
            wp_send_json_error('Invalid or missing data.');
        }

        wp_die(); // Finaliza o script AJAX
    }
}

// Registrar a ação AJAX
add_action('wp_ajax_wptools_register_loading_time', 'wptools_register_loading_time');
add_action('wp_ajax_nopriv_wptools_register_loading_time', 'wptools_register_loading_time');
