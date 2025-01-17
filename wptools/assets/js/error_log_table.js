jQuery(document).ready(function ($) {
    // console.log("Carregou datatables...");
    $('#wptools-loadingIndicator').show();
    //  $('#wptools-error-log-table').show();
    // modal tabs...
    function wptools_resetTabs() {
        $('#tab-details').prop('checked', true); // Seleciona a aba da esquerda
        $('#tab-analysis').prop('checked', false); // Desmarca a aba da direita
        $('#details-content').show(); // Mostra o conteúdo da aba da esquerda
        $('#analysis-content').hide(); // Oculta o conteúdo da aba da direita
        $('#open-ai-analysis').show(); // Mostra o botão AI Analysis
    }
    var interval = setInterval(function () {
        var table = document.getElementById('wptools-error-log-table');
        // alert(table);
        var wptools_loadingIndicator = document.getElementById('wptools-loadingIndicator');
        // if (table && table.querySelector('tbody') && table.querySelector('tbody').children.length > 0) {
        if (table && table.querySelector('tbody').children.length > 0) {
            // Quando a tabela tiver dados, esconda o indicador de carregamento e mostre a tabela
            wptools_loadingIndicator.style.display = 'none';
            table.style.display = 'block';
            // console.log("Tabela carregada com dados!");
            clearInterval(interval); // Para o intervalo após detectar a tabela
        } else {
            // console.log("Tabela ainda não tem dados, aguardando...");
        }
    }, 500); // Intervalo de 500ms para verificar se os dados estão disponíveis
    function wptools_sanitizeHTML(input) {
        const div = document.createElement('div');
        div.textContent = input; // Define o texto como conteúdo da div
        return div.innerHTML; // Retorna o texto sanitizado
    }
    /*
        function sanitizeErrorContent(errorContent) {
            // Remover qualquer tag <pre> e seu conteúdo
            return errorContent.replace(/<pre[^>]*>([\s\S]*?)<\/pre>/g, '');
        }
    */
    let table;
    function wptools_parseErrorString_no_js(errorString) {
        // Matriz de regexes para capturar a mensagem e o path
        const regexesN = [
            /^(.*?)(\s+in\s+(\/[^:]+))(\s+on\s+line\s+(\d+))$/, // Para erros com 'in' e 'on line'
            /^(.*?)(\s+in\s+(\/.*?:\d+))$/, // Para outros formatos com 'in' e apenas 'path:linha'
        ];
        const regexes = [
            /^(.*?)(\s+in\s+([^:]+):\d+)$/,  // Formato genérico
            // Adicione outros formatos de regex conforme necessário
            /^(.*?)(\s+in\s+(\/[^:]+))(\s+on\s+line\s+(\d+))$/
        ];
        // Tentativa de encontrar mensagem, path e script
        let message, path, script, line;
        for (let i = 0; i < regexes.length; i++) {
            const match = errorString.match(regexes[i]);
            if (match) {
                message = match[1].trim();
                path = match[3].trim();
                line = match[5] ? parseInt(match[5], 10) : null;
                // A partir do path, capturamos o script
                const pathParts = path.split('/');
                script = pathParts[pathParts.length - 1]; // O script é a última parte do path
                // Remover o script do path
                path = path.replace(`/${script}`, "");  // Removendo o nome do script do path
                break;
            }
        }
        if (!message || !path) {
            // console.error("Não foi possível capturar a string de erro.");
            return null;
        }
        // Agora vamos capturar a linha com um segundo conjunto de regexes
        const lineRegexes = [
            /on\s+line\s+(\d+)$/,  // Para erros com 'on line'
            /:(\d+)$/, // Para erros com 'path:linha' sem 'on line'
        ];
        // Loop para tentar cada regex de linha
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
        // Verificar se a string de erro é válida
        if (typeof errorString !== 'string' || errorString.trim() === '') {
            console.error("String de erro inválida.");
            return null;
        }
        const regex = /Message:\s([^\(]*)/;
        const match1 = errorString.match(regex);
        if (!match1) {
            // alert('1');
            // console.error("Não foi possível capturar a mensagem de erro.");
            return null;
        }
        const wptools_message = match1[1].trim(); // Mensagem de erro
        const regex2 = /(https?:\/\/[^\s\)]+)/;
        const match2 = errorString.match(regex2);
        if (!match2) {
            return null;
        }
        const wptools_url = match2[1]; // URL completa
        // alert(wptools_url);
        let wptools_path_ok, wptools_script;
        try {
            // Crie o objeto URL
            const wptools_urlObj = new URL(wptools_url);
            // Acesse e verifique o valor de pathname
            const wptools_path = wptools_urlObj.pathname;
            console.log(wptools_path);
            if (wptools_path) {
                const wptools_pathParts = wptools_path.split('/');
                wptools_script = wptools_pathParts[wptools_pathParts.length - 1]; // O script é a última parte do path
                wptools_path_ok = wptools_path.replace(`/${wptools_script}`, ""); // Remover o nome do script do path
                console.log(wptools_path_ok);
            }
        } catch (error) {
            console.error("Erro ao criar o objeto URL ou acessar pathname:", error);
        }
        // Expressão regular para capturar o número da linha
        const regex31 = /- Line:\s*(\d+)/;
        const match31 = errorString.match(regex31);
        if (match31) {
            var wptools_line = match31[1]; // Captura o número da linha
        } else {
            return null;
        }
        // Verificar se todas as variáveis necessárias foram definidas
        if (!wptools_url || !wptools_path_ok || !wptools_script || !wptools_line) {
            console.error("Dados incompletos.");
            return null;
        }
        // Retornar um objeto com as informações capturadas
        const wptools_return = {
            message: wptools_message,
            url: wptools_url,
            path: wptools_path_ok,
            script: wptools_script,
            line: wptools_line
        };
        console.log(wptools_return);
        return wptools_return;
    }
    function wptools_parseErrorString(errorString, errorType) {
        // Verificar se a string de erro é válida
        if (typeof errorString !== 'string' || errorString.trim() === '') {
            console.error("String de erro inválida.");
            return null;
        }
        // Verificar se é um erro de JavaScript com base no tipo de erro
        const isJavascriptError = errorType.toLowerCase().includes("javascript");
        if (isJavascriptError) {
            //console.log("Chamando wptools_parseErrorString_js...");
            return wptools_parseErrorString_js(errorString); // Chama a função para erros de JavaScript
        } else {
            //console.log("Chamando wptools_parseErrorString_no_js...");
            return wptools_parseErrorString_no_js(errorString); // Chama a função para erros não relacionados a JavaScript
        }
    }
    function wptools_initializeDataTable() {
        table = $('#wptools-error-log-table').DataTable({
            pageLength: 10, // Define o número de linhas por página
            order: [[0, "desc"]], // Ordena a primeira coluna (índice 0) em ordem decrescente
            columnDefs: [
                {
                    type: "date", // Define o tipo da coluna como "date"
                    targets: 0 // Aplica isso à primeira coluna (índice 0)
                }
            ],
            language: {
                emptyTable: ""
            }
        });

        // console.log("Tabela inicializada dentro de wptools_initializeDataTable");
        wptools_updateTypeFilter();
        table.on('draw.dt', function () {
            $('#wptools-loadingIndicator').hide();
            $('#wptools-error-log-table').show();
            // console.log("Tabela redesenhada (draw.dt)");
            wptools_updateTypeFilter();
        });
    }
    function wptools_updateTypeFilter() {
        if (typeof table === 'undefined' || !table.column) {
            console.log("A instância do DataTable não está disponível.");
            return;
        }
        // Verifica se a tabela tem dados
        if (table.rows().count() === 0) {
            console.log("A tabela está vazia.");
            return;
        }
        // Salva o valor atual do filtro antes de atualizar as opções
        var currentFilterValue = $('#type-filter').val();
        var types = [];
        table.column(1).data().unique().each(function (value, index) {
            if (types.indexOf(value) === -1) {
                types.push(value);
            }
        });
        // console.log("Tipos encontrados:", types);
        var typeFilter = $('#type-filter');
        typeFilter.empty(); // Limpa o select
        typeFilter.append('<option value="all">All</option>');
        types.forEach(function (type) {
            typeFilter.append('<option value="' + type + '">' + type + '</option>');
        });
        // Restaura o valor do filtro, se ainda for válido
        if (types.includes(currentFilterValue) || currentFilterValue === "all") {
            typeFilter.val(currentFilterValue);
        } else {
            typeFilter.val("all"); // Fallback para "All" se o valor não for mais válido
        }
        // console.log("Conteúdo final do select de 'Type':", typeFilter.html());
        // console.log("Valor do filtro restaurado para:", typeFilter.val());
    }
    $('#error-log-wrapper').on('click', '.view-details', function () {
        wptools_resetTabs(); // Reseta as abas para o estado padrão
        const row = $(this).closest('tr').children();
        // Capturando os valores das duas primeiras colunas e a descrição
        const date = row.eq(0).text();
        //const type = row.eq(1).text();
        const type = row.eq(1).text().trim(); // Remove espaços extras no início e no final
        const rawError = row.eq(2).text(); // A descrição contém a string do erro
        // console.log("Raw error (1):", rawError);
        // Sanitizando a string rawError para exibição segura
        const sanitizedRawError = wptools_sanitizeHTML(rawError);
        const parsedError = wptools_parseErrorString(rawError, type);
        let details;
        /* ---------------------------------------------------------*/
        if (parsedError) {
            details = `
            <tr><td><strong>Date:</strong></td><td>${date}</td></tr>
        `;
            // var wtype = type;
            var wtype = type.trim().toLowerCase();
            const phpErrors = {
                "Parse": "Critical Syntax error in the code that stops script execution.",
                "Fatal": "Critical error that stops script execution.",
                "Warning": "Non-critical issue that doesn't stop execution but may eventually lead to problems.",
                "Notice": "Minor issue, like using an undefined variable.",
                "Deprecated": "Use of outdated features that may be removed in future versions.",
                "Core": "Internal PHP error, often related to the engine.",
                "Compile": "Error during script compilation.",
                "User": "Custom error/warning/notice triggered by the developer.",
                "Javascript": "Issues that impact functionality and user experience."
            };
            // Verifica se wtype existe no objeto phpErrors (case-insensitive)
            const foundKey = Object.keys(phpErrors).find(key => key.toLowerCase() === wtype.toLowerCase());
            if (foundKey) {
                // console.warn(1);
                wtype = wtype + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="dashicons dashicons-info"></span> ' + phpErrors[foundKey] + " ";
            } else {
                console.warn(`Tipo de erro desconhecido: "${wtype}"`); // Avisa no console
                wtype = wtype + ''; // Adiciona uma mensagem para tipos desconhecidos
            }
            details += `
        <tr><td><strong>Type:</strong></td><td>${wtype}</td></tr>
    `;
            // Adicionar "Message" apenas se não for vazio, null ou undefined
            if (parsedError.message && parsedError.message.trim() !== "") {
                details += `<tr><td><strong>Message:</strong></td><td>${parsedError.message}</td></tr>`;
            }
            // Adicionar "Path" apenas se não for vazio, null ou undefined
            if (parsedError.path && parsedError.path.trim() !== "") {
                details += `<tr><td><strong>Path:</strong></td><td>${parsedError.path}</td></tr>`;
            }
            // Adicionar "Script" apenas se não for vazio, null ou undefined
            if (parsedError.script && parsedError.script.trim() !== "") {
                details += `<tr><td><strong>Script:</strong></td><td>${parsedError.script}</td></tr>`;
            }
            // Adicionar "Line" apenas se não for vazio, null ou undefined
            if (parsedError.line !== null && parsedError.line !== undefined && parsedError.line !== "") {
                details += `<tr><td><strong>Line:</strong></td><td>${parsedError.line}</td></tr>`;
            }
            // Verificando se o path contém '/plugins/' ou '/themes/' e criando novas linhas com o nome do diretório
            if (parsedError.path.includes('/plugins/')) {
                const pluginName = parsedError.path.split('/plugins/')[1].split('/')[0];
                details += `
                < tr > <td colspan="2"><strong>Plugin:</strong> ${pluginName}</td></tr >
                    `;
                const note = " - Contact the plugin developer with these details and request support. However, be advised that often the cause may be external to the plugin, such as insufficient server memory (or insufficient WordPress Memory Limit), conflicts with other plugins (or theme), lack of disk space, server overload due to hacker and bot spiders among other factors.";
                details += `
                    < tr > <td colspan="2"><strong>Note:</strong> ${note}</td></tr >
                        `;
            }
            if (parsedError.path.includes('/themes/')) {
                const themeName = parsedError.path.split('/themes/')[1].split('/')[0];
                details += `
        <tr><td colspan="2"><strong>Theme:</strong> ${themeName}</td></tr>`;
            }
        } else {
            // Remover o caractere `]` se estiver na primeira posição de sanitizedRawError
            const correctedSanitizedRawError = sanitizedRawError.startsWith(']')
                ? sanitizedRawError.substring(1) // Remove o primeiro caractere se for `]`
                : sanitizedRawError; // Mantém o valor original se não começar com `]`
            details = `
                <tr><td><strong>Date:</strong></td><td>${date}</td></tr>
                <tr><td><strong>Type:</strong></td><td>${type}</td></tr>
                <!-- <tr><td colspan="2"><strong>Unable to parse error details</strong></td></tr> -->
                    <tr><td><strong>Details:</strong></td><td colspan="2">${correctedSanitizedRawError}</td></tr>
            `;
            // alert(sanitizedRawError);
        }
        details += `
        <tr>
            <td colspan="2">
                <span class="dashicons dashicons-info"></span> 
                <strong>To learn more:</strong> <a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" target="_blank">Click here</a>
            </td>
        </tr>
    `;
        // Verificando o conteúdo da variável 'details' para garantir que está correto
        // console.log("Details to display:", details);
        // Inserindo os detalhes na tabela de erro e exibindo o modal
        $('#error-detail-table tbody').html(details);
        $('#error-detail-modal').fadeIn();
    });
    /* ------------------------------------ */
    $('#close-modal').click(function () {
        $('#error-detail-modal').fadeOut();
    });
    $('#analyze-button').click(function () {
        alert('Analyze functionality will be implemented here.');
    });
    $('#type-filter').change(function () {
        const typeValue = $(this).val();
        if (typeValue === "all") {
            // console.log("Removendo filtro de tipo");
            table.column(1).search('').draw();
        } else {
            //console.log("Filtrando por tipo:", typeValue);
            table.column(1).search('^' + typeValue + '$', true, false).draw();
        }
    });
    wptools_initializeDataTable();
    // Quando o modal for aberto, sempre seleciona a aba "Details" como a padrão
    // Ao abrir o modal, sempre coloca "Details" como aba padrão
    // Quando o botão que abre o modal é clicado
    $('#open-modal').on('click', function () {
        $('#error-detail-modal').show(); // Mostra o modal
        wptools_resetTabs(); // Reseta as abas para o estado padrão
    });
    // Quando o botão AI Analysis for clicado
    $('#open-ai-analysis').on('click', function () {
        // Marca a aba "AI Analysis" como ativa
        $('#tab-analysis').prop('checked', true);
        $('#tab-details').prop('checked', false);
        $('#details-content').hide();
        $('#analysis-content').show();
        // Oculta o botão AI Analysis após clicar
        $('#open-ai-analysis').hide();
    });
    // Alterna entre as abas manualmente
    $('input[type="radio"]').on('change', function () {
        if ($('#tab-details').prop('checked')) {
            $('#details-content').show();
            $('#analysis-content').hide();
            $('#open-ai-analysis').show(); // Mostrar o botão AI Analysis
        } else {
            $('#details-content').hide();
            $('#analysis-content').show();
            $('#open-ai-analysis').hide(); // Ocultar o botão AI Analysis
        }
    });
    // Fechar o modal
    $('#close-modal').on('click', function () {
        $('#error-detail-modal').hide(); // Fecha o modal
    });
    // Função para resetar as abas para o estado padrão
    function wptools_resetTabs() {
        $('#tab-details').prop('checked', true); // Seleciona a aba da esquerda
        $('#tab-analysis').prop('checked', false); // Desmarca a aba da direita
        $('#details-content').show(); // Mostra o conteúdo da aba da esquerda
        $('#analysis-content').hide(); // Oculta o conteúdo da aba da direita
        $('#open-ai-analysis').show(); // Mostra o botão AI Analysis
    }
    // Define a string oculta que será adicionada no início
    const hiddenString = ":";
    // Inicializa o campo com a string oculta
    $('#chat-input').val(hiddenString);
    // Quando o usuário digita no campo
    $('#chat-input').on('input', function () {
        // Captura o valor atual do campo
        let currentValue = $(this).val();
        // Verifica se a string oculta foi removida ou alterada
        if (!currentValue.startsWith(hiddenString)) {
            // Se a string oculta foi removida, restaura o valor com a string oculta
            $(this).val(hiddenString + currentValue);
        }
    });
    // Quando o campo perde o foco (opcional)
    $('#chat-input').on('blur', function () {
        let currentValue = $(this).val();
        if (!currentValue.startsWith(hiddenString)) {
            $(this).val(hiddenString + currentValue);
        }
    });
});
