<?PHP



function wptools_enqueue_scripts_with_nonce3()
{
    // Enfileirar seu script no frontend
    wp_enqueue_script('wptools-loading-time-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);
    // $nonce = wp_create_nonce('wptools-add-loading-info');
    $nonce = substr(NONCE_SALT, 0, 10);
    wp_localize_script('wptools-loading-time-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));
    do_action('wptools_enqueue_additional_scripts');
}
add_action('wp_enqueue_scripts', 'wptools_enqueue_scripts_with_nonce3');
function wptools_enqueue_admin_scripts_with_nonce3()
{
    wp_enqueue_script('wptools-loading-time-admin-js', WPTOOLSURL . 'js/loading-time.js', array('jquery'), null, true);
    // $nonce = wp_create_nonce('wptools-add-loading-info-admin');
    $nonce = substr(NONCE_SALT, 0, 10);
    wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));
    do_action('wptools_enqueue_additional_admin_scripts');
}
add_action('admin_enqueue_scripts', 'wptools_enqueue_admin_scripts_with_nonce3');




// Function to register loading time in the database
if (!function_exists("wptools_register_loading_time3")) {
    function wptools_register_loading_time3()
    {
        global $wpdb;
        //   Verify nonce
        $nonce = esc_attr($_POST['nonce']);
        if (!$nonce === substr(NONCE_SALT, 0, 10)) {
            wp_send_json_error('Invalid nonce.');
            wp_die();
        }
        if (
            isset($_POST['page_url'])
            && isset($_POST['loading_time'])
        ) {
            $page_url = esc_attr($_POST['page_url']);
            $loading_time = $_POST['loading_time'];
            $table_name = $wpdb->prefix . 'wptools_page_load_times';
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
            $data = array(
                'page_url' => $page_url,
                'load_time' =>  $loading_time,
                'timestamp' => current_time('mysql', 1) // Usa o horário atual do WordPress
            );
            $wpdb->insert($table_name, $data);
            wp_send_json_success('Success'); // You can send any desired success response
        } else {
            wp_send_json_error('Invalid or missing data.');
        }
        wp_die('ok'); // End the execution of the WordPress AJAX script
    }
}
// Register the AJAX action in WordPress
add_action('wp_ajax_wptools_register_loading_time3', 'wptools_register_loading_time3');
add_action('wp_ajax_nopriv_wptools_register_loading_time3', 'wptools_register_loading_time3');
