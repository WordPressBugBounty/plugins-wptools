if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function ($) {
        // console.log("Carregou");
        var loadingTime = performance.now();
        // Check if loadingTime is a valid number
        if (!isNaN(loadingTime) && loadingTime > 0) {
            // Convert to seconds
            var loadingTimeInSeconds = loadingTime / 1000;
        } else {
            console.error('Invalid loading time value.');
            return;
        }
        var urlWithDomain = window.location.href;
        // Check if the variable is defined and not null, empty, or consists only of whitespace
        if (urlWithDomain && urlWithDomain.trim() !== "") {
            // The variable is defined and not null, empty, or consists only of whitespace
            // Proceed with the code here
            //console.log("The urlWithDomain variable is correct:", urlWithDomain);
        } else {
            console.log("The urlWithDomain variable is not correct.");
            return;
        }

        function extractBaseURL(urlWithDomain) {
            var urlWithoutDomain = urlWithDomain.replace(/^(https?:\/\/)?(www\.)?[^\/]+\/?(.*)$/, "$3");
            if (urlWithoutDomain === "") {
                return "/";
            }
            if (urlWithoutDomain.endsWith("/")) {
                urlWithoutDomain = urlWithoutDomain.slice(0, -1);
            }
            return urlWithoutDomain;
        }
        var url = extractBaseURL(urlWithDomain);
        // Check if the URL is valid before logging
        if (typeof url === 'string' && url.length > 0) {
            //console.log("ajax: " + loadingTimeInSeconds);


            //console.log('Nonce:', wptools_ajax_object.ajax_nonce);
            //console.log('url:', url);

            var data = {
                action: 'wptools_register_loading_time3',
                page_url: url,
                loading_time: loadingTimeInSeconds,
                nonce: wptools_ajax_object.ajax_nonce
            };
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                success: function (data) {
                    // This outputs the result of the ajax request
                    // console.log(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.group("AJAX Error");
                    console.error("Status textual:", textStatus);
                    console.error("Código HTTP:", jqXHR.status);
                    console.error("Erro lançado:", errorThrown);
                    console.error("Resposta do servidor:", jqXHR.responseText);
                    console.groupEnd();
                }
            });
        } else {
            console.error('Invalid URL! ' + url);
        }
    });
}