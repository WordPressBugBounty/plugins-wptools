<?php namespace wptools_plugin_activate {

    // used in WPtools and WPmemory
    // checar pela existencia de um transiente ou cookie antes de executar

    $bill_debug = false;
    //$bill_debug = true;

    //

    if (is_multisite()) {
        return;
    }


    /*
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if (function_exists('is_plugin_active')){


        $bill_plugins_to_check = array(
            'antihacker/antihacker.php',        
            'wp-memory/wpmemory.php',  
            'wptools/wptools.php',  
            'stopbadbots/stopbadbots.php'   
        );


        foreach ($bill_plugins_to_check as $plugin_path) {
            if (is_plugin_active($plugin_path)) 
            return;
        }

    }
    */



    if (__NAMESPACE__ == "wptools_plugin_activate") {
        $BILLPRODUCT = "WPTOOLS";
        $BILLPRODUCTNAME = "WP Tools Plugin";
        $BILLPRODUCTSLANGUAGE = "wptools";
        $BILLCLASS = "ACTIVATED_" . $BILLPRODUCT;
        $BILL_OPTIN = strtolower($BILLPRODUCT) . "_optin";
        $PRODUCT_URL = WPTOOLSURL;
        $PRODUCTVERSION = WPTOOLSVERSION;
    }

    $showroom_url = admin_url("tools.php?page=wp_tools_admin_page");
    $showroom_url .= "&tab=tools";
    if (!isset($_COOKIE[$BILLCLASS]) or $bill_debug) { 
            //div modal
            echo '<div id="bill-activate-modal-wptools" class="bill-activate-modal-wptools" style="display:none" >';
          
            ?>
                <div class="bill-vote-message-wptools">
                            

                    <?php 

                        echo '<h3>';
                        echo esc_attr($BILLPRODUCTNAME).' - ';
                        echo esc_attr__("PRE-INSTALLATION CHECKUP", "wptools");
                        echo '</h3>'; 


                        echo '<h4>';
                        echo esc_attr__("Welcome!", "wptools");
                        echo '</h4>'; 


                        $wptools_memory = wptools_check_memory();
                        $data = $wptools_memory;
                        $data["msg_type"] = ""; 

                        if ($wptools_memory['msg_type'] == 'notok') {
                            esc_attr_e("Unable to get your Memory Info","wptools"); 
                        }

                        if (is_array($data) and isset($data["wp_limit"] ) and isset($data["usage"])) {
                            // Check if each key exists before accessing it
                            if (array_key_exists("msg_type", $data)) {
                                if($data["msg_type"] == "notok")
                                echo "Unable to retrieve memory data from your server. This could be due to a hosting issue.";
                            } else {
                                echo "Unable to retrieve memory data from your server. This could be due to a hosting issue (2).";
                            }

                            $data["free"] = $data["wp_limit"] - $data["usage"];
                            $data["percent"] = $data["usage"] / $data["wp_limit"] ;

                            if ($data["free"] < 30 || $data["percent"] > 0.8) {
                                // Change the color of the message to red
                                //
                                $data["color"] = "color:red;";
                                // Set the warning message
                                $data["msg_type"] = "warning";
                            }
                        }
                        else {
                            $data["msg_type"] = "";  
                        }
                        
                            if (
                                array_key_exists("free", $data) &&
                                array_key_exists("percent", $data)
                            ) {
                                // Display the results
                                echo "Percentage of used memory: " .
                                    esc_attr(number_format($data["percent"] * 100, 0)) .
                                    "%<br>";
                                echo "Free memory: " . esc_attr($data["free"]) . "MB<br>";
                            }

                            // Check if 'usage' key exists before accessing it
                            if (array_key_exists("usage", $data)) {
                                echo "Memory Usage: " . esc_attr($data["usage"]) . "MB<br>";
                            }

                            if (array_key_exists("limit", $data)) {
                                echo "PHP Memory Limit: " . esc_attr($data["limit"]) . "MB<br>";
                            }

                            // Check if 'wp_limit' key exists before accessing it
                            if (array_key_exists("wp_limit", $data)) {
                                echo "WordPress Memory Limit: " .
                                    esc_attr($data["wp_limit"]) .
                                    "MB<br>";
                            }
                            echo "<br /><strong>" . "Memory Status: " . "</strong>";

                    if ($data["msg_type"] == "warning") {

                            // Display the status message


                            if ($data["msg_type"] !== "warning") {
                                echo "All good.";
                                echo "<br>";
                            } else {

                                echo '<p style="color: red;">';

                                echo esc_attr__(
                                    "Your WordPress Memory Limit is too low, which can lead to critical issues on your site due to insufficient resources. Promptly address this issue before continuing.",
                                    'wptools'
                                );
                                echo "</p>";
                                echo "</b>";
                                //
                                ?>
                                    </b>
                                    <a href= "https://wpmemory.com/fix-low-memory-limit/" target="_blank">
                                    <?php echo esc_attr__(
                                        "Learn More",
                                        'wptools'
                                    ); ?>
                                    </a>
                                    </p>
                                    <br>

                                <?php
                                $all_plugins = get_plugins();

                                $is_wp_tools_installed = false;

                                foreach ($all_plugins as $plugin_info) {
                                    if ($plugin_info["Name"] === "WP Memory") {
                                        $is_wp_tools_installed = true;
                                        break; // Exit the loop once found
                                    }
                                }

                                if (!$is_wp_tools_installed) { ?>
                                        If you'd like help with memory management, this free plugin can help.
                                        <br>
                                        <a href="#" id="bill-install-wpmemory-now" class="button button-primary bill-install-plugin-now">Install WPmemory Free</a>
                                        <button id="loading-spinner" class="button button-primary" style="display: none;" aria-label="Loading...">
                                    <span class="loading-text">Installing...</span>
                                    </button>
                            <?php }

                            }

                        //
                        ?>

                <?php } else {

                            // NO errorss found

                                ?>
                                <br />
                                Ok!
                                <br />
                                <br />
                                We have been developing WordPress plugins and themes for 10 years and now have a suite of 20 plugins and 6 themes serving thousands of users.
                                <br />
                                If you find any issues, please consider requesting free support before leaving feedback.
                                <br /> <br />

                                By proceeding, you agree that you have read and understood the 
                                <a href="https://siterightaway.net/terms-of-use-of-our-plugins-and-themes/" target="_blank">terms of use</a>
                                of our plugins and themes.

                        <?php
                        }
                        // end not errors
                        ?>

                        <form>                         
                                                    
                            <br />  <br />

                            <a href="#" class="button button-primary" id="wptools-activate-close-up-dialog">
                            <?php esc_attr_e("CONTINUE", "wptools"); ?></a>

                            <br />  <br />

                            <input type="hidden" id="nonce" name="nonce" value="<?php echo esc_attr(wp_create_nonce(
                                "bill_install"
                            )); ?>" />

<input type="hidden" id="slug" name="slug" value="<?php echo esc_attr($BILLPRODUCT); ?>" />
 


                            <input type="hidden" id="showroom" name="showroom" value="<?php echo esc_attr(
                                $showroom_url
                            ); ?>" />
                            <br />
                        </form>

                </div>


        
                <?php

                add_option("wptools_activated_notice", "0");
                update_option("wptools_activated_notice", "0");
                //

                /*
                $wtime = time() + 3600 * 24;
                $jsCode =
                    "document.cookie = '" .
                    $BILLCLASS .
                    "=" .
                    time() .
                    "; expires=" .
                    date("D, d M Y H:i:s", $wtime) .
                    " UTC; path=/';";
                // echo "<script>{$jsCode}</script>";
                // echo '<script>' . esc_js($jsCode) . '</script>';
                */

            echo '</div>'; // end modal 

    } //nao tem cookie...


//

    

} // end Namespace
?>
