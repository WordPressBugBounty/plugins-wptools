<?php

/**
 * Tools
 * 2022/01/11
 *
 * https://xxx.com/wp-admin/admin-ajax.php?action=xxx_exportDiagnostics&nonce=b0e9911454
 * 
 * */
// Exit if accessed directly
if (! defined('ABSPATH')) exit;


require_once(WPTOOLSPATH . "functions/function_sysinfo.php");


echo '<div class="dbg-bkg" style="background:white">';
echo '<br>';
echo '<big>';
esc_attr_e("If you need support, please, copy and paste the info below in our", "wptools"); ?>
&nbsp;
<a href="https://BillMinozzi.com/support"><?php esc_attr_e("Support Site", "wptools"); ?></a>
<br><br>
<?php
$sysinfo = wptools_sysinfo_get();

$allowed_tags = array(
	'br' => array(), // Permite a tag <br>
	'p' => array(),  // Permite a tag <p> (se necessário)
	'strong' => array(), // Permite a tag <strong> (se necessário)
	'em' => array(), // Permite a tag <em> (se necessário)
	'a' => array(    // Permite a tag <a> com atributos específicos
		'href' => array(),
		'title' => array(),
		'target' => array(),
	),
);
echo nl2br(wp_kses($sysinfo, $allowed_tags)); // Aplica sanitização e converte \n em <br>






echo '</big>';
echo '</div>';
return;
