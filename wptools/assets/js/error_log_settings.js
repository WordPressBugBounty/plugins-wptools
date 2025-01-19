jQuery(document).ready(function ($) {
    const $lastNotice = $('.notice').last();
    const $settingsButton = $('<button>', {
        id: 'wptools-settings-button',
        text: wptoolsTranslations.setupButton, // Texto traduzido
        css: {
            padding: '8px 12px',
            backgroundColor: '#0073aa',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            marginTop: '35px',
            marginRight: '35px',
            position: 'absolute',
            right: '0',
            zIndex: 1000,
        },
    });
    if ($lastNotice.length) {
        $lastNotice.after($settingsButton);
        // console.log('Botão inserido após a última notificação.');
    } else {
        $('#wpbody-content').prepend($settingsButton.css({ position: 'absolute' }));
        // console.log('Botão inserido no início do conteúdo como fallback.');
    }
    const $settingsPanel = $('<div>', {
        id: 'settings-panel',
        css: {
            position: 'fixed',
            top: 0,
            right: '-320px',
            width: '300px',
            height: '100%',
            background: '#fff',
            borderLeft: '1px solid #ccc',
            boxShadow: '-2px 0 5px rgba(0, 0, 0, 0.2)',
            overflowY: 'auto',
            transition: 'right 0.3s ease-in-out',
            padding: '20px',
            zIndex: 1001,
        },
    }).appendTo('body');
    const $closeButton = $('<button>', {
        id: 'close-button',
        text: wptoolsTranslations.closeButton, // Texto traduzido
        css: {
            position: 'absolute',
            top: '50px',
            right: '10px',
            backgroundColor: '#ccc',
            color: '#333',
            border: 'none',
            borderRadius: '4px',
            padding: '5px 10px',
            cursor: 'pointer',
        },
    }).appendTo($settingsPanel);
    $settingsPanel.on('click', '#close-button', function () {
        $settingsPanel.css('right', '-320px');
        isPanelOpen = false;
    });
    let isPanelOpen = false;
    $settingsButton.on('click', function () {
        if (isPanelOpen) {
            $settingsPanel.css('right', '-320px');
        } else {
            $settingsPanel.css('right', '0');
            wptools_loadLogs();
        }
        isPanelOpen = !isPanelOpen;
    });
    function wptools_loadLogs() {
        $settingsPanel.html('<p>' + wptoolsTranslations.loadingMessage + '</p>'); // Texto traduzido
        $settingsPanel.append($closeButton);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wptools_find_logs',
                _ajax_nonce: wptoolsTranslations.nonce, // Nonce adicionado aqui
            },
            success: function (response) {
                if (response.success) {
                    wptools_displayLogs(response.data, response.data.selected_log);
                } else {
                    $settingsPanel.html('<p>' + wptoolsTranslations.errorLoadingLogs + '</p>').append($closeButton); // Texto traduzido
                }
            },
            error: function () {
                $settingsPanel.html('<p>' + wptoolsTranslations.errorLoadingLogs2 + '</p>').append($closeButton); // Texto traduzido
            },
        });
    }
    function wptools_displayLogs(logs, selectedLog) {
        if (logs.data.length === 0) {
            $settingsPanel.html('<p><strong>' + wptoolsTranslations.noLogsFound + '</strong></p>')
                .find('p')
                .css({
                    'margin-right': '70px',
                    'margin-top': '30px'
                })
                .end()
                .append($closeButton);
            return;
        }
        const $list = $('<form>');
        logs.data.forEach(function (log) {
            const $label = $('<label>', { css: { display: 'block', marginBottom: '10px' } });
            const $radio = $('<input>', {
                type: 'radio',
                name: 'logfile',
                value: log.path,
            });
            if (log.path === selectedLog) {
                $radio.prop('checked', true);
            }
            $label.append($radio).append(` ${log.name} (${log.size}) - ${log.path}`);
            $list.append($label);
        });
        const $viewButton = $('<button>', {
            id: 'wptools_setup_ajax',
            text: wptoolsTranslations.saveButton, // Texto traduzido
            css: {
                marginTop: '20px',
                padding: '8px 12px',
                backgroundColor: '#0073aa',
                color: 'white',
                border: 'none',
                borderRadius: '4px',
                cursor: 'pointer',
            },
        }).on('click', function (e) {
            e.preventDefault();
            const $button = $('#wptools_setup_ajax');
            $button.attr('disabled', true);
            $button.css({
                backgroundColor: '#d3d3d3',
                cursor: 'not-allowed',
            });
            const selectedLog = $('input[name="logfile"]:checked').val();
            if (selectedLog) {
                // console.log('Log selecionado:', selectedLog);
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wptools_save_log_option',
                        log_file: selectedLog,
                        _ajax_nonce: wptoolsTranslations.nonce, // Nonce adicionado aqui
                    },
                    success: function (response) {
                        if (response.success) {
                            // console.log('Resposta do servidor:', response.data);
                            alert(wptoolsTranslations.logSavedSuccess); // Texto traduzido
                            if (typeof table !== 'undefined' && table) {
                                table.ajax.reload(null, false);
                                // console.log('Tabela recarregada com sucesso.');
                            } else {
                                // console.error('Tabela não inicializada.');
                            }
                            location.reload();
                        } else {
                            // console.error('Erro na resposta:', response.data);
                            alert(wptoolsTranslations.logSaveError); // Texto traduzido
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // console.error('Erro na requisição AJAX:', textStatus, errorThrown);
                        alert(wptoolsTranslations.ajaxError); // Texto traduzido
                    }
                });
            } else {
                alert(wptoolsTranslations.selectLogAlert); // Texto traduzido
            }
        });
        $settingsPanel.html('<h3>' + wptoolsTranslations.logFilesHeader + '</h3>') // Texto traduzido
            .append($list)
            .append($viewButton)
            .append($closeButton);
    }
});