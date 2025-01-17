jQuery(document).ready(function ($) {
    // Cria o botão de configurações
    // Localiza o último aviso ou notificação do WordPress
    // Localiza o último aviso ou notificação do WordPress
    const $lastNotice = $('.notice').last();
    // Cria o botão com estilos personalizados
    const $settingsButton = $('<button>', {
        id: 'wptools-settings-button',
        text: 'Setup',
        css: {
            padding: '8px 12px',
            backgroundColor: '#0073aa',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            marginTop: '35px', // Espaço acima do botão
            marginRight: '35px', // Espaço à direita, para afastar da borda
            position: 'absolute', // Garante controle total sobre o posicionamento
            right: '0', // Alinha à direita
            zIndex: 1000, // Garante que o botão fique acima de outros elementos
        },
    });
    // Insere o botão após a última notificação
    if ($lastNotice.length) {
        // Insere o botão e ajusta a posição com relação ao contêiner do aviso
        $lastNotice.after($settingsButton);
        // $('#wpbody-content').prepend($settingsButton.css({ position: 'absolute' }));
        console.log('Botão inserido após a última notificação.');
    } else {
        // Se nenhuma notificação for encontrada, insere o botão no contêiner principal
        $('#wpbody-content').prepend($settingsButton.css({ position: 'absolute' }));
        console.log('Botão inserido no início do conteúdo como fallback.');
    }
    // Cria o painel lateral
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
    // Adiciona o botão de fechar fora do fluxo de re-renderização
    const $closeButton = $('<button>', {
        id: 'close-button',
        text: 'Close',
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
    // Evento de clique no botão de fechar (usando event delegation)
    $settingsPanel.on('click', '#close-button', function () {
        $settingsPanel.css('right', '-320px');
        isPanelOpen = false;
    });
    // Variável para controle do estado do painel
    let isPanelOpen = false;
    // Evento de clique no botão de configurações
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
        $settingsPanel.html('<p>Loading...</p>'); // Clear content and show loading state
        $settingsPanel.append($closeButton); // Add the close button to the panel
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wptools_find_logs',
                // nonce: wptools_ajax.nonce,
            },
            success: function (response) {
                if (response.success) {
                    wptools_displayLogs(response.data, response.data.selected_log);
                } else {
                    $settingsPanel.html('<p>Erro ao carregar os logs.</p>').append($closeButton);
                }
            },
            error: function () {
                $settingsPanel.html('<p>Erro ao carregar os logs (2).</p>').append($closeButton);
            },
        });
    }
    function wptools_displayLogs(logs, selectedLog) {
        if (logs.data.length === 0) {
            //  $settingsPanel.html('<p>No log files found.</p>').append($closeButton);
            $settingsPanel.html('<p><strong>No log files found. The log files should exist. Please contact your hosting provider.</strong></p>')
                .find('p') // Seleciona o elemento <p>
                .css({
                    'margin-right': '70px',  // Aplica a margem à direita
                    'margin-top': '30px'     // Aplica a margem superior de 30px
                })
                .end() // Retorna ao painel
                .append($closeButton);
            return;
        }
        const $list = $('<form>');
        // Adicionar uma linha para depurar o valor atual da opção
        logs.data.forEach(function (log) {
            const $label = $('<label>', { css: { display: 'block', marginBottom: '10px' } });
            const $radio = $('<input>', {
                type: 'radio',
                name: 'logfile',
                value: log.path,
            });
            // Marca o botão de rádio como selecionado se corresponder a `selectedLog`
            if (log.path === selectedLog) {
                $radio.prop('checked', true);
            }
            // Adiciona o nome, tamanho e caminho do log ao label
            $label.append($radio).append(` ${log.name} (${log.size}) - ${log.path}`);
            $list.append($label);
        });
        const $viewButton = $('<button>', {
            id: 'wptools_setup_ajax', // Adiciona o ID ao botão
            text: 'Save',
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
            e.preventDefault(); // Impede o comportamento padrão do botão
            const $button = $('#wptools_setup_ajax');
            // Desabilita o botão
            $button.attr('disabled', true);
            $button.css({
                backgroundColor: '#d3d3d3', // Cor de fundo mais clara quando desabilitado
                cursor: 'not-allowed',      // Muda o cursor para indicar desabilitado
            });
            // Captura o valor do botão de rádio selecionado no momento do clique
            const selectedLog = $('input[name="logfile"]:checked').val();
            if (selectedLog) {
                console.log('Log selecionado:', selectedLog); // Use console.log para depuração
                // Enviar uma requisição AJAX para a função PHP
                $.ajax({
                    url: ajaxurl, // URL do admin-ajax.php (fornecida pelo WordPress)
                    type: 'POST',
                    data: {
                        action: 'wptools_save_log_option', // Nome da ação que será chamada no PHP
                        log_file: selectedLog, // Passa o log selecionado como parâmetro
                    },
                    success: function (response) {
                        if (response.success) {
                            // Exibe os dados retornados no console
                            console.log('Resposta do servidor:', response.data);
                            // Aqui você pode atualizar a interface do usuário com os dados recebidos
                            //alert('Log carregado com sucesso! (22)');
                            if (typeof table !== 'undefined' && table) {
                                table.ajax.reload(null, false); // false para manter a paginação atual
                                console.log('Tabela recarregada com sucesso.');
                            } else {
                                console.error('Tabela não inicializada.');
                            }
                            // Recarrega a página imediatamente
                            location.reload();
                        } else {
                            console.error('Erro na resposta:', response.data);
                            alert('Erro ao carregar o log.');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Erro na requisição AJAX:', textStatus, errorThrown);
                        alert('Erro na requisição AJAX. Verifique o console para mais detalhes.');
                    }
                });
            } else {
                alert('Por favor, selecione um arquivo de log.');
            }
        });
        $settingsPanel.html('<h3>Log Files, choose one.</h3>')
            .append($list)
            .append($viewButton)
            .append($closeButton);
    }
});
