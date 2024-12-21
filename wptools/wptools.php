<?php
/*
Plugin Name: wptools
Plugin URI:  https://BillMinozzi.com
Description: WP Tools Increase PHP memory limit, time limit, max upload file size limit without editing any files.Show PHP info, PHP and Javascript errors, Server info and more tools. 
Version:     4.76
Author:      Bill Minozzi
Plugin URI:  https://BillMinozzi.com
Domain Path: /language
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
// meta description(?) for page or posts
*/
if (!defined('ABSPATH')) {
	die('We\'re sorry, but you can not directly access this file.');
}
//
// Debug
//if (!ini_get('error_log') or empty(trim(ini_get('error_log'))));
//   @ini_set('error_log', ABSPATH . 'error_log'); // path to server-writable log file

/*
// error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
 error_reporting(E_ERROR | E_WARNING | E_PARSE);
*/

/*
debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

$wptools_plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$wptools_plugin_version = $wptools_plugin_data['Version'];
define('WPTOOLSVERSION', $wptools_plugin_version);
define('WPTOOLSHOMEURL', admin_url());
define('WPTOOLSPATH', plugin_dir_path(__file__));
define('WPTOOLSURL', plugin_dir_url(__file__));
define('WPTOOLSIMAGES', plugin_dir_url(__file__) . 'images');
define('WPTOOLSADMURL', admin_url());

if (is_admin())
    add_action('init', 'wptools_localization_init');
	//add_action('plugins_loaded', 'wptools_localization_init');

$wptools_request_url = trim(sanitize_text_field($_SERVER['REQUEST_URI']));
$wptools_bypass_wpdebug =  trim(sanitize_text_field(get_option('wptools_bypass_wpdebug', 'no')));
if (!defined('WP_DEBUG'))
	define('WP_DEBUG', false);
if ($wptools_bypass_wpdebug == 'yes' and WP_DEBUG == false) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
$wptools_radio_server_load =  trim(sanitize_text_field(get_option('wptools_radio_server_load', 'yes')));
$wptools_radio_server_load = strtolower($wptools_radio_server_load);
$wptools_disable_lazy =  trim(sanitize_text_field(get_option('wptools_disable_lazy', 'yes')));
$wptools_disable_emojis =  trim(sanitize_text_field(get_option('wptools_disable_emojis', 'yes')));
$wptools_show_pageload_info =  trim(sanitize_text_field(get_option('wptools_show_pageload_info', 'yes')));
$wptools_classic_widget =  trim(sanitize_text_field(get_option('wptools_classic_widget', 'no')));
$wptools_show_adminbar =  trim(sanitize_text_field(get_option('wptools_show_adminbar', 'no')));
$wptools_vote =  trim(sanitize_text_field(get_option('wptools_vote', '')));
$wptools_checkversion =  trim(sanitize_text_field(get_option('wptools_checkversion', '')));
$wptools_show_errors =  trim(sanitize_text_field(get_option('wptools_show_errors', 'yes')));
$wptools_logo =  trim(sanitize_text_field(get_option('wptools_logo', '')));
$wptools_erase_readme =  trim(sanitize_text_field(get_option('wptools_erase_readme', '')));
$wptools_remove_icon =  trim(sanitize_text_field(get_option('wptools_remove_icon', '')));
$wptools_logo_width =  trim(sanitize_text_field(get_option('wptools_logo_width', '')));
$wptools_logo_height =  trim(sanitize_text_field(get_option('wptools_logo_height', '')));
$wptools_jquery_version = trim(sanitize_text_field(get_option('wptools_jquery_version', 'default')));
$wptools_disable_selfping  = trim(sanitize_text_field(get_option('wptools_disable_selfping', '')));
$wptools_auto_updates = trim(sanitize_text_field(get_option('wptools_auto_updates', 'yes')));

$wptools_improve_wp_dashboard_performance  = trim(sanitize_text_field(get_option("wptools_improve_wp_dashboard_performance", "no")));






$wptools_activated_notice =  trim(sanitize_text_field(get_option('wptools_activated_notice', '0')));


// $wptools_was_activated =  trim(sanitize_text_field(get_option('wptools_was_activated', '0')));

$wptools_activated_pointer =  trim(sanitize_text_field(get_option('wptools_activated_pointer', '0')));







$wptools_radio_email_weekly_error_notification = trim(sanitize_text_field(get_option('wptools_radio_email_weekly_error_notification', 'yes')));

$wptools_radio_email_weekly_load_notification = trim(sanitize_text_field(get_option('wptools_radio_email_weekly_load_notification', 'yes')));

$wptools_disable_ziparchive = trim(sanitize_text_field(get_option('wptools_disable_ziparchive', 'no')));


$wptools_radio_email_error_notification =  trim(sanitize_text_field(get_option('wptools_radio_email_error_notification', 'no')));

if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}
if (is_admin()) {
	add_action('admin_menu', 'wptools_menu');
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wptools_add_action_links22');
	function wptools_add_action_links22($links)
	{
		$mylinks = array(
			'<a href="' . admin_url('admin.php?page=wp-tools') . '">Settings</a>',
		);
		return array_merge($links, $mylinks);
	}
}
function wptools_login_logo()
{
	global $wptools_logo;
	global $wptools_logo_width;
	global $wptools_logo_height;
?>
	<style type="text/css">
		#login h1 a,
		.login h1 a {
			background-image: url('<?php echo esc_url($wptools_logo); ?>') !important;
			width: <?php echo esc_attr($wptools_logo_width); ?>px !important;
			height: <?php echo esc_attr($wptools_logo_height); ?>px !important;
			background-repeat: no-repeat;
			background-size: <?php echo esc_attr($wptools_logo_width); ?>px <?php echo esc_attr($wptools_logo_height); ?>px;
			//padding-bottom: 30px;
		}
	</style>
<?php }
if (!empty($wptools_logo) and !empty($wptools_logo_width) and !empty($wptools_logo_height)) {
	add_action('login_enqueue_scripts', 'wptools_login_logo');
}
function wptools_admin_bar_remove_logo()
{
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}
if ($wptools_remove_icon == 'yes' and is_admin())
	add_action('wp_before_admin_bar_render', 'wptools_admin_bar_remove_logo', 0);
if ($wptools_erase_readme == 'yes' and is_admin())
	wptools_remove_readme();
function wptools_remove_readme()
{
	$file = ABSPATH . 'readme.html';
	if (file_exists($file)) {
		if (!unlink($file))
			chmod($file, 755);
		if (file_exists($file)) {
			if (!unlink($file)) {
				add_action('admin_notices', 'wptools_alert_notice');
			}
		}
	}
	$file = ABSPATH . 'license.txt';
	if (file_exists($file)) {
		if (!unlink($file))
			chmod($file, 755);
		if (file_exists($file)) {
			if (!unlink($file)) {
				add_action('admin_notices', 'wptools_alert_notice');
			}
		}
	}
}
function wptools_alert_notice()
{
?>
	<div class="notice-warning">
		<p><?php esc_attr_e('Unable to remove file read.me and/or license.txt. Talk with your hosting company.', 'wptools'); ?></p>
	</div>
	<?php
}
$wptools_email_to =  trim(sanitize_text_field(get_option('wptools_email_to', '')));


$wptools_radio_email_error_notification =  trim(sanitize_text_field(get_option('wptools_radio_email_error_notification', 'no')));


function wptools_general_admin_notice($msg)
{
	if (is_admin() and !empty($msg)) {
		echo '<div class="notice notice-warning is-dismissible">
			 <p>' . esc_attr($msg) . '</p>
		     </div>';
	}
}

require_once(WPTOOLSPATH . "functions/functions.php");


if (!function_exists('ini_set')) {
	function wptools_general_admin_notice1()
	{
		if (is_admin()) {
			echo '<div class="notice notice-warning is-dismissible">
				 <p>' . esc_attr("Your server doesn\'t have a PHP function ini_set.", "wptools") . '</p>
				 <p>' . esc_attr("Please, talk with your hosting company.", "wptools") . '</p>
			 </div>';
		}
	}
	add_action('admin_notices', 'wptools_general_admin_notice');
}
if (!function_exists('ini_get')) {
	function wptools_general_admin_notice2()
	{
		if (is_admin()) {
			echo '<div class="notice notice-warning is-dismissible">
				 <p>' . esc_attr("Your server doesn\'t have a PHP function ini_get.", "wptools") . '</p>
				 <p>' . esc_attr("Please, talk with your hosting company.", "wptools") . '</p>
			 </div>';
		}
	}
	add_action('admin_notices', 'wptools_general_admin_notice');
}
// memory
$wptools_memory_limit = (int) get_option('wptools_memory_limit', '0');
if ($wptools_memory_limit > 0 and $wptools_memory_limit <= 512) {
	if ($wptools_memory_limit > wptools_get_limit())
		if (!wptools_set_limit($wptools_memory_limit)) {
			wptools_general_admin_notice('wptools: Fail to set new memory limit!');
		}
}
// time
$wptools_time_limit = get_option('wptools_time_limit', '');
if ($wptools_time_limit > 0 and $wptools_time_limit <= 360) {
	if ($wptools_memory_limit > wptools_current_time_limit())
		if (!wptools_set_time_limit($wptools_time_limit)) {
			wptools_general_admin_notice('wptools: Fail to set new time limit!');
		}
}
//Size upload
/*
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
*/
$wptools_max_filesize = (int) get_option('wptools_max_filesize', '');
$wptools_max_filesize = $wptools_max_filesize * (1024 * 1024);
if ($wptools_max_filesize > 0) {
	// and $wptools_max_filesize <= 26214400
	if ($wptools_max_filesize > wptools_current_upload_max_filesize()) {
		// var_dump(wptools_set_upload_max_filesize());
		if (!add_filter('upload_size_limit', 'wptools_set_upload_max_filesize', 20)) {
			wptools_general_admin_notice('wptools: Fail to set new upload file limit!');
		}
	}
}
/////////////////////////////////////////
if (is_admin()) {
	require_once(WPTOOLSPATH . 'includes/help/help.php');
	add_action('setup_theme', 'wptools_load_settings');
	function wptools_load_settings()
	{
		require_once(WPTOOLSPATH . "settings/load-plugin.php");
		require_once(WPTOOLSPATH . "settings/options/plugin_options_tabbed.php");
	}
	$plugin = plugin_basename(__FILE__);
	function wptools_add_action_links($links)
	{
		$mylinks = array(
			'<a href="' . admin_url('admin.php?page=settings-wptools') . '">Settings44</a>',
		);
		return array_merge($links, $mylinks);
	}
}

add_action('wp_head', 'wptools_ajaxurl');
function wptools_ajaxurl()
{
	echo '<script type="text/javascript">
	   var ajaxurl = "' . admin_url('admin-ajax.php') . '";
	 </script>';
}

if (is_admin()) {
	/*
	add_action('wp_head', 'wptools_ajaxurl');
	function wptools_ajaxurl()
	{
		echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
	}
	*/
	add_action('wp_ajax_wptools_get_ajax_data', 'wptools_get_ajax_data');
	//add_action('wp_ajax_wptools_get_js_errors', 'wptools_get_js_errors');
	add_action('wp_ajax_wptools_get_speed_info', 'wptools_get_speed_info');


	function wptools_alert_errors()
	{
		global $wp_admin_bar;

		$site = WPTOOLSHOMEURL . "admin.php?page=wptools_options21";
		$title = '<div class="wptools-alert-logo"></div><span id="wptools_alert_errors" class="text">' . esc_attr__("Site Errors", "wptools") . '</span>';

		$args = array(
			'id'    => 'wptools-alert',
			'title' => $title,
			'href'  => $site,
			'meta'  => array(
				'class' => 'wptools-alert',
				'title' => ''
			)
		);

		$wp_admin_bar->add_node($args);

		echo '<style>';
		echo '#wpadminbar #wp-admin-bar-wptools-alert .ab-item {
			color: white !important;
			width: 120px;
			background-color: black !important; /* Cor padrão */
		}';

		// Estilo para fundo vermelho se houver erros
		if (wptools_errors_today(1)) {
			echo '#wpadminbar #wp-admin-bar-wptools-alert .ab-item {
				background-color: red !important;
			}';
		}

		// Estilo para o logotipo
		$logourl = WPTOOLSIMAGES . "/bell.png";
		echo '#wpadminbar #wp-admin-bar-wptools-alert .ab-item .wptools-alert-logo {
			background-image: url("' . esc_url($logourl) . '");
			float: left;
			width: 26px;
			height: 30px;
			background-repeat: no-repeat;
			background-position: 0 6px;
			background-size: 20px;
		}';
		echo '</style>';
	}


	function wptools_alert_errors99()
	{
		global $wp_admin_bar;
		//global $wptools_radio_server_load;
		$site = WPTOOLSHOMEURL . "admin.php?page=wptools_options21";
		$args = array(
			'id' => 'wptools-alert',
			'title' => '<div class="wptools-alert-logo"></div><span id="wptools_alert_errors" class="text">' . esc_attr__("Site Errors", "wptools"),
			'href' => $site,
			'meta' => array(
				'class' => 'wptools-alert',
				'title' => ''
			)
		);





		$wp_admin_bar->add_node($args);
		echo '<style>';
		if (wptools_errors_today(1)) {
			echo '#wpadminbar .wptools-alert  {
			 background: red !important; */
				color: white !important;
				width: 120px;
				}';
		} else {
			echo '#wpadminbar .wptools-alert  {
				/* background: green !important; */
				color: white !important;
				width: 120px;
				}';
		}
		$logourl = WPTOOLSIMAGES . "/bell.png";
		echo '#wpadminbar .wptools-alert-logo  {
			background-image: url("' . esc_url($logourl) . '");
			float: left;
			width: 26px;
			height: 30px;
			background-repeat: no-repeat;
			background-position: 0 6px;
			background-size: 20px;
			}';
		echo '</style>';
	}
	function wptools_custom_toolbar_link($wp_admin_bar)
	{
		global $wp_admin_bar;
		global $wptools_radio_server_load;
		$site = WPTOOLSHOMEURL . "admin.php?page=wp-tools";
		$args = array(
			'id' => 'wptools',
			'title' => '<div class="wptools-logo"></div><span id="wptools_proc_load" class="text">wait</span>',
			'href' => $site,
			'meta' => array(
				'class' => 'wptools',
				'title' => ''
			)
		);
		$wp_admin_bar->add_node($args);
		echo '<style>';
		echo '#wpadminbar .wptools  {
			/* background: red !important; */
			color: black !important;
			width: 80px;
			}';
		$logourl = WPTOOLSIMAGES . "/processorx30x35.png";
		echo '#wpadminbar .wptools-logo  {
			background-image: url("' . esc_url($logourl) . '");
			float: left;
			width: 26px;
			height: 30px;
			background-repeat: no-repeat;
			background-position: 0 6px;
			background-size: 20px;
			}';
		echo '</style>';
	}
	/*
	function wptools_get_js_errors()
	{
		if (isset($_REQUEST)) {
			if (!isset($_REQUEST['wptools_js_error_catched']))
				die("empty error");
			if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'jquery-wptools')) {
				status_header(406, 'Invalid nonce');
				die();
			}
			$wptools_js_error_catched = sanitize_text_field($_REQUEST['wptools_js_error_catched']);
			$wptools_js_error_catched = trim($wptools_js_error_catched);
			if (!empty($wptools_js_error_catched)) {
				$txt = 'Javascript ' . $wptools_js_error_catched;
				error_log($txt);
				// send email
				wptools_add_accumulated_error($txt);
				die('OK!!!');
			}
		}
		die('NOT OK!');
	}
	*/
	function wptools_get_ajax_data()
	{
		try {
			// LOAD AVERAGES 
			if (function_exists('sys_getloadavg')) {
				$loadavg = sys_getloadavg();
				if (gettype($loadavg) !== 'array' or count($loadavg) < 3)
					wp_die();
				$load_1 = number_format($loadavg[0], 2);
				$load_5 = number_format($loadavg[1], 2);
				$load_15 = number_format($loadavg[2], 2);
			} else {
				$load_1 = 0;
				$load_5 = 0;
				$load_15 = 0;
			}
		} catch (Exception $e) {
			$load_1 = 0;
			$load_5 = 0;
			$load_15 = 0;
		}


		try {
			// NUMBER OF CORES 
			if (wptools_check_if_obd_permitted() and is_readable("/proc/cpuinfo")) {
				preg_match_all('/^processor/m', @file_get_contents('/proc/cpuinfo'), $cores);
				$cores = count($cores[0]);
			} else {

				if (wptools_exec_enabled()) {
					$cores = @shell_exec('nproc');
				} else
					$cores = 0;
			}
		} catch (Exception $e) {
			$cores = 0;
		}
		/*
		try {
			// CPU INFO
			
			
			
			if (wptools_check_if_obd_permitted() && is_readable('/proc/stat')) {
				$proc_stat = file('/proc/stat'); // Read file into array, split by lines
				$proc_stat_cpu = preg_split('/\ +/', $proc_stat[0]); // Read 1st line of file, split into array by spaces. The first line is the aggregate of all cores
				if (gettype($proc_stat_cpu) === 'array' && count($proc_stat_cpu) >= 8) {
					// Calculate total CPU time
					$proc_stat_cpu['total'] = $proc_stat_cpu[1] + $proc_stat_cpu[2] + $proc_stat_cpu[3] + $proc_stat_cpu[4] + $proc_stat_cpu[5] + $proc_stat_cpu[6] + $proc_stat_cpu[7];
					// Calculate CPU usage excluding idle time
					$proc_stat_cpu['usage'] = $proc_stat_cpu[1] + $proc_stat_cpu[2] + $proc_stat_cpu[3] + $proc_stat_cpu[5] + $proc_stat_cpu[6] + $proc_stat_cpu[7];
				} else {
					// Handle case where array keys are undefined
					$proc_stat_cpu['total'] = '';
					$proc_stat_cpu['usage'] = 0;
				}
			}
			


			$proc_stat_cpu['total'] = '';
			$proc_stat_cpu['usage'] = 0;

			if (wptools_check_if_obd_permitted() && is_readable('/proc/stat')) {
				$proc_stat = file('/proc/stat'); // Read file into array, split by lines
				
				// Verificar se o arquivo foi lido com sucesso e se a primeira linha existe
				if ($proc_stat !== false && isset($proc_stat[0])) {
					$proc_stat_cpu = preg_split('/\ +/', $proc_stat[0]); // Read 1st line of file, split into array by spaces. The first line is the aggregate of all cores
					
					// Verificar se $proc_stat_cpu é um array antes de tentar acessar seus elementos
					if (is_array($proc_stat_cpu)) {
						// Inicializar variáveis
						$total_cpu_time = 0;
						$usage_cpu_time = 0;
						$all_elements_exist = true; // Flag para verificar se todos os elementos existem
						
						// Calcular tempo total da CPU e tempo de uso excluindo o tempo ocioso
						for ($i = 1; $i <= 7; $i++) {
							if (!isset($proc_stat_cpu[$i])) {
								// Se algum elemento não existir, definir a flag como false e sair do loop
								$all_elements_exist = false;
								break;
							}
							$total_cpu_time += $proc_stat_cpu[$i];
							if ($i != 4) { // Ignorar o tempo ocioso (índice 4)
								$usage_cpu_time += $proc_stat_cpu[$i];
							}
						}
						
						// Se todos os elementos existirem, atribuir os valores calculados, caso contrário, atribuir os valores padrão
						if ($all_elements_exist) {
							$proc_stat_cpu['total'] = $total_cpu_time;
							$proc_stat_cpu['usage'] = $usage_cpu_time;
						} else {
							// Se algum elemento não existir, definir os valores padrão
							$proc_stat_cpu['total'] = '';
							$proc_stat_cpu['usage'] = 0;
						}
					}
				}
			}
		} catch (Exception $e) {
			$proc_stat_cpu['total'] = '';
			$proc_stat_cpu['usage'] = 0;
		}
		*/

		try {
			$proc_stat_cpu['total'] = '';
			$proc_stat_cpu['usage'] = 0;

			if (wptools_check_if_obd_permitted() && is_readable('/proc/stat')) {
				$proc_stat = @file('/proc/stat'); // Suprimir erros com @

				if ($proc_stat !== false && isset($proc_stat[0])) {
					$proc_stat_cpu = preg_split('/\s+/', $proc_stat[0]); // Usando \s+ para lidar com múltiplos espaços
					$total_cpu_time = 0;
					$usage_cpu_time = 0;
					$all_elements_exist = true;

					if (is_array($proc_stat_cpu)) {
						foreach ($proc_stat_cpu as $key => $value) {
							if ($key >= 1 && $key <= 7) {
								if (!isset($proc_stat_cpu[$key])) {
									$all_elements_exist = false;
									break;
								}
								$total_cpu_time += $value;
								if ($key != 4) {
									$usage_cpu_time += $value;
								}
							}
						}
						if ($all_elements_exist) {
							$proc_stat_cpu['total'] = $total_cpu_time;
							$proc_stat_cpu['usage'] = $usage_cpu_time;
						}
					}
				}
			}
		} catch (Exception $e) {
			$proc_stat_cpu['total'] = '';
			$proc_stat_cpu['usage'] = 0;
		}







		$monitor = array('load_1' => $load_1, 'load_5' => $load_5, 'load_15' => $load_15, 'cores' => $cores, 'proc_stat_cpu_total' => $proc_stat_cpu['total'], 'proc_stat_cpu_usage' => $proc_stat_cpu['usage']);
		echo wp_json_encode($monitor); //the output
		wp_die();
	}
	function wptools_add_admstylesheet()
	{
		global $wptools_radio_server_load;
		global $wptools_tab;

		wp_enqueue_script("jquery");
		if (isset($_GET['tab']))
			$wptools_tab = sanitize_text_field($_GET['tab']);
		else {
			if (bill_check_resources(false))
				$wptools_tab = 'requirements';
			else
				$wptools_tab = 'dashboard';
		}
		if (isset($_GET['page']))
			$page = sanitize_text_field($_GET['page']);
		else
			$page = '';
		if (($page == 'wptools_options31' or $page == 'wp-tools')  and $wptools_tab == 'dashboard') {
			wp_enqueue_script('wptools-smoothiejs', WPTOOLSURL . 'js/smoothie.min.js', array('jquery'), WPTOOLSVERSION, true);
			wp_enqueue_script('jquery');
			wp_enqueue_script('wptah-flot', WPTOOLSURL .
				'js/jquery.flot.min.js', array('jquery'));
			wp_enqueue_script('wptflotpie', WPTOOLSURL .
				'js/jquery.flot.pie.js', array('jquery'));

			wp_enqueue_script('wptcircle', WPTOOLSURL .
				'js/radialIndicator.js', array('jquery'));

			wp_enqueue_script('wptgauge', WPTOOLSURL .
				'js/speedometer.js', array('jquery'));

			//var_dump(WPTOOLSURL .'js/jquery-gauge.min.js');

			wp_enqueue_style('wpt-jquery-gauge-css', WPTOOLSURL .
				'css/speedometer.css');
		}

		if (isset($_GET['page']))  // {
			$page = sanitize_text_field($_GET['page']);
		else
			$page = '';
		if ($wptools_radio_server_load == 'yes' or $page == 'wptools_options31' or $page == 'wp-tools') {
			//$pos = stripos(PHP_OS_FAMILY, 'linux');
			//if ($pos !== false) {
			wp_register_script("wptools-processor", WPTOOLSURL . 'js/processor.js', array('jquery'), WPTOOLSVERSION, true);
			wp_enqueue_script('wptools-processor');
			//}
		}
		if ($wptools_radio_server_load == 'yes') {
			if (defined('PHP_OS_FAMILY'))
				$pos = stripos(PHP_OS_FAMILY, 'linux');
			else
				$pos = false;

			if ($pos !== false) {
				wp_register_script("wptools-processor", WPTOOLSURL . 'js/processor.js', array('jquery'), WPTOOLSVERSION, true);
				wp_enqueue_script('wptools-processor');
			}
		}
		if (wptools_errors_today(1)) {
			wp_register_script("wptools-alert-errors", WPTOOLSURL . 'js/alert_errors.js', array('jquery'), WPTOOLSVERSION, true);
			wp_enqueue_script('wptools-alert-errors');
		}
		wp_enqueue_script(
			'wptools-scripts-js',
			plugin_dir_url(__FILE__) . 'js/wptools_scripts.js'
		);
	}
	add_action('admin_enqueue_scripts', 'wptools_add_admstylesheet', 1000);
	if ($wptools_radio_server_load == 'yes')
		add_action('admin_bar_menu', 'wptools_custom_toolbar_link', 999);
	if ($wptools_show_errors != 'no') {
		if (wptools_errors_today(365))
			add_action('admin_bar_menu', 'wptools_alert_errors', 999);
	}
}
$wptools_disable_sitemap =  trim(sanitize_text_field(get_option('wptools_disable_sitemap', 'no')));
$wptools_disable_sitemap = strtolower($wptools_disable_sitemap);
$wptools_disable_updates_notifications =  trim(sanitize_text_field(get_option('wptools_disable_updates_notifications', 'no')));
$wptools_disable_updates_notifications = strtolower($wptools_disable_updates_notifications);

$wptools_add_google_webmaster =  trim(get_option('wptools_add_google_webmaster', ''));

$wptools_add_bing_webmaster_metaname =  sanitize_text_field(trim(get_option('wptools_add_bing_webmaster_metaname', '')));

$wptools_add_bing_webmaster_content =  sanitize_text_field(trim(get_option('wptools_add_bing_webmaster_content', '')));


$wptools_add_analitics =  trim(sanitize_text_field(get_option('wptools_add_analitics', '')));
$wptools_alert_debug =  trim(sanitize_text_field(get_option('wptools_alert_debug', 'no')));
$wptools_alert_debug = strtolower($wptools_alert_debug);
$wptools_hide_admin_bar =  trim(sanitize_text_field(get_option('wptools_hide_admin_bar', 'no')));
$wptools_hide_admin_bar = strtolower($wptools_hide_admin_bar);
if ($wptools_disable_updates_notifications == 'yes') {
	add_filter('auto_core_update_send_email', 'wptools_stop_auto_update_emails', 10, 4);
	add_filter('auto_plugin_update_send_email', '__return_false');
	add_filter('auto_theme_update_send_email', '__return_false');
}
if ($wptools_disable_lazy == 'yes')
	add_filter('wp_lazy_loading_enabled', '__return_false');
if ($wptools_classic_widget == 'yes') {
	// Disables the block editor from managing widgets in the Gutenberg plugin.
	add_filter('gutenberg_use_widgets_block_editor', '__return_false');
	// Disables the block editor from managing widgets.
	add_filter('use_widgets_block_editor', '__return_false');
}
function wptools_stop_auto_update_emails($send, $type, $core_update, $result)
{
	if (!empty($type) && $type == 'success') {
		return false;
	}
	return true;
}


function wptools_load_chat()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			if (! class_exists('wptools_BillChat\ChatPlugin')) {
				require_once dirname(__FILE__) . "/includes/chat/class_bill_chat.php";
			}
		}
	}
}
add_action('wp_loaded', 'wptools_load_chat');



//disable sitemap
function wptools_disable_sitemap_main()
{
	add_filter('wp_sitemaps_enabled', '__return_false');
}
if ($wptools_disable_sitemap == 'yes')
	add_action('init', 'wptools_disable_sitemap_main');
if ($wptools_disable_sitemap == 'users') {
	add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
		return ($name == 'users') ? false : $provider;
	}, 10, 2);
}
function wptools_webmaster_tools()
{
	global $wptools_add_google_webmaster;
	global $wptools_add_bing_webmaster_metaname;
	global $wptools_add_bing_webmaster_content;

	if (!empty($wptools_add_google_webmaster)) {
		$wptools_add_google_webmaster =  str_replace('"', '', $wptools_add_google_webmaster);
		echo '<meta name="google-site-verification" content="' . esc_attr($wptools_add_google_webmaster) . '" />';
	}
	if (!empty($wptools_add_bing_webmaster_metaname))
		echo '<meta name="' . esc_attr($wptools_add_bing_webmaster_metaname) . '" content="' . esc_attr($wptools_add_bing_webmaster_content) . '"  />';
}

if (!is_admin()) {
	add_action('wp_head', 'wptools_webmaster_tools');
}


function wptools_add_analytics()
{

	global $wptools_add_analitics;
	// Global Site Tag / gtag.js (new method)
	// https://developers.google.com/analytics/devguides/collection/gtagjs/Ffile
	if (substr($wptools_add_analitics, 0, 2) == 'G-') {
		echo "<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr($wptools_add_analitics) . "\"></script>
	

	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '" . esc_attr($wptools_add_analitics) . "');
	</script>" . PHP_EOL;
	} else {
		echo "<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		ga('create', '" . esc_attr($wptools_add_analitics) . "', 'auto');
		ga('send', 'pageview');
	</script>" . PHP_EOL;
	}
}
if (!empty($wptools_add_analitics))
	add_action('wp_footer', 'wptools_add_analytics');

if ($wptools_alert_debug == 'yes') {
	// define( 'WP_DEBUG', true );
	if (defined('WP_DEBUG')) {
		if (WP_DEBUG) {
			add_action('admin_bar_menu', 'wptools_custom_toolbar_debug', 999);
		}
	}
}
function wptools_custom_toolbar_debug($wp_admin_bar)
{

	global $wp_admin_bar;
	$site = WPTOOLSHOMEURL . "admin.php?page=settings-wptools&tab=general_settings";
	$args = array(
		'id' => 'wptools_debug_active',
		'title' => '<span id="wptools_debug_active" class="text">WP Debug Active</span>',
		'href' => $site,
		'meta' => array(
			'class' => 'wptools_debug_active',
			'title' => ''
		)
	);
	$wp_admin_bar->add_node($args);
	echo '<style>';
	echo '#wpadminbar .wptools_debug_active  {
		background: red !important; 
		color: black !important;
		width: 119px;
		}';
	echo '</style>';
}
if ($wptools_disable_emojis == 'yes')
	add_action('init', 'wptools_emojis_disable');
function wptools_emojis_disable()
{
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'wptools_emojis_disable_tinymce');
	add_filter('wp_resource_hints', 'wptools_emojis_disable_remove_dns_prefetch', 10, 2);
}
function wptools_emojis_disable_tinymce($plugins)
{
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	}
	return array();
}
function wptools_emojis_disable_remove_dns_prefetch($urls, $relation_type)
{
	if ('dns-prefetch' == $relation_type) {
		$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
		foreach ($urls as $key => $url) {
			if (strpos($url, $emoji_svg_url_bit) !== false) {
				unset($urls[$key]);
			}
		}
	}
	return $urls;
}
//
function wptools_mysql_queries()
{
	// echo '<!-- ';
	$r = esc_attr__('Page Load Info: ', 'wptools');
	$r .= ' ' . get_num_queries();
	$r .= ' ' . esc_attr__('queries and loaded in', 'wptools');
	$r .= ' ' . timer_stop(0, 1);
	$r .= ' ' . esc_attr__('seconds', 'wptools');
	// echo ' -->';
	return $r;
}
if (is_admin() and $wptools_show_pageload_info == 'yes')
	add_filter('admin_footer_text', 'wptools_mysql_queries');



// ====================  begin error ... 
// debug
// add_filter( 'automatic_updates_send_debug_email', '__return_false' );
// wptools_send_accumulated_errors_email();
// $old_error_handler = set_error_handler("wptoolsErrorHandler");

function wptools_send_error_email($errors)
{
	global $wptools_email_to;
	// Prepare email content
	$dt = date("Y-m-d H:i:s");
	if (function_exists('get_site_url')) {
		$site_url = get_site_url();
		if (!is_null($site_url)) {
			$parsed_url = parse_url($site_url);
			$dom = $parsed_url['host'];
		} else {
			$dom = esc_attr__('Site Name unavailable', "wptools");
		}
	} else {
		$dom = esc_attr__('Site Name unavailable', "wptools");
	}
	$send_by = 'wordpress@' . $dom;
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: WordPress <' . $send_by . '>'
	);
	$subject = esc_attr__('Notification about errors and warnings at: ', "wptools") . ' ' . $dom;
	$msg = esc_attr__("This email was sent from your website", "wptools") . ": " . $dom . "\n";
	$msg .= esc_attr__("by the wpTools plugin.", "wptools") . "\n";
	$msg .= esc_attr__("Date", "wptools") . ": " . $dt . "\n\n";
	$msg .= esc_attr__("Errors and Warnings:", "wptools") . "\n";
	// Append accumulated errors to the email message
	foreach ($errors as $error) {
		$msg .= $error . "\n";
	}
	$msg .= "\n\n";
	$msg .= esc_attr__("You can stop emails at the Notifications Tab.", "wptools") . "\n";
	$msg .= esc_attr__("Dashboard => WP tools=> Settings", "wptools");
	$email_from = "wordpress@" . $dom;
	// Send the 

	// $success = wp_mail($wptools_email_to, $subject, $msg, $headers);


	try {
		$success = @wp_mail($wptools_email_to, $subject, $msg, $headers);
	} catch (Exception $e) {
		error_log('Fail to scheddulle event L831: ' . $e->getMessage());
		$success = false;
	}


	// $success = true;
	if (!$success) {
		error_log('Failed to send email wptools_email');
	} else {
		// error_log1('Send email wptools_email successful');
	}
}

// call with cron  each 15 minutes...
function wptools_send_accumulated_errors_email()
{
	$current_time = time();
	$last_email_sent = get_transient('wptools_last_email_sent');
	$accumulated_errors = get_transient('wptools_accumulated_errors');
	if ($last_email_sent === false || ($current_time - $last_email_sent) >= (60 * 15) || $last_email_sent === '') {

		set_transient('wptools_last_email_sent', $current_time, 60 * 15);
		// Check if there are accumulated errors
		$accumulated_errors = get_transient('wptools_accumulated_errors');
		if ($accumulated_errors) {
			// Send...
			wptools_send_error_email($accumulated_errors);
			// Reset accumulated errors and transient
			delete_transient('wptools_accumulated_errors');
		}
	}
}

function wptools_add_accumulated_error($error)
{
	// Get existing errors from transient
	$accumulated_errors = get_transient('wptools_accumulated_errors');
	if (!$accumulated_errors) {
		$accumulated_errors = array();
	}
	$timestamp = date('Y-m-d H:i:s');
	$accumulated_errors[] = $timestamp . ' - ' . $error;
	// Set updated transient
	$r = set_transient('wptools_accumulated_errors', $accumulated_errors, (25 * 60));
	$accumulated_errors = get_transient('wptools_accumulated_errors');
}

if ($wptools_radio_email_error_notification == 'yes' or $wptools_bypass_wpdebug == 'yes') {
	add_action('shutdown', 'wptoolsErrorCatch');
}

function wptoolsErrorCatch()
{
	$error = error_get_last();

	if ($error !== null) {
		$errno = $error['type'];
		$errstr = $error['message'];
		$errfile = $error['file'];
		$errline = $error['line'];
		wptoolsErrorHandler($errno, $errstr, $errfile, $errline);
	}
}


function wptoolsErrorHandler($errno, $errstr, $errfile, $errline)
{
	// Requires customized class on class_bill_catch_errors.php
	$errstr = htmlspecialchars($errstr);
	$txt = '';
	switch ($errno) {
		case E_USER_ERROR:
			$txt .= "ERROR [$errno] $errstr\n";
			$txt .= "  Fatal error on line $errline in file $errfile";
			break; // Ensure to break after each case
		case E_USER_WARNING:
			$txt .= "WARNING [$errno] $errstr\n";
			$txt .= "  Warning on line $errline in file $errfile";
			break;
		case E_USER_NOTICE:
			$txt .= "NOTICE [$errno] $errstr\n";
			$txt .= "  Notice on line $errline in file $errfile";
			break;
		case E_NOTICE:
			$txt .= "NOTICE [$errno] $errstr\n";
			$txt .= "  Notice on line $errline in file $errfile";
			break;
		case 'Javascript':
			$txt .= "JAVASCRIPT $errstr\n";
			$txt .= "  Error on line $errline in file $errfile";
			break;
		default:
			$txt .= "Unknown error type: [$errno] $errstr\n";
			$txt .= "  On line $errline in file $errfile";
			break;
	}

	if (function_exists('wptools_add_accumulated_error')) {
		wptools_add_accumulated_error($txt);


		/*
		//	$errno, $errstr, $errfile, $errline)

		$logFile = trailingslashit(ABSPATH) . 'error_log'; // trailingslashit adiciona uma barra no final do caminho, se necessário
		// $logFile = trailingslashit(WP_CONTENT_DIR) . 'error_log'; // A função trailingslashit garante que haverá uma barra no final do caminho
		//

		// precisa tratar ese javascript...
		$logMessage = "Javascript " . $errstr . " - " . $errfile . " - " . $errline;

		$date_format = get_option('date_format');

		if ($date_format) {
			$formattedMessage = "[" . date_i18n($date_format) . ' ' . date('H:i:s') . "] - " . $logMessage . PHP_EOL;
		} else {
			$formattedMessage = "[" . date('M-d-Y H:i:s') . "] - " . $logMessage . PHP_EOL;
		}

		$logFile = trailingslashit(ABSPATH) . 'error_log'; // trailingslashit adiciona uma barra no final do caminho, se necessário

		$r = file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);

		*/


		/*
			when using set error handler
			if ($errno != E_USER_ERROR) {
				wptools_add_accumulated_error($txt);
			}
			else{
				if (defined('WP_DISABLE_FATAL_ERROR_HANDLER') && WP_DISABLE_FATAL_ERROR_HANDLER === true)
				{
					wptools_add_accumulated_error($txt);
				}
			}
			*/
	}

	/*
	when using set error handler
	In PHP 7 and above, exceptions are used for error handling by default.
	Even if your custom handler returns true, exceptions might still be logged internally.
	always will log
		Display
		false: php will show by internal engine
		true: You need show with your custom.
	
	if (defined('WP_DEBUG') && WP_DEBUG === true) {
		return false; // show - the normal error handler continues 
	} else {
	    // error_log($txt);
		return true;  // hide - might still be logged internally 
	}
	*/
}

// ============ end error...



function wptools_add_stylesheet()
{
	wp_enqueue_style('admin-css', WPTOOLSURL .
		'css/admin.css');
	wp_enqueue_style('help-css', WPTOOLSURL .
		'dashboard/css/help.css');

	wp_enqueue_style('admin-settings-css', WPTOOLSURL .
		'settings/styles/admin-settings.css');
}

if (is_admin())
	add_action('wp_loaded', 'wptools_add_stylesheet', 1000);
if (!is_admin() and $wptools_show_adminbar == 'yes') {
	// Yes == Disable...
	//var_dump($wptools_show_adminbar);
	function wptools_disable_admin_toolbar()
	{
		$wptools_custom_css = '
				.show-admin-bar {
					display: none;
				}';
		wp_add_inline_style('wptools-css2', $wptools_custom_css);
	}
	add_filter('show_admin_bar', '__return_false', 999);
	add_action('wp_enqueue_scripts', 'wptools_disable_admin_toolbar');
}




if (is_admin())
	add_action('admin_menu', 'wptools_change_menu_label', 99);

function wptools_change_menu_label()
{
	global $menu;
	global $submenu;
	$submenu['wp-tools'][0][0] = esc_attr__('Dashboard', "wptools");
}
add_action('wp_head', function () {
	if (!current_user_can('administrator')) {
	?>
		<script type="text/javascript">
			console.log = function() {};
			console.log('ok2');
		</script>
	<?php
	}
}, 1);
function wptools_plugin_row_meta($links, $file)
{
	global $wptools_checkversion;
	if (strpos($file, 'wptools.php') !== false) {
		if (is_multisite())
			$url = WPTOOLSHOMEURL . "plugin-install.php?s=sminozzi&tab=search&type=author";
		else {
			if (empty($wptools_checkversion))
				$url = "https://wptoolsplugin.com/premium/";
			else
				$url = WPTOOLSHOMEURL . "admin.php?page=wptools_options39";
		}
		if (empty($wptools_checkversion))
			$new_links['Pro'] = '<a href="' . $url . '" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>';
		else
			$new_links['Pro'] = '<a href="' . $url . '" target="_blank"><b><font color="#FF6600">Click To see more plugins from same author</font></b></a>';
		$links = array_merge($links, $new_links);
	}
	return $links;
}

add_filter('plugin_row_meta', 'wptools_plugin_row_meta', 10, 2);

if (is_admin()) {

	// Pointer... 
	function wptools_load_pointer()
	{
		$pointers = get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true);
		$pointers = ''; // str_replace( 'plugins', '', $pointers );
		update_user_meta(get_current_user_id(), 'dismissed_wp_pointers', $pointers);
	}


	function wptools_load_pointer_css()
	{
		wp_enqueue_style('wpt-pointer', WPTOOLSURL . 'css/bill-wp-pointer.css');
	}





	$wptools_activated_pointer =  trim(sanitize_text_field(get_option('wptools_activated_pointer', '0')));


	if ($wptools_activated_pointer == '1') {
		$BILLCLASS = 'ACTIVATED_WPTOOLS';

		if (isset($_COOKIE[$BILLCLASS])) {
			$bill_tempo_criacao = sanitize_text_field($_COOKIE[$BILLCLASS]);

			$bill_intervalo = time() - $bill_tempo_criacao;
			// wait 30 sec...
			if ($bill_intervalo > 10) {
				//add_action( 'wp_enqueue_scripts', 'wptools_load_pointer_css' );
				add_action('admin_enqueue_scripts', 'wptools_adm_enqueue_scripts_pointer');
				wptools_load_pointer();
				add_action('wp_loaded', 'wptools_load_pointer_css');

				$r = update_option('wptools_activated_pointer', '0');
				if (!$r) {
					add_option('wptools_activated_pointer', '0');
				}
			}
		}
	}
	// end pointer


	// Activation...
	register_activation_hook(__FILE__, 'wptools_activated');



	// >>>>>>>>>>>>>>>>>>>>>>
	$BILLCLASS = 'ACTIVATED_WPTOOLS';
	if (!isset($_COOKIE[$BILLCLASS]) and $wptools_activated_notice == '1') {

		if (! is_multisite()) {
			add_action('wp_loaded', 'wptools_load_feedback3');
			add_action('admin_notices', 'wptools_include_file_more_plugins');
		}
	} else {

		add_option("wptools_activated_notice", "0");
		update_option("wptools_activated_notice", "0");
	}

	// 2023
	function wptools_load_feedback3()
	{
		global $wptools_request_url;
		if (strpos($wptools_request_url, 'plugins.php') !== false) {
			wp_register_style('bill-feedback-wptools-css', WPTOOLSURL . 'includes/feedback/feedback-plugin.css');
			wp_enqueue_style('bill-feedback-wptools-css');

			wp_register_script('bill-feedback-wptools-js', WPTOOLSURL . 'includes/feedback/activated-manager.js', array('jquery'), WPTOOLSVERSION, true);
			wp_enqueue_script('bill-feedback-wptools-js');
		}
	}

	function wptools_include_file_more_plugins()
	{
		global $wptools_request_url;
		if (strpos($wptools_request_url, 'plugins.php') !== false)
			require_once(WPTOOLSPATH . 'includes/feedback/activated-manager.php');
	}

	function wptools_activated()
	{
		/*
		$r = update_option('wptools_was_activated', '1');
		if (!$r) {
			add_option('wptools_was_activated', '1');
		}
		*/

		$r = update_option('wptools_activated_notice', '1');
		if (!$r) {
			add_option('wptools_activated_notice', '1');
		}
		$r = update_option('wptools_activated_pointer', '1');
		if (!$r) {
			add_option('wptools_activated_pointer', '1');
		}
	}
	// end activation
}



function wptools_dismissible_notice()
{
	$r = update_option('wptools_dismiss', false);
	if (!$r) {
		$r = add_option('wptools_dismiss', false);
	}
	wp_die($r);
}
add_action('wp_ajax_wptools_dismissible_notice', 'wptools_dismissible_notice');

if (get_option('wptools_dismiss', true) and is_admin())

	add_action('admin_notices', 'wptools_dismiss_admin_notice');
function wptools_dismiss_admin_notice()
{
	if (!bill_check_resources(false))
		return;
	?>
	<div id="wptools_an1" class="notice-warning notice is-dismissible">
		<p>
			Please, look the WP Tools plugin Dashboard (Server Check & Requirements Tab)&nbsp;
			<a class="button button-primary" href="admin.php?page=wptools_options31&tab=requirements">or click here</a>
		</p>
	</div>
<?php
	//endif;
}
if ($wptools_disable_selfping == 'yes') {
	$home = esc_url(home_url());
	add_action('pre_ping', function (&$post_links, &$pung, int $post_ID) {
		foreach ($post_links as $key => $link) {
			if (0 === strpos($link, $home)) {
				unset($post_links[$key]);
			}
		}
	}, 10, 3);
}
add_action('wp_ajax_wptools_bill_go_pro_hide', 'wptools_bill_go_pro_hide');
function wptools_load_feedback()
{
	if (is_admin() or is_super_admin()) {
		if (file_exists(WPTOOLSPATH . 'includes/feedback-last/feedback-last.php'))
			require_once WPTOOLSPATH . "includes/feedback-last/feedback-last.php";
		//debug2();
	}
}
add_action('wp_loaded', 'wptools_load_feedback');

//
//
/*
function wptools_localization_init()
{
	$path = basename(dirname(__FILE__)) . '/language';
	$loaded = load_plugin_textdomain('wptools', false, $path);
	if (!$loaded and get_locale() <> 'en_US') {
		if (function_exists('wptools_localization_init_fail'))
			add_action('admin_notices', 'wptools_localization_init_fail');
	}
}
*/

function wptools_localization_init()
{
	$path = WPTOOLSPATH . 'language/';
	$locale = apply_filters('plugin_locale', determine_locale(), 'wptools');

	// Full path of the specific translation file (e.g., es_AR.mo)
	$specific_translation_path = $path . "wptools-$locale.mo";
	$specific_translation_loaded = false;

	// Check if the specific translation file exists and try to load it
	if (file_exists($specific_translation_path)) {
		$specific_translation_loaded = load_textdomain('wptools', $specific_translation_path);
	}

	// List of languages that should have a fallback to a specific locale
	$fallback_locales = [
		'de' => 'de_DE',  // German
		'fr' => 'fr_FR',  // French
		'it' => 'it_IT',  // Italian
		'es' => 'es_ES',  // Spanish
		'pt' => 'pt_BR',  // Portuguese (fallback to Brazil)
		'nl' => 'nl_NL'   // Dutch (fallback to Netherlands)
	];

	// If the specific translation was not loaded, try to fallback to the generic version
	if (!$specific_translation_loaded) {
		$language = explode('_', $locale)[0];  // Get only the language code, ignoring the country (e.g., es from es_AR)

		if (array_key_exists($language, $fallback_locales)) {
			// Full path of the generic fallback translation file (e.g., es_ES.mo)
			$fallback_translation_path = $path . "wptools-{$fallback_locales[$language]}.mo";

			// Check if the fallback generic file exists and try to load it
			if (file_exists($fallback_translation_path)) {
				load_textdomain('wptools', $fallback_translation_path);
			}
		}
	}

	// Load the plugin
	load_plugin_textdomain('wptools', false, plugin_basename(WPTOOLSPATH) . '/language/');
}






function wptools_localization_init_fail()
{
	if (get_option('wptools_dismiss_language') == '1')
		return;

	echo '<div id="wptools_an2" class="update notice is-dismissible">
                     <br />
                     WP Tools Plugin not load the localization file (Language file).
                     <br />
                     Please, contact me at our Support Page to translate it on your language.
					 <br />
					 <br />
					 </div>';
}

function wptools_dismissible_notice2()
{
	$r = update_option('wptools_dismiss_language', '1');
	if (!$r) {
		$r = add_option('wptools_dismiss_language', '1');
	}
}
add_action('wp_ajax_wptools_dismissible_notice2', 'wptools_dismissible_notice2');

if (function_exists('wptools_errors_today')) {
	if (wptools_errors_today(1))
		add_filter('site_status_tests', 'wptools_error_test');
}

function wptools_add_error_test()
{
	$result = array(
		'label'       => esc_attr__('WP Tools found errors on your site'),
		'status'      => 'critical',
		'badge'       => array(
			'label' => esc_attr__('Critical', 'wptools'),
			'color' => 'red',
		),
		'description' => sprintf(
			'<p>%s</p>',
			esc_attr__('Code and hosting errors can destroy your site and the user experience.', 'wptools')
		),
		'test'        => 'wptools_plugin',
	);
	$result['actions'] = sprintf(
		'<p><a href="%s">%s</a></p>',
		esc_url(admin_url('admin.php?page=wptools_options21')),
		esc_attr__('Visit the WP Tools show errors page', 'wptools')
	);

	return $result;
}


function wptools_check_if_obd_permitted()
{
	$obd = ini_get('open_basedir');

	if (!$obd) return true;

	if (empty($obd))
		return true;
	else
		return false;

	// if (substr(realpath($file), 0, strlen($obd))==$obd) return true;
}

function wptools_exec_enabled()
{
	if (!function_exists('shell_exec'))
		return false;
	$disabled = explode(',', ini_get('disable_functions'));
	return !in_array('exec', $disabled);
}

if (isset($_GET['wptools_get_speed_info'])) {
	wptools_get_speed_info();
}


function wptools_get_speed_info()
{
	require_once WPTOOLSPATH . "functions/functions_benchmark.php";
	$arr_cfg = array();
	$arr_cfg['db.host'] = DB_HOST;
	$arr_cfg['db.user'] = DB_USER;
	$arr_cfg['db.pw'] = DB_PASSWORD;
	$arr_cfg['db.name'] = DB_NAME;
	$showServerName = true;
	// wptools_performance_share($benchmarkResult);
	$server_performance = wptools_test_benchmark($arr_cfg);
	$benchmarkResult = $server_performance;
	$server_performance = $server_performance['total'];

	$r = get_transient('wptools_performance_share');



	if (!$r) {
		wptools_performance_share($benchmarkResult);
		$r = get_transient('wptools_performance_share');
	}
	$r = json_decode($r, true);
	$benchmarkIndustryResult = wptools_industry_benchmark($r);
	$industry_performance = $benchmarkIndustryResult['total'];


	if ($industry_performance < 1)
		$industry_performance = 1;
	if ($server_performance >= $industry_performance)
		wp_die(round(($industry_performance / $server_performance) * 100));
	else
		wp_die('100');

	return;
}

function wptools_send_error_summary_email()
{
	global $wptools_email_to;
	global $wpdb;
	global $wptools_radio_email_weekly_error_notification;


	if ($wptools_radio_email_weekly_error_notification != 'yes')
		return;

	// Check if currently processing error summary
	if (get_transient('wptools_doing_summary_email')) {
		// Already processing summary, return without doing anything
		return;
	}


	// Start error summary processing
	set_transient('wptools_doing_summary_email', 1, 60); // Lock for 1 minute to prevent multiple emails



	$wptools_last_email_sent = get_option('wptools_last_email_sent_issues');
	if ($wptools_last_email_sent > time() - (5 * 24 * 60 * 60)) {
		return;
	}

	$wptools_table_name = $wpdb->prefix . 'wptools_errors';
	if ($wpdb->get_var("SHOW TABLES LIKE '$wptools_table_name'") !== $wptools_table_name)
		return;
	$seven_days_ago = date('Y-m-d', strtotime('-7 days'));
	$query = $wpdb->prepare(
		"SELECT COUNT(*) AS qtotal
	  FROM $wptools_table_name
	  WHERE error_date >= %s",
		$seven_days_ago
	);
	$error_count = $wpdb->get_var($query);
	if (!is_numeric($error_count) or $error_count < 1)
		return;
	$dt = date("Y-m-d H:i:s");
	if (function_exists('get_site_url')) {
		$site_url = get_site_url(); // Retorna o URL completo do site, como "https://example.com"
		if (function_exists('get_site_url')) {
			$site_url = get_site_url();
			if (!is_null($site_url)) {
				$parsed_url = parse_url($site_url);
				$dom = $parsed_url['host'];
			} else {
				$dom = esc_attr__('Site Name unavailable', "wptools");
			}
		} else {
			$dom = esc_attr__('Site Name unavailable', "wptools");
		}
	} else {
		$dom = esc_attr__('Site Name unavailable', "wptools");
	}
	$send_by = 'wordpress@' . $dom;
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: WordPress <' . $send_by . '>'
	);
	if (empty($wptools_email_to))
		$wptools_email_to = sanitize_email(get_option('admin_email', ''));
	$message = esc_attr__('Hello', "wptools") . "\n\n";
	$email_from = 'wordpress@' . $dom;
	$subject = esc_attr__('Weekly summary of issues on the site: ', "wptools") . ' ' . $dom;
	$message .= esc_attr__('This email was sent from your website', 'wptools') . ': ' . $dom .  "\n\n";
	$message .= esc_attr__('This is a weekly summary of errors, warnings, or notices that occurred on your site assisted by the wpTools plugin.', "wptools") . "\n\n";
	$message .= esc_attr__('Total number of issues', "wptools") . ': ' . $error_count . "\n\n";
	$message .= esc_attr__('Please visit your site for more details:', "wptools") . "\n\n";
	$message .= esc_attr__('Dashboard => wpTools => Dashboard', "wptools") . "\n\n";
	$message .= esc_attr__('You can stop weekly emails at the Notifications Tab:', "wptools") . "\n";
	$message .= esc_attr__('Dashboard => WP tools=> Settings', "wptools") . "\n";

	// $success = wp_mail($wptools_email_to, $subject, $message, $headers);


	try {
		$success = @wp_mail($wptools_email_to, $subject, $message, $headers);
	} catch (Exception $e) {
		error_log('Fail to scheddulle event L831: ' . $e->getMessage());
		$success = false;
	}

	if (!$success) {
		error_log('Failed to send email wptools_email');
		return;
	}
	update_option('wptools_last_email_sent_issues', time());
	// End error summary processing
	// not necessary... delete_transient('wptools_doing_summary_email');
	return;
}


add_filter('manage_upload_columns', 'wptools_add_column_file_size');
add_action('manage_media_custom_column', 'wptools_column_file_size', 10, 2);

function wptools_add_column_file_size($columns)
{ // Create the column
	$columns['filesize'] = 'File Size';
	return $columns;
}
function wptools_column_file_size($column_name, $media_item)
{ // Display the file size
	if ('filesize' != $column_name || !wp_attachment_is_image($media_item)) {
		return;
	}
	$filesize = filesize(get_attached_file($media_item));
	$filesize = size_format($filesize, 2);
	echo esc_attr($filesize);
}
require_once WPTOOLSPATH . 'functions/function_time_loading.php';




function wptools_send_load_summary_email()
{
	global $wptools_email_to;
	global $wpdb;
	global $wptools_radio_email_weekly_load_notification;

	if ($wptools_radio_email_weekly_load_notification != 'yes')
		return;

	// Check if currently processing load summary
	if (get_transient('wptools_doing_load_summary_email')) {
		// Already processing summary, return without doing anything
		return;
	}

	// Start load summary processing
	set_transient('wptools_doing_load_summary_email', 1, 60); // Lock for 1 minute to prevent multiple emails

	$wptools_last_email_sent = get_option('wptools_last_load_issues');
	if ($wptools_last_email_sent > time() - (4 * 24 * 60 * 60)) {
		return;
	}
	$wptools_table_name = $wpdb->prefix . 'wptools_page_load_times';
	if ($wpdb->get_var("SHOW TABLES LIKE '$wptools_table_name'") !== $wptools_table_name)
		return;
	//$seven_days_ago = date('Y-m-d', strtotime('-7 days'));
	$query = $wpdb->prepare(
		"SELECT AVG(load_time) AS average_load_time
		FROM $wptools_table_name
		WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
		AND page_url NOT LIKE %s",
		'wp-admin'
	);
	$page_load = $wpdb->get_var($query);
	//if (!is_numeric($page_load) or $page_load < 3.1) // 3.1
	//   return;
	//if (!is_numeric($page_load) or $page_load > 30) 
	//   return;
	$page_load_rounded = round($page_load, 1);
	if ($page_load_rounded > 30 or $page_load_rounded < 3)
		return;
	if (empty($wptools_email_to))
		$wptools_email_to = sanitize_email(get_option('admin_email', ''));
	$dt = date("Y-m-d H:i:s");
	if (function_exists('get_site_url')) {
		$site_url = get_site_url();
		if (!is_null($site_url)) {
			$parsed_url = parse_url($site_url);
			$dom = $parsed_url['host'];
		} else {
			$dom = esc_attr__('Site Name unavailable', "wptools");
		}
	} else {
		$dom = esc_attr__('Site Name unavailable', "wptools");
	}
	$send_by = 'wordpress@' . $dom;
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: WordPress <' . $send_by . '>'
	);
	if (empty($wptools_email_to))
		$wptools_email_to = sanitize_email(get_option('admin_email', ''));
	$subject = esc_attr__('Weekly summary of Page Load on the site: ', "wptools") . ' ' . $dom;
	$message = esc_attr__('Hello,', 'wptools') . "\n\n";
	$message .= esc_attr__('This is a weekly summary of Page Load seconds that occurred on your site assisted by the wpTools plugin:', 'wptools') . "\n";
	$message .= esc_attr__('Weekly Page Load Time:', 'wptools') . ' ' . $page_load_rounded . "\n";
	$message .= esc_attr__('Google recommends that the page loading time be less than 3 seconds. A faster-loading website is more likely to rank higher, potentially attracting more visitors.', 'wptools') . "\n";
	$message .= esc_attr__('Please visit your site for more details:', 'wptools') . "\n";
	$message .= esc_attr__('Dashboard => wpTools => Dashboard', 'wptools') . "\n\n";
	$msg = esc_attr__('This email was sent from your website', 'wptools') . ': ' . $dom . "\n";
	$msg .= esc_attr__('by the wpTools plugin.', 'wptools') . "\n";
	$msg .= esc_attr__('Date:', 'wptools') . ' ' . $dt . "\n\n";
	$msg .= $message . "\n\n";
	$msg .= esc_attr__('You can stop weekly emails at the Notifications Tab:', 'wptools') . "\n";
	$msg .= esc_attr__('Dashboard => WP tools => Settings', 'wptools');
	// $success = wp_mail($wptools_email_to, $subject, $msg, $headers);


	try {
		$success = @wp_mail($wptools_email_to, $subject, $msg, $headers);
	} catch (Exception $e) {
		error_log('Fail to scheddulle event L831: ' . $e->getMessage());
		$success = false;
	}


	if (!$success) {
		error_log('Failed to send email wptools_email');
		return;
	}
	update_option('wptools_last_load_issues', time());
	return;
}



if ($wptools_disable_ziparchive == 'yes' and function_exists('_unzip_file_pclzip')) {
	add_filter('unzip_file_use_ziparchive', '__return_false');
}


// catch js errors...

function wptools_bill_hooking_catch_errors()
{
	$declared_classes = get_declared_classes();
	foreach ($declared_classes as $class_name) {
		if (strpos($class_name, "bill_catch_errors") !== false) {
			return;
		}
	}
	$plugin_slug = 'wptools';
	require_once dirname(__FILE__) . "/includes/catch-errors/class_bill_catch_errors.php";
}
add_action("init", "wptools_bill_hooking_catch_errors", 15);


//     >>>>> Custom Cron Job
function wptools_cron_customized()
{

	$transient_value = get_transient('wptools_doing_cron');
	if ($transient_value !== false && (time() - $transient_value) < 60) {
		return;
	} else {
		delete_transient('wptools_doing_cron');
		set_transient('wptools_doing_cron', time(), 60);
	}

	wptools_send_accumulated_errors_email();
	$current_time = time();

	// hourly
	/*
		$option_name = 'wptools_cron_issues_event';
		$interval = "hourly";
		$str_timestamp = date('Y-m-d H:i:s', strtotime('+1 hour'));
		$next_scheduled_time = wptools_wp_next_scheduled($option_name);
		$next_scheduled_time = strtotime($next_scheduled_time);
		if ($next_scheduled_time && $current_time >= $next_scheduled_time) {
			// wptools_log_to_error_log();
			// Make a call  AJAX:
			wp_remote_post( admin_url( 'admin-ajax.php' ), array(
				'body' => array(
					'action' => 'wptools_log_to_error_log_ajax'
				)
			) );
			update_option(  $option_name , $str_timestamp );
			wptools_wp_schedule_event( $interval, $option_name );
		} 
		*/

	/*
		// 15 minutes...
		$option_name = 'wptools_cron_send_accumulated errors';
		$interval = "15_minutes";
		$str_timestamp = date('Y-m-d H:i:s', strtotime('+15 minutes'));

		$next_scheduled_time = wptools_wp_next_scheduled($option_name);
		$next_scheduled_time = strtotime($next_scheduled_time);
		if ($next_scheduled_time && $current_time >= $next_scheduled_time) {
			// wptools_log_to_error_log();
            wptools_send_accumulated_errors_email();
			
			update_option(  $option_name , $str_timestamp );
			wptools_wp_schedule_event( $interval, $option_name );
		} 
		*/


	// +1 week'
	$option_name = 'wptools_cron_issues_event';
	$interval = "weekly";
	$str_timestamp = date('Y-m-d H:i:s', strtotime('+1 ' . $interval));
	$next_scheduled_time = wptools_wp_next_scheduled($option_name);
	$next_scheduled_time = strtotime($next_scheduled_time);

	if (empty($next_scheduled_time)) {
		wptools_wp_schedule_event($interval, $option_name);
		return;
	}

	if ($next_scheduled_time && $current_time >= $next_scheduled_time) {
		// ?
		wptools_send_error_summary_email();
		update_option($option_name, $str_timestamp);
		wptools_wp_schedule_event($interval, $option_name);
	}

	// 5_days
	$option_name = 'wptools_cron_load_time_event';


	$interval = "5_days";
	$str_timestamp = date('Y-m-d H:i:s', strtotime('+5' . $interval));
	$next_scheduled_time = wptools_wp_next_scheduled($option_name);
	$next_scheduled_time = strtotime($next_scheduled_time);

	if (empty($next_scheduled_time)) {
		wptools_wp_schedule_event($interval, $option_name);
		return;
	}
	if ($next_scheduled_time && $current_time >= $next_scheduled_time) {
		wptools_send_load_summary_email();
		update_option($option_name, $str_timestamp);
		wptools_wp_schedule_event($interval, $option_name);
	}
}
add_action('wp_loaded', 'wptools_cron_customized');



//wptools_send_error_summary_email();



function wptools_wp_schedule_event($interval, $option_name)
{
	$str_timestamp = date('Y-m-d H:i:s', strtotime('+1 minute'));
	switch ($interval) {
		case 'weekly':
			$str_timestamp = date('Y-m-d H:i:s', strtotime('+1 week'));
			update_option($option_name, $str_timestamp);
			break;
		case '5_days':
			$str_timestamp = date('Y-m-d H:i:s', strtotime('+5 days'));
			update_option($option_name, $str_timestamp);
			break;
		case 'hourly':
			$str_timestamp = date('Y-m-d H:i:s', strtotime('+60 minute'));
			update_option($option_name, $str_timestamp);
			break;
		case '15_minutes':
			$str_timestamp = date('Y-m-d H:i:s', strtotime('+15 minutes'));
			break;
		case '1_minute':
			$str_timestamp = date('Y-m-d H:i:s', strtotime('+1 minute'));
			break;
		default:
			return;
	}
}
function wptools_wp_next_scheduled($event_name)
{

	$next_scheduled = get_option($event_name);

	if ($next_scheduled) {
		return get_option($event_name, '0');
	} else {
		return 'Evento not schedulled.';
	}
}

function wptools_bill_more()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			$declared_classes = get_declared_classes();
			foreach ($declared_classes as $class_name) {
				if (strpos($class_name, "Bill_show_more_plugins") !== false) {
					// return;
				}
			}
			require_once dirname(__FILE__) . "/includes/more/class_bill_more.php";
		}
	}
}
add_action("init", "wptools_bill_more");
// gg
/*
add_action( 'wp_enqueue_scripts', 'enqueue_ajax_script' );
function enqueue_ajax_script() {
  wp_enqueue_script( 'my-ajax-script', plugins_url( '/my-script.js', __FILE__ ), array( 'jquery' ), '1.0', true );
}
*/
//add_action( 'wp_ajax_nopriv_wptools_log_to_error_log_ajax', 'wptools_log_to_error_log_ajax' ); // Para usuários não logados
//add_action( 'wp_ajax_wptools_log_to_error_log_ajax', 'wptools_log_to_error_log_ajax' ); // Para usuários logados
/*
debug
function wptools_log_to_error_log_ajax() {
    wptools_log_to_error_log();
    wp_die(); 
}
*/
//
// more

/*
if (is_admin() or is_super_admin()) {
	if (!class_exists('class_billtools_show_plugins') and !class_exists('class_bill_show_plugins')  ) {
		add_action('admin_enqueue_scripts', 'wptools_load_upinstall');
		require_once(WPTOOLSPATH . "includes/more/more.php"); 
		
	}
}
function wptools_load_upinstall() {
	wp_enqueue_style('wptools-more', WPTOOLSURL . 'includes/more/more.css');
	wp_register_script('wptools-more-js', WPTOOLSURL . 'includes/more/more.js', array('jquery'));
	wp_enqueue_script('wptools-more-js');
}
*/

//
/**********************************
 * DEBUG
 **********************************/
/* function wptools_save_error() {
    update_option( 'wptools_plugin_error',  ob_get_contents() );
}
add_action( 'activated_plugin', 'wptools_save_error' );
echo get_option( 'wptools_plugin_error' ); */
// var_dump( wptools_errors_today());
// XMLHttpRequest must not be sending.
// add_action('admin_notices', 'wptools_dismiss_admin_notice');


// Tenta obter o formato de data configurado nas opções do WordPress
//$date_format = get_option('date_format');


/*
// Verifica se o formato de data foi obtido corretamente
if ($date_format) {
    // Formata a data e hora atual de acordo com o formato obtido
    $formattedMessage = "[" . date_i18n($date_format) . "] - " . $logMessage . PHP_EOL;
} else {
    // Em caso de falha ao obter o formato de data, formata no padrão dos Estados Unidos
    $formattedMessage = "[" . date('M-d-Y H:i:s') . "] - " . $logMessage . PHP_EOL;
}

}
debug2($formattedMessage);
*/

// --------------- 






add_action('admin_menu', function () {
	// Declare the global $submenu array
	global $submenu;

	// Check if the submenu already exists
	if (!isset($submenu['plugins.php']['mu-plugins'])) {
		add_submenu_page(
			'plugins.php',                              // Parent page (Plugins)
			esc_html__('MU Plugins', 'wptools'),          // Page title
			esc_html__('MU Plugins', 'wptools'),          // Menu name
			'manage_options',                            // Required capability
			'mu-plugins',                                // Slug for the page
			'exibir_mu_plugins'                          // Callback function
		);
	}
});




function exibir_mu_plugins()
{
	echo '<div class="wrap"><h1>' . esc_html__('Loaded MU Plugins', 'wptools') . '</h1><ul>';

	echo '<strong>';
	esc_html_e('MU-plugins (Must-Use Plugins) are plugins that are loaded automatically. They are stored in a specific folder (`wp-content/mu-plugins`) and cannot be activated or deactivated through the admin panel. The premium version of our plugin WP Tools allows you to activate and deactivate them.', 'wptools');


	echo '</strong><br>';
	echo '<p><a href="https://wptoolsplugin.com/understanding-mu-plugins-in-wordpress-a-key-to-site-security/" target="_blank">' . esc_attr__('Learn More', 'wptools') . '</a></p>';

	echo '<br><br>';

	$mu_plugins = listar_mu_plugins();

	$count = 0; // Background alternation counter
	foreach ($mu_plugins as $plugin => $details) {
		$plugin_path = WPMU_PLUGIN_DIR . '/' . $plugin;
		$file_name = basename($plugin); // Plugin file name
		$background_style = ($count % 2 == 0) ? 'background-color: #d3d3d3;' : '';

		echo '<li style="' . esc_attr($background_style) . '">';
		echo '<strong>' . esc_html($details['Name']) . '</strong>';
		echo ' - ' . esc_html($details['Description']) . '<br>';

		// Author
		if (!empty($details['Author'])) {
			echo esc_html__('Author:', 'wptools') . ' ' . esc_html($details['Author']) . '<br>';
		}

		// Author URI
		if (!empty($details['AuthorURI'])) {
			echo ' (<a href="' . esc_url($details['AuthorURI']) . '" target="_blank">' . esc_html__('Author Site', 'wptools') . '</a>)<br>';
		}

		// Version
		if (!empty($details['Version'])) {
			echo esc_html__('Version:', 'wptools') . ' ' . esc_html($details['Version']) . '<br>';
		}

		// File Name
		echo esc_html__('File Name:', 'wptools') . ' ' . esc_html($file_name) . '<br>';

		// File Path
		if (!empty($plugin_path)) {
			echo esc_html__('File Path:', 'wptools') . ' ' . esc_html($plugin_path) . '<br>';
		}

		// Check if the file exists and get the modification date
		if (file_exists($plugin_path)) {
			$modification_time = filemtime($plugin_path);
			echo esc_html__('Last Modified:', 'wptools') . ' ' . esc_html(date('d/m/Y H:i:s', $modification_time)) . '<br>';
		} else {
			echo esc_html__('Modification date not available.', 'wptools') . '<br>';
		}


		// Add the link to deactivate or reactivate the plugin
		if (str_ends_with($plugin, '.php')) {
			/*
			echo '<a href="' . esc_url(admin_url('admin-post.php?action=deactivate_mu_plugin&plugin=' . urlencode($plugin))) . '" 
                    onclick="return confirm(\'' . esc_js(esc_attr__('Are you sure you want to deactivate this plugin?', 'wptools')) . '\');">
                    ' . esc_html__('Deactivate Plugin', 'wptools') . '</a>';
					*/
			esc_attr_e('Status: Activated', 'wptools');
		} elseif (str_ends_with($plugin, '.disabled')) {

			/*
			echo '<a href="' . esc_url(admin_url('admin-post.php?action=activate_mu_plugin&plugin=' . urlencode($plugin))) . '" 
                    onclick="return confirm(\'' . esc_js(esc_attr__('Are you sure you want to reactivate this plugin?', 'wptools')) . '\');">
                    ' . esc_html__('Reactivate Plugin', 'wptools') . '</a>';
					*/
			esc_attr_e('Status: Deactivated', 'wptools');
		}


		echo '</li>';
		$count++;
	}

	echo '</ul></div>';
}

// Function to list MU plugins, including .php and .disabled files
function listar_mu_plugins()
{
	$mu_plugins = [];
	$plugin_files = array_merge(
		glob(WPMU_PLUGIN_DIR . '/*.php'),
		glob(WPMU_PLUGIN_DIR . '/*.disabled')
	);

	foreach ($plugin_files as $plugin_file) {
		$plugin_data = get_plugin_data($plugin_file, false, false);
		$mu_plugins[basename($plugin_file)] = $plugin_data;
	}

	return $mu_plugins;
}

// Action to deactivate the plugin
add_action('admin_post_deactivate_mu_plugin', 'deactivate_mu_plugin');
function deactivate_mu_plugin()
{
	if (current_user_can('activate_plugins')) {
		$plugin = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
		$plugin_path = WPMU_PLUGIN_DIR . '/' . $plugin;
		$new_path = $plugin_path . '.disabled';

		if (file_exists($plugin_path) && rename($plugin_path, $new_path)) {
			wp_redirect(admin_url('admin.php?page=mu-plugins&status=deactivated'));
			exit;
		}
	}
}

// Action to reactivate the plugin
add_action('admin_post_activate_mu_plugin', 'activate_mu_plugin');
function activate_mu_plugin()
{
	if (current_user_can('activate_plugins')) {
		$plugin = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
		$plugin_path = WPMU_PLUGIN_DIR . '/' . $plugin;
		$new_path = str_replace('.disabled', '', $plugin_path);

		if (file_exists($plugin_path) && rename($plugin_path, $new_path)) {
			wp_redirect(admin_url('admin.php?page=mu-plugins&status=activated'));
			exit;
		}
	}
}
