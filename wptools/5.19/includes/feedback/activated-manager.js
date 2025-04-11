jQuery(document).ready(function($){
    // console.log('js activated... 24'); 
    // jQuery('#adminmenumain').css('opacity', '.1');
        // jQuery('.wrap').css('opacity', '.1');
        // jQuery('.wp-pointer').css('opacity', '0');
        jQuery('.bill-activate-modal-wptools').slideDown(); 
        // jQuery('.bill-activate-modal-wptools').css('opacity', '1');
        jQuery('#imagewait').hide();

    
   // jQuery('#bill_imagewait').show();


    // install plugin wpmemory
    jQuery('#bill-install-wpmemory-now').click(function(e) {
        // alert('ccc');
        e.preventDefault();
        //jQuery('.bill-install-wpt-plugin-now').prop('disabled', true);
        jQuery('#bill-install-wpmemory-now').hide();
        jQuery('#loading-spinner').show();
        var nonce = jQuery('#nonce').val();
        // alert(nonce);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bill_install_plugin2',
                slug: 'wp-memory',
                nonce: nonce
            },
            success: function(response) {
                //console.log(response);
                jQuery('#loading-spinner').hide();
                var main_slug = jQuery('#main_slug').val();
                var slug = 'wp-memory';
                // sendSecondRequest_wpmemory(response,slug,main_slug);
                if(response.trim() === 'OK') {
                    jQuery('#bill_imagewait').hide();
                    //alert('Plugin WPmemory Installed Successfully.');
                    $('body').showToast('WPmemory Installed Successfully!', 5000, 'ok');
                }


                /*
                var d = new Date();
                d.setTime(d.getTime() + (wtime * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";
                */
                // create cookie...
                //var BILLCLASS = "ACTIVATED_" + slug;
                var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                var d = new Date();
                var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";

            },
            error: function(xhr, status, error) {
                jQuery('#loading-spinner').hide();
                console.error('Error while installing the plugin.:', error);
                alert('An error occurred while installing the plugin. Please try again later.');
            },
            complete: function(result) {
                jQuery('#loading-spinner').hide();
                //console.log('result');
                // sendSecondRequest_wpmemory('OK', 'wp-memory','wptools');
                jQuery('#bill_imagewait').hide();
                jQuery('#loading-spinner').prop('disabled', true);
                jQuery('#loading-spinner').text('Installed');
            }
        });
    });
        // Close
       jQuery('#wptools-activate-close-up-dialog').on('click', function() {



        var slug = jQuery('#slug').val();

        // console.log(slug);


        var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
        var d = new Date();
        var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
        d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
        var expires = "expires="+d.toUTCString();

        //console.log(BILLCLASS);
        //console.log(expires);

        document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";

        

        alert('Thank you for installing our plugin!');
        jQuery('.bill-activate-modal-wptools').hide(); 

         // location.reload();
        });
});  