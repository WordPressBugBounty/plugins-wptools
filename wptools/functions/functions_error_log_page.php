<?php
// 2025/01/15
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly
//register_tick_function('meu_tick_function');
//declare(ticks=1);
if (!$wptools_is_admin) {
    return;
}
function wptools_myplugin_enqueue_scripts($hook)
{
    // debug4($hook);
    if ($hook === 'wp-tools_page_wptools_options21') { // Ajuste para o slug da sua página de dashboard
        // wp_enqueue_script('myplugin-settings-script', plugins_url('settings.js', __FILE__), ['jquery'], '1.0', true);
        wp_enqueue_script('myplugin-settings-script', WPTOOLSURL .
            'assets/js/error_log_settings.js', array('jquery'));
        // debug4(WPTOOLSURL .
        //    'assets/js/error_log_settings.js');
        wp_localize_script('myplugin-settings-script', 'myplugin_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wptools_myplugin_nonce'),
        ]);



        // Define as mensagens traduzíveis
        /*
        $translations = [
            'setupButton' => __('Setup', 'textdomain'),
            'closeButton' => __('Close', 'textdomain'),
            'loadingMessage' => __('Loading...', 'textdomain'),
            'errorLoadingLogs' => __('Error loading logs.', 'textdomain'),
            'errorLoadingLogs2' => __('Error loading logs (2).', 'textdomain'),
            'noLogsFound' => __('No log files found. The log files should exist. Please contact your hosting provider.', 'textdomain'),
            'logFilesHeader' => __('Log Files, choose one.', 'textdomain'),
            'saveButton' => __('Save', 'textdomain'),
            'selectLogAlert' => __('Please select a log file.', 'textdomain'),
            'logSavedSuccess' => __('Log successfully loaded.', 'textdomain'),
            'logSaveError' => __('Error loading the log.', 'textdomain'),
            'ajaxError' => __('AJAX request error. Check the console for more details.', 'textdomain'),
        ];
        */


        //wp_enqueue_script('meu-script', 'caminho/para/meu-script.js', array('jquery'), null, true);






        // Gera o nonce
        $wptools_nonce = wp_create_nonce('wptools_ajax_nonce');

        // Passa o nonce e outras traduções para o JavaScript
        wp_localize_script('myplugin-settings-script', 'wptoolsTranslations', array(
            'setupButton' => __('Setup', 'wptools'),
            'closeButton' => __('Close', 'wptools'),
            'loadingMessage' => __('Loading logs...', 'wptools'),
            'errorLoadingLogs' => __('Error loading logs.', 'wptools'),
            'errorLoadingLogs2' => __('An error occurred while loading logs.', 'wptools'),
            'noLogsFound' => __('No logs found.', 'wptools'),
            'saveButton' => __('Save', 'wptools'),
            'logSavedSuccess' => __('Log saved successfully!', 'wptools'),
            'logSaveError' => __('Error saving log.', 'wptools'),
            'ajaxError' => __('AJAX error. Please try again.', 'wptools'),
            'selectLogAlert' => __('Please select a log file.', 'wptools'),
            'logFilesHeader' => __('Log Files', 'wptools'),
            'nonce' => $wptools_nonce, // Nonce passado para o JavaScript
        ));




        // Passa as mensagens para o script
        //  wp_localize_script('myplugin-settings-script', 'wptoolsTranslations', $translations);
    } else {
        //debug4("nao entrou");
    }
}
add_action('admin_enqueue_scripts', 'wptools_myplugin_enqueue_scripts');

add_action('wp_ajax_wptools_find_logs', 'wptools_find_logs');
function wptools_find_logs()
{


    if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'wptools_ajax_nonce')) {
        wp_send_json_error('Nonce inválido');
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Você não tem permissão para executar esta ação.');
    }



    // Array que será preenchida com os logs encontrados
    $logs = [];
    // Função para adicionar um arquivo de log à array $logs
    function add_log_to_array($path, &$logs)
    {
        if (file_exists($path)) {
            // Verifica se o caminho já existe na array $logs
            $path_exists = false;
            foreach ($logs as $log) {
                if ($log['path'] === $path) {
                    $path_exists = true;
                    break;
                }
            }
            // Se o caminho não existir, adiciona o log à array
            if (!$path_exists) {
                $logs[] = [
                    'name' => basename($path), // Nome do arquivo (ex: error_log)
                    'path' => $path,           // Caminho completo do arquivo
                    'size' => wptools_format_size(filesize($path)), // Tamanho formatado (ex: 12MB)
                ];
            }
        }
    }
    // Função para formatar o tamanho do arquivo (ex: bytes para MB)
    function wptools_format_size($size)
    {
        if ($size >= 1048576) {
            return round($size / 1048576, 2) . 'MB'; // Converte para MB
        } elseif ($size >= 1024) {
            return round($size / 1024, 2) . 'KB'; // Converte para KB
        } else {
            return $size . 'B'; // Mantém em bytes
        }
    }
    $error_log_path = '';
    // Caminho do error_log definido no php.ini
    if (function_exists('ini_get')) {
        $error_log_path = trim(ini_get("error_log"));
    }
    // Adiciona o error_log do php.ini, se existir e for diferente do padrão
    if (!empty($error_log_path) && $error_log_path !== trim(ABSPATH . "error_log")) {
        add_log_to_array($error_log_path, $logs);
    }
    // Caminhos padrão de logs
    $default_logs = [
        ABSPATH . "error_log",
        ABSPATH . "php_errorlog",
        plugin_dir_path(__FILE__) . "/error_log",
        plugin_dir_path(__FILE__) . "/php_errorlog",
        get_theme_root() . "/error_log",
        get_theme_root() . "/php_errorlog",
        WP_CONTENT_DIR . "/debug.log"
    ];
    foreach ($default_logs as $log_path) {
        add_log_to_array($log_path, $logs);
    }
    // Caminhos de logs na área de administração
    $bill_admin_path = str_replace(get_bloginfo("url") . "/", ABSPATH, get_admin_url());
    $admin_logs = [
        $bill_admin_path . "/error_log",
        $bill_admin_path . "/php_errorlog"
    ];
    foreach ($admin_logs as $log_path) {
        add_log_to_array($log_path, $logs);
    }
    // Caminhos de logs em plugins
    $bill_plugins = array_slice(scandir(plugin_dir_path(__FILE__)), 2);
    foreach ($bill_plugins as $bill_plugin) {
        $plugin_path = plugin_dir_path(__FILE__) . "/" . $bill_plugin;
        if (is_dir($plugin_path)) {
            add_log_to_array($plugin_path . "/error_log", $logs);
            add_log_to_array($plugin_path . "/php_errorlog", $logs);
        }
    }
    // Caminhos de logs em temas
    $bill_themes = array_slice(scandir(get_theme_root()), 2);
    foreach ($bill_themes as $bill_theme) {
        $theme_path = get_theme_root() . "/" . $bill_theme;
        if (is_dir($theme_path)) {
            add_log_to_array($theme_path . "/error_log", $logs);
            add_log_to_array($theme_path . "/php_errorlog", $logs);
        }
    }
    $logs9999 = [
        ['name' => 'error_log', 'path' => ABSPATH . '/error_log', 'size' => '12MB'],
        ['name' => 'debug_log', 'path' => WP_CONTENT_DIR . '/debug.log', 'size' => '8MB'],
    ];
    // Get the selected log file from options
    $selected_log = get_option('wptools_log_file_name_option');
    // Send the response back to JavaScript
    // error_log('sl : ' . $selected_log);
    wp_send_json_success(array('data' => $logs, 'selected_log' => $selected_log));
}
function wptools_save_log_option()
{

    // Verifica o nonce
    if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'wptools_ajax_nonce')) {
        wp_send_json_error('Nonce inválido');
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Você não tem permissão para executar esta ação.');
    }



    // Get the selected log file path from the AJAX request
    if (isset($_POST['log_file'])) {
        $log_file = sanitize_text_field($_POST['log_file']); // Sanitize the log file path
        // Get current option value
        $current_value = get_option('wptools_log_file_name_option');
        // Log the current value for debugging
        error_log('Current log option: ' . var_export($current_value, true));
        if ($current_value !== $log_file) {
            // Save new log file option only if it's different
            $result = update_option('wptools_log_file_name_option', $log_file); // Save to WordPress options
            error_log('Result after update_option: ' . var_export($result, true)); // Check the result
            if ($result) {
                wp_send_json_success(array('message' => 'Log file option saved successfully'));
            } else {
                wp_send_json_error(array('message' => 'Failed to save log file option'));
            }
        } else {
            wp_send_json_success(array('message' => 'Log file option is already set to the selected value'));
        }
    } else {
        wp_send_json_error(array('message' => 'No log file selected'));
    }
}
// Register the AJAX action to handle saving the selected log option
add_action('wp_ajax_wptools_save_log_option', 'wptools_save_log_option');
