<?php

/**
 * @author William Sergio Minossi
 * @copyright 2023 11 25
 */
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}
//
$wptools_options = array(
    'wptools_server_performance',
    'wptools_checkversion',
    'wptools_last_notification_date',
    'wptools_last_notification_date2',
    'wptools_activated_notice',
    'wptools_was_activated',
    'wptools_activated_pointer',
    'wptools_dismiss',
    'wptools_dismiss_language',
    'wptools_plugin_error',
    'wptools_radio_email_weekly_error_notification',
    'wptools_disable_ziparchive',
    'wptools_last_load_issues',
    'wptools_last_email_sent_issues',
    'wptools_activated_pointer',
    'wptools_activated_notice'
);


// Apaga todas as opções no site atual
foreach ($wptools_options as $option_name => $option_value) {
    if (is_multisite()) {
        // Apaga a opção no site atual em uma instalação multisite
        delete_site_option($option_name);
    } else {
        // Apaga a opção no site único
        delete_option($option_name);
    }
}

// Drop a custom db table

/*
global $wpdb;
$current_table = $wpdb->prefix . 'wptools_errors';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'wptools_page_load_times';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
*/


global $wpdb;

$wptools_tables = [
    'wptools_errors',
    'wptools_page_load_times'
];

foreach ($wptools_tables as $table) {
    try {
        $current_table = $wpdb->prefix . $table;
        $drop_result = $wpdb->query("DROP TABLE IF EXISTS $current_table");

        if (false === $drop_result) {
            // Não lança exceção, apenas ignora ou personaliza o erro
        }
    } catch (Exception $e) {
        // Ignore o erro ou registre uma mensagem personalizada
        // error_log($e->getMessage());  // Opcional: descomente se quiser registrar o erro
    }
}




//wp_clear_scheduled_hook('wptools_weekly_cron_job_loadtime');
//wp_clear_scheduled_hook('wptools_weekly_cron_job');


$plugin_name = 'bill-catch-errors.php'; // Name of the plugin file to be removed

// Retrieve all must-use plugins
$wp_mu_plugins = get_mu_plugins();

// MU-Plugins directory
$mu_plugins_dir = WPMU_PLUGIN_DIR;

if (isset($wp_mu_plugins[$plugin_name])) {
    // Get the plugin's destination path
    $destination = $mu_plugins_dir . '/' . $plugin_name;

    // Attempt to remove the plugin
    if (!unlink($destination)) {
        // Log the error if the file could not be deleted
        error_log("Error removing the plugin file from the MU-Plugins directory: $destination");
    } else {
        // Optionally, log success if the plugin is removed successfully
        // error_log("Successfully removed the plugin file: $destination");
    }
}
