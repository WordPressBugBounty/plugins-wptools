<?php
// 2023-10-16 upd: 2023-10-16
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
// Linha 41 é custom do wptools
if(is_multisite())
  return;
if(!function_exists("bill_is_action_registered")){
	function bill_is_action_registered($hook_name, $callback_function) {
		global $wp_filter;
		if (isset($wp_filter[$hook_name])) {
			foreach ($wp_filter[$hook_name] as $priority => $actions) {
				foreach ($actions as $action) {
					if (is_array($action['function']) && $action['function'][0] === $callback_function) {
						return true;
					}
				}
			}
		}
		return false;
	}
}
if (!bill_is_action_registered('wp_ajax_bill_get_js_errors', 'bill_js_error_catched')) {
	add_action('wp_ajax_bill_js_error_catched', 'bill_js_error_catched');
	add_action('wp_ajax_nopriv_bill_js_error_catched', 'bill_js_error_catched');
}
if(!function_exists("bill_js_error_catched")){
	function bill_js_error_catched()
	{
		if (isset($_REQUEST)) {
			if (!isset($_REQUEST['bill_js_error_catched']))
				die("empty error");
			if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'bill-catch-js-errors')) {
				status_header(406, 'Invalid nonce');
				die();
			}
			$bill_js_error_catched = sanitize_text_field($_REQUEST['bill_js_error_catched']);
			$bill_js_error_catched = trim($bill_js_error_catched);
			// 2024
			$errstr = substr($bill_js_error_catched, 9);

            /*
			$errfile = explode(" | ", $message)[1];
			$errline = explode(" | ", $message)[2];
            */


            $parts = explode(" | ", $message);
            if (isset($parts[1])) {
                $errfile = $parts[1];
            } else {
                // Lida com a ausência do índice 1
                //$errfile = 'Indice 1 não encontrado';
                die('NOT OK 1!');
            }
            
            if (isset($parts[2])) {
                $errline = $parts[2];
            } else {
                // Lida com a ausência do índice 2
                //$errline = 'Indice 2 não encontrado';
                die('NOT OK 2!');
            }
            


			wptoolsErrorHandler('Javascript', $errstr, $errfile, $errline);
			if (!empty($bill_js_error_catched)) {
				$parts = explode(" | ", $bill_js_error_catched);
				for ($i = 0; $i < count($parts); $i++) {
					$txt = 'Javascript ' . $parts[$i];
					 error_log($txt);
					add_option( 'bill_javascript_error', time() );
				}
				die('OK!!!');
			}
		}
		die('NOT OK!');
	}
}
class bill_catch_errors {
    public function __construct() {
        add_action('wp_head', array($this, 'add_bill_javascript_to_header'));
        add_action('admin_head', array($this, 'add_bill_javascript_to_header'));
    }
    public function add_bill_javascript_to_header() {
        $nonce = wp_create_nonce('bill-catch-js-errors'); 
        $ajax_url = $this->get_ajax_url().'?action=log_js_error&_wpnonce=' . $nonce; 
        ?>
        <script>
        var errorQueue = []; 
        var timeout;
        function isBot() {
            const bots = ['bot', 'googlebot', 'bingbot', 'facebook', 'slurp', 'twitter','yahoo']; // Add other bots if necessary
            const userAgent = navigator.userAgent.toLowerCase();
            return bots.some(bot => userAgent.includes(bot));
        }
        window.onerror = function(msg, url, line) {
            var errorMessage = [
                'Message: ' + msg,
                'URL: ' + url,
                'Line: ' + line
            ].join(' - ');
            // Filter bots errors...
            if (isBot()) {
                return;
            }
            errorQueue.push(errorMessage); 
            if (errorQueue.length >= 5) { 
                sendErrorsToServer();
            } else {
                clearTimeout(timeout);
                timeout = setTimeout(sendErrorsToServer, 5000); 
            }
        }
        function sendErrorsToServer() {
            if (errorQueue.length > 0) {
                var message = errorQueue.join(' | ');
                var xhr = new XMLHttpRequest();
                var nonce = '<?php echo esc_js($nonce); ?>';
                var ajaxurl = '<?php echo esc_js($ajax_url); ?>';
                xhr.open('POST', encodeURI(ajaxurl)); 
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (200 === xhr.status) {
                        try {
                            //console.log(xhr.response);
                        } catch (e) {
                            console.log('error xhr not 200!');
                        }
                    } else {
                        console.log('error 2');
                    }
                };
                xhr.send(encodeURI('action=bill_js_error_catched&_wpnonce=' + nonce + '&bill_js_error_catched=' + message));
                errorQueue = []; // Clear the error queue after sending
            }
        }
        window.addEventListener('beforeunload', sendErrorsToServer);
        </script>
        <?php
    }
    private function get_ajax_url() {
        return admin_url('admin-ajax.php');
    }
}
new bill_catch_errors();
