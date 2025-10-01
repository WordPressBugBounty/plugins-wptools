<?php
// Define a ação única que será usada para criar e verificar o Nonce.
// Deve ser uma string que identifica a ação (salvar tempo de carregamento).
$wptools_nonce_action = 'wptools_loading_action';

// -----------------------------------------------------------
// 1. ENFILEIRAMENTO DE SCRIPTS (FRONTEND)
// -----------------------------------------------------------
function wptools_enqueue_scripts_with_nonce3()
{
    // Acessa a variável de ação global
    global $wptools_nonce_action; 
    
    // Enfileira o script. O último 'true' garante que ele carrega no footer.
    wp_enqueue_script('wptools-loading-time-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);
    
    // Cria o Nonce real e temporário, baseado na ação definida
    $nonce = wp_create_nonce( $wptools_nonce_action ); 
    
    // Localiza o script, injetando o nonce seguro e a URL AJAX correta
    wp_localize_script('wptools-loading-time-js', 'wptools_ajax_object', array(
        'ajax_nonce' => $nonce,
        'ajax_url' => admin_url('admin-ajax.php'), // A URL AJAX correta
    ));

    do_action('wptools_enqueue_additional_scripts');
}
// Prioridade 99: Atrasar a execução para melhorar o timing, evitando o ReferenceError.
add_action('wp_enqueue_scripts', 'wptools_enqueue_scripts_with_nonce3', 99);

// -----------------------------------------------------------
// 2. ENFILEIRAMENTO DE SCRIPTS (ADMIN/BACKEND)
// -----------------------------------------------------------
function wptools_enqueue_admin_scripts_with_nonce3()
{
    // Acessa a variável de ação global
    global $wptools_nonce_action;
    
    wp_enqueue_script('wptools-loading-time-admin-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);
    
    // Cria o Nonce real e temporário
    $nonce = wp_create_nonce( $wptools_nonce_action ); 
    
    // Localiza o script, injetando o nonce seguro e a URL AJAX correta
    wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array(
        'ajax_nonce' => $nonce,
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
    
    do_action('wptools_enqueue_additional_admin_scripts');
}
// Prioridade 99: Atrasar a execução para melhorar o timing no Admin
add_action('admin_enqueue_scripts', 'wptools_enqueue_admin_scripts_with_nonce3', 99);

// -----------------------------------------------------------
// 3. FUNÇÃO DE PROCESSAMENTO AJAX
// -----------------------------------------------------------
if (!function_exists("wptools_register_loading_time3")) {
    function wptools_register_loading_time3()
    {
        global $wpdb, $wptools_nonce_action;
        
        // VALIDAÇÃO DE SEGURANÇA (NONCE):
        // Verifica o nonce. Se for inválido ou expirado, encerra a execução de forma segura.
        check_ajax_referer( $wptools_nonce_action, 'nonce' );
        
        // VALIDAÇÃO DE DADOS E INSERÇÃO
        if (isset($_POST['page_url']) && isset($_POST['loading_time'])) {
            
            // Sanitização e casting de dados
            $page_url = sanitize_text_field($_POST['page_url']);
            $loading_time = floatval($_POST['loading_time']);
            
            $table_name = $wpdb->prefix . 'wptools_page_load_times';
            
            // Lógica de criação da tabela (mantida do seu código original)
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
            
            // Inserção no banco de dados
            $data = array(
                'page_url' => $page_url,
                'load_time' =>  $loading_time,
                'timestamp' => current_time('mysql', 1) 
            );
            $wpdb->insert($table_name, $data);
            
            wp_send_json_success('Tempo de carregamento registrado com sucesso.');
        } else {
            wp_send_json_error('Dados inválidos ou ausentes para registro.');
        }
        
        wp_die();
    }
}
// Registra as ações AJAX para usuários logados e deslogados
add_action('wp_ajax_wptools_register_loading_time3', 'wptools_register_loading_time3');
add_action('wp_ajax_nopriv_wptools_register_loading_time3', 'wptools_register_loading_time3');
