<?php

/**
 * Tools
 * 2022/01/11
 *
 * 
 * */
// Exit if accessed directly
if (! defined('ABSPATH')) exit;

echo '<div class="dbg-bkg" style="background:white">';
echo '<br>';
echo '<big>';
esc_attr_e("If you need support, please, copy and paste the info below in our", "wptools"); ?>
&nbsp;
<a href="https://BillMinozzi.com/support"><?php esc_attr_e("Support Site", "wptools"); ?></a>
<br><br>

<?php
if (! current_user_can('activate_plugins')) {
	return;
}

//wptools_sysinfo_display();


?>
<textarea style="height:100vh;width:100%" readonly="readonly" onclick="this.focus(); this.select()"><?php echo wptools_sysinfo_get(); ?></textarea>
<?php

echo '</big>';
echo '</div>';
