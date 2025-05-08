jQuery(document).ready(function ($) {
    // console.log("Carregou datatables...");
    $('#wptools-loadingIndicator').show();

    function wptools_resetTabs() {
        $('#tab-details').prop('checked', true);
        $('#tab-analysis').prop('checked', false);
        $('#details-content').show();
        $('#analysis-content').hide();
        $('#open-ai-analysis').show();
    }

    var interval = setInterval(function () {
        var table = document.getElementById('wptools-error-log-table');
        var wptools_loadingIndicator = document.getElementById('wptools-loadingIndicator');
        if (table && table.querySelector('tbody').children.length > 0) {
            wptools_loadingIndicator.style.display = 'none';
            table.style.display = 'block';
            clearInterval(interval);
        }
    }, 500);

    function wptools_sanitizeHTML(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }

    let table;
    function wptools_parseErrorString_no_js(errorString) {
        const regexes = [
            /^(.*?)(\s+in\s+(\/[^:]+))(\s+on\s+line\s+(\d+))$/,
            /^(.*?)(\s+in\s+(\/.*?:\d+))$/,
        ];
        let message, path, script, line;
        for (let i = 0; i < regexes.length; i++) {
            const match = errorString.match(regexes[i]);
            if (match) {
                message = match[1].trim();
                path = match[3].trim();
                line = match[5] ? parseInt(match[5], 10) : null;
                const pathParts = path.split('/');
                script = pathParts[pathParts.length - 1];
                path = path.replace(`/${script}`, "");
                break;
            }
        }
        if (!message || !path) {
            return null;
        }
        const lineRegexes = [
            /on\s+line\s+(\d+)$/,
            /:(\d+)$/,
        ];
        for (let i = 0; i < lineRegexes.length; i++) {
            const lineMatch = errorString.match(lineRegexes[i]);
            if (lineMatch) {
                line = parseInt(lineMatch[1], 10);
                break;
            }
        }
        return {
            message,
            path,
            script,
            line
        };
    }

    function wptools_parseErrorString_js(errorString) {
        if (typeof errorString !== 'string' || errorString.trim() === '') {
            console.error(wptools_vars.invalid_error_string);
            return null;
        }
        const regex = /Message:\s([^\(]*)/;
        const match1 = errorString.match(regex);
        if (!match1) {
            return null;
        }
        const wptools_message = match1[1].trim();
        const regex2 = /(https?:\/\/[^\s\)]+)/;
        const match2 = errorString.match(regex2);
        if (!match2) {
            return null;
        }
        const wptools_url = match2[1];
        let wptools_path_ok, wptools_script;
        try {
            const wptools_urlObj = new URL(wptools_url);
            const wptools_path = wptools_urlObj.pathname;
            if (wptools_path) {
                const wptools_pathParts = wptools_path.split('/');
                wptools_script = wptools_pathParts[wptools_pathParts.length - 1];
                wptools_path_ok = wptools_path.replace(`/${wptools_script}`, "");
            }
        } catch (error) {
            console.error(wptools_vars.url_error, error);
        }
        const regex31 = /- Line:\s*(\d+)/;
        const match31 = errorString.match(regex31);
        if (match31) {
            var wptools_line = match31[1];
        } else {
            return null;
        }
        if (!wptools_url || !wptools_path_ok || !wptools_script || !wptools_line) {
            console.error(wptools_vars.incomplete_data);
            return null;
        }
        const wptools_return = {
            message: wptools_message,
            url: wptools_url,
            path: wptools_path_ok,
            script: wptools_script,
            line: wptools_line
        };
        return wptools_return;
    }

    function wptools_parseErrorString(errorString, errorType) {
        if (typeof errorString !== 'string' || errorString.trim() === '') {
            console.error(wptools_vars.invalid_error_string);
            return null;
        }
        const isJavascriptError = errorType.toLowerCase().includes("javascript");
        if (isJavascriptError) {
            return wptools_parseErrorString_js(errorString);
        } else {
            return wptools_parseErrorString_no_js(errorString);
        }
    }

    function wptools_initializeDataTable() {
        table = $('#wptools-error-log-table').DataTable({
            pageLength: 10,
            order: [[0, "desc"]],
            columnDefs: [
                {
                    type: "date",
                    targets: 0
                }
            ],
            language: {
                emptyTable: wptools_vars.empty_table
            }
        });

        wptools_updateTypeFilter();
        table.on('draw.dt', function () {
            $('#wptools-loadingIndicator').hide();
            $('#wptools-error-log-table').show();
            wptools_updateTypeFilter();
        });
    }

    function wptools_updateTypeFilter() {
        if (typeof table === 'undefined' || !table.column) {
            console.log(wptools_vars.table_not_available);
            return;
        }
        if (table.rows().count() === 0) {
            //console.log(wptools_vars.empty_table);
            return;
        }
        var currentFilterValue = $('#type-filter').val();
        var types = [];
        table.column(1).data().unique().each(function (value, index) {
            if (types.indexOf(value) === -1) {
                types.push(value);
            }
        });
        var typeFilter = $('#type-filter');
        typeFilter.empty();
        typeFilter.append('<option value="all">' + wptools_vars.all + '</option>');
        types.forEach(function (type) {
            typeFilter.append('<option value="' + type + '">' + type + '</option>');
        });
        if (types.includes(currentFilterValue) || currentFilterValue === "all") {
            typeFilter.val(currentFilterValue);
        } else {
            typeFilter.val("all");
        }
    }

    $('#error-log-wrapper').on('click', '.view-details', function () {
        wptools_resetTabs();
        const row = $(this).closest('tr').children();
        const date = row.eq(0).text();
        const type = row.eq(1).text().trim();
        const rawError = row.eq(2).text();
        const sanitizedRawError = wptools_sanitizeHTML(rawError);
        const parsedError = wptools_parseErrorString(rawError, type);
        let details;
        if (parsedError) {
            details = `
            <tr><td><strong>${wptools_vars.date}:</strong></td><td>${date}</td></tr>
        `;
            var wtype = type.trim().toLowerCase();
            const phpErrors = {
                "Parse": wptools_vars.parse_error,
                "Fatal": wptools_vars.fatal_error,
                "Warning": wptools_vars.warning_error,
                "Notice": wptools_vars.notice_error,
                "Deprecated": wptools_vars.deprecated_error,
                "Core": wptools_vars.core_error,
                "Compile": wptools_vars.compile_error,
                "User": wptools_vars.user_error,
                "Javascript": wptools_vars.javascript_error
            };
            const foundKey = Object.keys(phpErrors).find(key => key.toLowerCase() === wtype.toLowerCase());
            if (foundKey) {
                wtype = wtype + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="dashicons dashicons-info"></span> ' + phpErrors[foundKey] + " ";
            } else {
                console.warn(wptools_vars.unknown_error_type + `: "${wtype}"`);
                wtype = wtype + '';
            }
            details += `
        <tr><td><strong>${wptools_vars.type}:</strong></td><td>${wtype}</td></tr>
    `;
            if (parsedError.message && parsedError.message.trim() !== "") {
                details += `<tr><td><strong>${wptools_vars.message}:</strong></td><td>${parsedError.message}</td></tr>`;
            }
            if (parsedError.path && parsedError.path.trim() !== "") {
                details += `<tr><td><strong>${wptools_vars.path}:</strong></td><td>${parsedError.path}</td></tr>`;
            }
            if (parsedError.script && parsedError.script.trim() !== "") {
                details += `<tr><td><strong>${wptools_vars.script}:</strong></td><td>${parsedError.script}</td></tr>`;
            }
            if (parsedError.line !== null && parsedError.line !== undefined && parsedError.line !== "") {
                details += `<tr><td><strong>${wptools_vars.line}:</strong></td><td>${parsedError.line}</td></tr>`;
            }
            if (parsedError.path.includes('/plugins/')) {
                const pluginName = parsedError.path.split('/plugins/')[1].split('/')[0];
                details += `
                <tr><td colspan="2"><strong>${wptools_vars.plugin}:</strong> ${pluginName}</td></tr>
                    `;
                details += `
                    <tr><td colspan="2"><strong>${wptools_vars.note}:</strong> ${wptools_vars.plugin_note}</td></tr>
                        `;
            }
            if (parsedError.path.includes('/themes/')) {
                const themeName = parsedError.path.split('/themes/')[1].split('/')[0];
                details += `
        <tr><td colspan="2"><strong>${wptools_vars.theme}:</strong> ${themeName}</td></tr>`;
            }
        } else {
            const correctedSanitizedRawError = sanitizedRawError.startsWith(']')
                ? sanitizedRawError.substring(1)
                : sanitizedRawError;
            details = `
                <tr><td><strong>${wptools_vars.date}:</strong></td><td>${date}</td></tr>
                <tr><td><strong>${wptools_vars.type}:</strong></td><td>${type}</td></tr>
                    <tr><td><strong>${wptools_vars.details}:</strong></td><td colspan="2">${correctedSanitizedRawError}</td></tr>
            `;
        }
        details += `
        <tr>
            <td colspan="2">
                <span class="dashicons dashicons-info"></span> 
                <strong>${wptools_vars.learn_more}:</strong> <a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" target="_blank">${wptools_vars.click_here}</a>
            </td>
        </tr>
    `;
        $('#error-detail-table tbody').html(details);
        $('#error-detail-modal').fadeIn();
    });

    $('#close-modal').click(function () {
        $('#error-detail-modal').fadeOut();
    });

    $('#analyze-button').click(function () {
        alert(wptools_vars.analyze_functionality);
    });

    // $('#type-filter').change(function () {
    $('#type-filter').on('change', function () {
        const typeValue = $(this).val();
        if (typeValue === "all") {
            table.column(1).search('').draw();
        } else {
            table.column(1).search('^' + typeValue + '$', true, false).draw();
        }
    });

    wptools_initializeDataTable();

    $('#open-modal').on('click', function () {
        $('#error-detail-modal').show();
        wptools_resetTabs();
    });

    $('#open-ai-analysis').on('click', function () {
        $('#tab-analysis').prop('checked', true);
        $('#tab-details').prop('checked', false);
        $('#details-content').hide();
        $('#analysis-content').show();
        $('#open-ai-analysis').hide();
    });

    $('input[type="radio"]').on('change', function () {
        if ($('#tab-details').prop('checked')) {
            $('#details-content').show();
            $('#analysis-content').hide();
            $('#open-ai-analysis').show();
        } else {
            $('#details-content').hide();
            $('#analysis-content').show();
            $('#open-ai-analysis').hide();
        }
    });

    $('#close-modal').on('click', function () {
        $('#error-detail-modal').hide();
    });

    function wptools_resetTabs() {
        $('#tab-details').prop('checked', true);
        $('#tab-analysis').prop('checked', false);
        $('#details-content').show();
        $('#analysis-content').hide();
        $('#open-ai-analysis').show();
    }

    const hiddenString = ":";
    $('#chat-input').val(hiddenString);

    $('#chat-input').on('input', function () {
        let currentValue = $(this).val();
        if (!currentValue.startsWith(hiddenString)) {
            $(this).val(hiddenString + currentValue);
        }
    });

    $('#chat-input').on('blur', function () {
        let currentValue = $(this).val();
        if (!currentValue.startsWith(hiddenString)) {
            $(this).val(hiddenString + currentValue);
        }
    });
});


