<?php

/**
 * 2022/01/11 - 22-1-2025
 * acertado debug
 *
 * */
// Exit if accessed directly
if (! defined('ABSPATH'))  exit;

function wptools_sysinfo_get()
{
    global $wpdb;
    $wptools_userAgentOri = wptools_get_ua2();

    // Get theme info
    $theme_data   = wp_get_theme();
    $theme        = $theme_data->Name . ' ' . $theme_data->Version;
    $parent_theme = $theme_data->Template;
    if (! empty($parent_theme)) {
        $parent_theme_data = wp_get_theme($parent_theme);
        $parent_theme      = $parent_theme_data->Name . ' ' . $parent_theme_data->Version;
    }
    // Try to identify the hosting provider
    $host = gethostname();
    if ($host === false) {
        $host = wptools_get_host();
    }
    $return  = '=== Begin System Info 2 (Generated ' . date('Y-m-d H:i:s') . ') ===' . "\n\n";
    $file_path_from_plugin_root = str_replace(WP_PLUGIN_DIR . '/', '', __DIR__);
    $path_array = explode('/', $file_path_from_plugin_root);
    // Plugin folder is the first element
    $plugin_folder_name = reset($path_array);
    $return .= '-- Plugin' . "\n\n";
    $return .= 'Name:                  ' .  $plugin_folder_name . "\n";
    $return .= 'Version:                  ' . WPTOOLSVERSION;
    $return .= "\n\n";
    $return .= '-- Site Info' . "\n\n";
    $return .= 'Site URL:                 ' . site_url() . "\n";
    $return .= 'Home URL:                 ' . home_url() . "\n";
    $return .= 'Multisite:                ' . (is_multisite() ? 'Yes' : 'No') . "\n";
    if ($host) {
        $return .= "\n" . '-- Hosting Provider' . "\n\n";
        $return .= 'Host:                     ' . $host . "\n";
    }
    $return .= "\n" . '-- User Browser' . "\n\n";
    $return .= $wptools_userAgentOri; // $browser;
    $return .= "\n\n";
    $locale = get_locale();
    // WordPress configuration
    $return .= "\n" . '-- WordPress Configuration' . "\n\n";
    $return .= 'Version:                  ' . get_bloginfo('version') . "\n";
    $return .= 'Language:                 ' . (!empty($locale) ? $locale : 'en_US') . "\n";
    $return .= 'Permalink Structure:      ' . (get_option('permalink_structure') ? get_option('permalink_structure') : 'Default') . "\n";
    $return .= 'Active Theme:             ' . $theme . "\n";
    if ($parent_theme !== $theme) {
        $return .= 'Parent Theme:             ' . $parent_theme . "\n";
    }
    $return .= 'ABSPATH:                  ' . ABSPATH . "\n";
    $return .= 'Plugin Dir:                  ' . WPTOOLSPATH . "\n";
    $return .= 'Table Prefix:             ' . 'Length: ' . strlen($wpdb->prefix) . '   Status: ' . (strlen($wpdb->prefix) > 16 ? 'ERROR: Too long' : 'Acceptable') . "\n";
    //$return .= 'Admin AJAX:               ' . ( wptools_test_ajax_works() ? 'Accessible' : 'Inaccessible' ) . "\n";

    if (defined('WP_DEBUG')) {
        $return .= 'WP_DEBUG:                 ' . (WP_DEBUG ? 'Enabled' : 'Disabled');
    } else
        $return .= 'WP_DEBUG:   
	              ' .  'Not Set\n';
    $return .= "\n";
    $return .= 'Display Errors:           ' . (ini_get('display_errors') ? 'On (' . ini_get('display_errors') . ')' : 'N/A') . "\n";


    $return .= "\n";


    $return .= 'WP Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";


    //Error Log configuration





    /*
    $return .= "\n" . '-- Error Handler Information' . "\n\n";

    if (function_exists('set_error_handler')) {
        $return .= 'set_error_handler() Exists:   Yes' . "\n";

        //$current_error_handler = set_error_handler(function () { / * no-op * / }); // Obtém o manipulador atual sem alterar
        restore_error_handler(); // Restaura o manipulador anterior

        if ($current_error_handler) {
            $return .= 'set_error_handler() in Use:   Yes' . "\n";

            if (is_array($current_error_handler)) { // Se for um array, é um manipulador de classe/objeto
                if (isset($current_error_handler[0]) && is_object($current_error_handler[0]) && method_exists($current_error_handler[0], $current_error_handler[1])) {
                    $return .= 'Handler Details:        Object: ' . get_class($current_error_handler[0]) . ', Method: ' . $current_error_handler[1] . "\n";
                } else {
                    $return .= 'Handler Details:        Unknown (Class/Object Handler)' . "\n";
                }

            } elseif (is_string($current_error_handler)) { // Se for uma string, é uma função
                $return .= 'Handler Details:        Function: ' . $current_error_handler . "\n";
            } else {
                $return .= 'Handler Details:        Unknown (Other Handler)' . "\n";
            }
        } else {
            $return .= 'set_error_handler() in Use:   No' . "\n";
        }
    } else {
        $return .= 'set_error_handler() Exists:   No' . "\n";
    }
    */


    $return .= "\n" . '-- Error Handler Information' . "\n\n";

    try {
        if (function_exists('set_error_handler')) {
            $return .= 'set_error_handler Exists:   Yes' . "\n";

            /*
    
            try { // Inner try-catch for the set_error_handler operations
                $current_error_handler = set_error_handler(function () { // no-op  });
                restore_error_handler();
    
                if ($current_error_handler) {
                    $return .= 'set_error_handler() in Use:   Yes' . "\n";
    
                    if (is_array($current_error_handler)) {
                        try { // Even more specific try-catch for object handler introspection
                            if (isset($current_error_handler[0]) && is_object($current_error_handler[0]) && method_exists($current_error_handler[0], $current_error_handler[1])) {
                                $return .= 'Handler Details:        Object: ' . get_class($current_error_handler[0]) . ', Method: ' . $current_error_handler[1] . "\n";
                            } else {
                                $return .= 'Handler Details:        Unknown (Class/Object Handler - Invalid)' . "\n";
                            }
                        } catch (Exception $e) {
                            $return .= 'Handler Details:        Error introspecting object handler: ' . $e->getMessage() . "\n";
                        }
                    } elseif (is_string($current_error_handler)) {
                        $return .= 'Handler Details:        Function: ' . $current_error_handler . "\n";
                    } else {
                        $return .= 'Handler Details:        Unknown (Other Handler)' . "\n";
                    }
                } else {
                    $return .= 'set_error_handler() in Use:   No' . "\n";
                }
            } catch (Exception $e) {
                $return .= 'Error getting current error handler: ' . $e->getMessage() . "\n";
            }

            */
    
        } else {
            $return .= 'set_error_handler() Exists:   No' . "\n";
        }
    } catch (Exception $e) {
        $return .= 'Error checking error handler functions: ' . $e->getMessage() . "\n";
    }


    $return .= "\n" . '-- PHP Error Log Configuration' . "\n\n";

    $error_log_path = ABSPATH . 'error_log'; // Consistent use of single quotes

    $return .= 'Root Place:                     ' . (file_exists($error_log_path) ? 'Exists. ('.$error_log_path.')'  : 'Does Not Exist') . "\n"; // More descriptive wording

    if (file_exists($error_log_path)) { // Check if the file exists before attempting to access its size, readability, or writability. This prevents warnings or errors if the file doesn't exist.
        $return .= 'Size:                     ' . size_format(filesize($error_log_path)) . "\n"; // Use filesize() for file size and size_format() for human-readable format.  file_size() doesn't exist in PHP.
        $return .= 'Readable:                     ' . (is_readable($error_log_path) ? 'Yes' : 'No') . "\n";  // Use is_readable() instead of file_readable(). More common and accurate.
        $return .= 'Writable:                     ' . (is_writable($error_log_path) ? 'Yes' : 'No') . "\n"; // Use is_writable() instead of file_writable(). More common and accurate.
    } else {
        $return .= 'Size:                     N/A' . "\n";
        $return .= 'Readable:                     N/A' . "\n";
        $return .= 'Writable:                     N/A' . "\n";
    }




    

    $return .= "\n" . '-- WordPress Debug Log Configuration' . "\n\n";

    $debug_log_path = WP_CONTENT_DIR . '/debug.log'; // Default path
    
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG !== true && is_string(WP_DEBUG_LOG)) {
        $debug_log_path = WP_DEBUG_LOG; // Override if it is defined and it is a string path.
    }
    
    $return .= 'Debug Log Path:             ' . $debug_log_path . "\n";
    
    try {
        if (file_exists($debug_log_path)) {
            $return .= 'File Exists:                  Yes' . "\n";
    
            try {
                $fileSize = filesize($debug_log_path);
                $return .= 'Size:                         ' . size_format($fileSize) . "\n";
            } catch (Exception $e) {
                $return .= 'Size:                         Error getting file size: ' . $e->getMessage() . "\n";
            }
    
            $return .= 'Readable:                     ' . (is_readable($debug_log_path) ? 'Yes' : 'No') . "\n";
            $return .= 'Writable:                     ' . (is_writable($debug_log_path) ? 'Yes' : 'No') . "\n";
    
            $isDebugEnabled = defined('WP_DEBUG') && WP_DEBUG;
            $isLogEnabled = defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
    
            $return .= 'WP_DEBUG Enabled:            ' . ($isDebugEnabled ? 'Yes' : 'No') . "\n";
            $return .= 'WP_DEBUG_LOG Enabled:        ' . ($isLogEnabled ? 'Yes' : 'No') . "\n";
    
            if ($isDebugEnabled && $isLogEnabled) {
                $return .= 'Debug Logging Active:       Yes' . "\n";
            } elseif ($isDebugEnabled) {
                $return .= 'Debug Logging Active:       No (Logging to file is disabled)' . "\n";
            } else {
                $return .= 'Debug Logging Active:       No (WP_DEBUG is disabled)' . "\n";
            }
    
        } else {
            $return .= 'File Exists:                  No' . "\n";
            $return .= 'Size:                         N/A' . "\n";
            $return .= 'Readable:                     N/A' . "\n";
            $return .= 'Writable:                     N/A' . "\n";
            $return .= 'WP_DEBUG Enabled:            ' . (defined('WP_DEBUG') && WP_DEBUG ? 'Yes' : 'No') . "\n";
            $return .= 'WP_DEBUG_LOG Enabled:        ' . (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Yes' : 'No') . "\n";
            $return .= 'Debug Logging Active:       No (File does not exist)' . "\n";
        }
    } catch (Exception $e) {
        $return .= 'Error checking debug log file: ' . $e->getMessage() . "\n";
    }
    
    
    $return .= 'WP_Query Debug: ' . (defined('WP_QUERY_DEBUG') && WP_QUERY_DEBUG ? 'Yes' : 'No') . "\n";
    
    // Add the new constants to the report:
    $return .= "\n" . '-- Additional Debugging Constants' . "\n\n";
    $return .= 'SCRIPT_DEBUG:                ' . (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'Yes' : 'No') . "\n";
    $return .= 'SAVEQUERIES:                 ' . (defined('SAVEQUERIES') && SAVEQUERIES ? 'Yes' : 'No') . "\n";
    $return .= 'WP_DEBUG_DISPLAY:            ' . (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'Yes' : 'No') . "\n";



    // WordPress active Theme
    $return .= "\n" . '-- WordPress Active Theme' . "\n\n";
    $return .= 'Theme Name:             ' . $parent_theme . "\n";
    // return $return;
    // Get plugins that have an update
    $updates = get_plugin_updates();
    // Must-use plugins
    // NOTE: MU plugins can't show updates!
    $muplugins = get_mu_plugins();
    if (count($muplugins) > 0) {
        $return .= "\n" . '-- Must-Use Plugins' . "\n\n";
        foreach ($muplugins as $plugin => $plugin_data) {
            $return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
        }
    }
    // WordPress active plugins
    $return .= "\n" . '-- WordPress Active Plugins' . "\n\n";
    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', array());
    foreach ($plugins as $plugin_path => $plugin) {
        if (!in_array($plugin_path, $active_plugins)) {
            continue;
        }
        $update = (array_key_exists($plugin_path, $updates)) ? ' (needs update - ' . $updates[$plugin_path]->update->new_version . ')' : '';
        $plugin_url = '';
        if (! empty($plugin['PluginURI'])) {
            $plugin_url = $plugin['PluginURI'];
        } elseif (! empty($plugin['AuthorURI'])) {
            $plugin_url = $plugin['AuthorURI'];
        } elseif (! empty($plugin['Author'])) {
            $plugin_url = $plugin['Author'];
        }
        if ($plugin_url) {
            $plugin_url = "\n" . $plugin_url;
        }
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . $plugin_url . "\n\n";
    }
    // WordPress inactive plugins
    $return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";
    foreach ($plugins as $plugin_path => $plugin) {
        if (in_array($plugin_path, $active_plugins)) {
            continue;
        }
        $update = (array_key_exists($plugin_path, $updates)) ? ' (needs update - ' . $updates[$plugin_path]->update->new_version . ')' : '';
        $plugin_url = '';
        if (! empty($plugin['PluginURI'])) {
            $plugin_url = $plugin['PluginURI'];
        } elseif (! empty($plugin['AuthorURI'])) {
            $plugin_url = $plugin['AuthorURI'];
        } elseif (! empty($plugin['Author'])) {
            $plugin_url = $plugin['Author'];
        }
        if ($plugin_url) {
            $plugin_url = "\n" . $plugin_url;
        }
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . $plugin_url . "\n\n";
    }
    if (is_multisite()) {
        // WordPress Multisite active plugins
        $return .= "\n" . '-- Network Active Plugins' . "\n\n";
        $plugins = wp_get_active_network_plugins();
        $active_plugins = get_site_option('active_sitewide_plugins', array());
        foreach ($plugins as $plugin_path) {
            $plugin_base = plugin_basename($plugin_path);
            if (!array_key_exists($plugin_base, $active_plugins)) {
                continue;
            }
            $update = (array_key_exists($plugin_path, $updates)) ? ' (needs update - ' . $updates[$plugin_path]->update->new_version . ')' : '';
            $plugin  = get_plugin_data($plugin_path);
            $plugin_url = '';
            if (! empty($plugin['PluginURI'])) {
                $plugin_url = $plugin['PluginURI'];
            } elseif (! empty($plugin['AuthorURI'])) {
                $plugin_url = $plugin['AuthorURI'];
            } elseif (! empty($plugin['Author'])) {
                $plugin_url = $plugin['Author'];
            }
            if ($plugin_url) {
                $plugin_url = "\n" . $plugin_url;
            }
            $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . $plugin_url . "\n\n";
        }
    }
    // Server configuration 
    $return .= "\n" . '-- Webserver Configuration' . "\n\n";
    $return .= 'OS Type & Version:        ' . wptools_OSName();
    $return .= 'PHP Version:              ' . PHP_VERSION . "\n";
    $return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
    $return .= 'Webserver Info:           ' . sanitize_text_field($_SERVER['SERVER_SOFTWARE']) . "\n";
    // PHP configs... 
    $return .= "\n" . '-- PHP Configuration' . "\n\n";
    $return .= 'PHP Memory Limit:             ' . ini_get('memory_limit') . "\n";
    $return .= 'Upload Max Size:          ' . ini_get('upload_max_filesize') . "\n";
    $return .= 'Post Max Size:            ' . ini_get('post_max_size') . "\n";
    $return .= 'Upload Max Filesize:      ' . ini_get('upload_max_filesize') . "\n";
    $return .= 'Time Limit:               ' . ini_get('max_execution_time') . "\n";
    $return .= 'Max Input Vars:           ' . ini_get('max_input_vars') . "\n";
    $return .= 'Display Errors:           ' . (ini_get('display_errors') ? 'On (' . ini_get('display_errors') . ')' : 'N/A') . "\n";
   

    $return .= 'Fopen:                     ' . (function_exists('fopen') ? 'Supported' : 'Not Supported') . "\n";
  
    $return .= 'Fseek:                     ' . (function_exists('fseek') ? 'Supported' : 'Not Supported') . "\n";
    $return .= 'Ftell:                     ' . (function_exists('ftell') ? 'Supported' : 'Not Supported') . "\n";
    $return .= 'Fread:                     ' . (function_exists('fread') ? 'Supported' : 'Not Supported') . "\n";
  



    // PHP extensions and such
    $return .= "\n" . '-- PHP Extensions' . "\n\n";
    $return .= 'cURL:                     ' . (function_exists('curl_init') ? 'Supported' : 'Not Supported') . "\n";
    $return .= 'fsockopen:                ' . (function_exists('fsockopen') ? 'Supported' : 'Not Supported') . "\n";
    $return .= 'SOAP Client:              ' . (class_exists('SoapClient') ? 'Installed' : 'Not Installed') . "\n";
    $return .= 'Suhosin:                  ' . (extension_loaded('suhosin') ? 'Installed' : 'Not Installed') . "\n";
    $return .= 'SplFileObject:            ' . (class_exists('SplFileObject') ? 'Installed' : 'Not Installed') . "\n";
   
    $return .= "\n" . '=== End System Info2 ===';
    return $return;
}
function wptools_OSName()
{
    try {
        if (false == function_exists("shell_exec") || false == @is_readable("/etc/os-release")) {
            return false;
        }
        $os = shell_exec('cat /etc/os-release | grep "PRETTY_NAME"');
        return explode("=", $os)[1];
    } catch (Exception $e) {
        // echo 'Message: ' .$e->getMessage();
        return false;
    }
}
function wptools_get_host()
{
    if (isset($_SERVER['SERVER_NAME'])) {
        $server_name = sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME']));
    } else {
        $server_name = 'Unknow';
    }
    $host = 'DBH: ' . DB_HOST . ', SRV: ' . $server_name;
    return $host;
}
function wptools_get_ua2()
{
    if (! isset($_SERVER['HTTP_USER_AGENT'])) {
        return '';
    }
    $ua = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
    if (!empty($ua))
        return trim($ua);
    else
        return "";
}
