<?php
/**
 * Configuration and loading.
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wptools_pcs_settings_config;

$wptools_pcs_settings_config = array();

/**
* Base Directory Setting
*/
$wptools_pcs_settings_config['base_dir'] = __DIR__ . '/';

/**
* Base URI Settings
* Use Wordpress' plugins_url to set this if not a theme.
*/
$wptools_pcs_settings_config['base_uri'] = plugins_url( 'settings' , dirname(__FILE__) ) . '/';

/**
* Requred Classes and Libraries
*/

// add_action('plugins_loaded', 'wptools_localization_init');


// add_action('plugins_loaded', 'wptools_localization_init');





require_once (plugin_dir_path(__FILE__) . "containers.php");
require_once (plugin_dir_path(__FILE__) . "fields.php");
require_once (plugin_dir_path(__FILE__) . "factories.php");
require_once (plugin_dir_path(__FILE__) . "page-builders.php");

