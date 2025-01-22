<?php
if (!defined("ABSPATH")) {
    exit();
}
// register_tick_function('meu_tick_function');
// declare(ticks=1);
wptools_show_logo();
?>
<h1 style="color: gray; text-align: center;"><?php esc_attr_e("Error Log Table", 'wptools'); ?></h1>
<div id="error-log-wrapper">
    <!-- Indicador de carregamento -->
    <div id="wptools-loadingIndicator">
        <div class="wptools-loadingIndicator"></div>
        <div class="wptools-loadingIndicator"></div>
        <div class="wptools-loadingIndicator"></div>
    </div>
    <!-- Filtro por tipo -->
    <div class="filter-options">
        <label for="type-filter"><?php esc_attr_e("Filter by Type", 'wptools'); ?>:</label>
        <select id="type-filter">
            <option value="all">All</option>
        </select>
    </div>
    <!-- Tabela de logs -->
    <table id="wptools-error-log-table" class="display">
        <thead>
            <tr>
                <th><?php esc_attr_e("Date", 'wptools'); ?></th>
                <th><?php esc_attr_e("Type", 'wptools'); ?></th>
                <th><?php esc_attr_e("Description", 'wptools'); ?></th>
                <th><?php esc_attr_e("Action", 'wptools'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo wptools_get_error_log_data(); ?>
        </tbody>
    </table>
    <div id="error-detail-modal" style="display: none;">
        <div class="modal-content">
            <!-- Tab Headers -->
            <div id="tab-header" style="display: flex; border-bottom: 2px solid #ccc;">
                <input type="radio" id="tab-details" name="tabs" checked style="display: none;">
                <label for="tab-details" style="flex: 1; text-align: center; padding: 10px; cursor: pointer; font-weight: bold; color: #333; border-bottom: 2px solid #0073aa;">Details</label>
                <input type="radio" id="tab-analysis" name="tabs" style="display: none;">
                <label for="tab-analysis" style="flex: 1; text-align: center; padding: 10px; cursor: pointer; font-weight: bold; color: #333; border-bottom: 2px solid transparent;">AI Analysis</label>
            </div>
            <!-- Tab Content -->
            <div id="tab-content" style="padding: 20px;">
                <!-- Details Tab Content -->
                <div id="details-content">
                    <table id="error-detail-table">
                        <tbody></tbody>
                    </table>
                </div>
                <!-- AI Analysis Tab Content -->
                <div id="analysis-content" style="display: none;">
                    <!-- chat -->
                    <div id="chat-box" style="margin-top: -10px !important; max-height: 400px !important;">
                        <div id="chat-header" style="padding: 10px !important;">
                            <h2><?php echo esc_attr__("Artificial Intelligence Support Chat for Issues and Solutions", "wptools"); ?></h2>
                        </div>
                        <div id="gif-container">
                            <div class="spinner999"></div>
                        </div> <!-- Onde o efeito será exibido -->
                        <div id="chat-messages" style="max-height: 180px !important;></div>
                        <div id=" error-message" style="display:none;"></div> <!-- Mensagem de erro -->
                        <form id="chat-form">
                            <div id="input-group">
                                <input type="text" id="chat-input" placeholder="<?php echo esc_attr__('Enter your message...', 'wptools'); ?>" />
                                <button type="submit" id="wptools"><?php echo esc_attr__('Send', 'wptools'); ?></button>
                            </div>
                            <div id="action-instruction" style="text-align: center; margin-top: 10px;">
                                <span><?php echo esc_attr__("Enter a message and click 'Send', or just copy and paste the content of description column.", 'wptools'); ?></span>
                            </div>
                        </form>
                    </div>
                    <!-- end chat -->
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button id="open-ai-analysis">AI Analysis</button>
                <button id="close-modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
function wptools_get_error_log_data()
{
    $default =    $file_path = ABSPATH . 'error_log';
    $file_path = get_option('wptools_log_file_name_option', $default);
    $output = '';




    try {
        // Verifica se o arquivo existe
        if (!file_exists($file_path)) {


            $file_path_error_log = ABSPATH . 'error_log';
            $file_path_debug_log = WP_CONTENT_DIR . 'debug.log';

            // Verifica se algum dos arquivos existe
            if (file_exists($file_path_error_log) || file_exists($file_path_debug_log)) {
                // Se pelo menos um arquivo existir, deixa o fluxo seguir adiante
                // Não retorna nada, apenas continua o processamento


                if (file_exists($file_path_error_log)) {

                    update_option('wptools_log_file_name_option', $file_path_error_log);
                } else {
                    update_option('wptools_log_file_name_option', $file_path_debug_log);
                }
            } else {
                // Se nenhum dos arquivos existir, retorna a mensagem de erro
                return '<tr><td colspan="4">' . esc_attr("Error log file not found! Click Setup Button at top right corner.", "wptools") . '</td></tr>';
            }
        }
    } catch (Exception $e) {
        echo '<tr><td colspan="4">Error to open error log file.';
        echo '  ' . $e->getMessage();
        echo '</td></tr>';
        return;
    }
    try {
        // Abre o arquivo em modo de leitura
        $file = fopen($file_path, 'r');
        if ($file === false) {
            return '<tr><td colspan="4">Error log file not readable!</td></tr>';
        }
    } catch (Exception $e) {
        echo '<tr><td colspan="4">Error to open error log file.';
        echo '  ' . $e->getMessage();
        echo '</td></tr>';
        return;
    }
    // Se chegou até aqui, o arquivo foi aberto com sucesso
    // Continue com o processamento do arquivo...
    // Inicializar o array para armazenar as linhas
    $lines = [];
    $numLines = 3000; // Número de linhas que você quer ler
    if (class_exists('SplFileObject')) {
        try {
            // Abre o arquivo com SplFileObject
            $fileObj = new SplFileObject($file_path, 'r');
            // Lê as primeiras $numLines linhas
            $ctd = 0;
            while (!$fileObj->eof() && $ctd < $numLines) {
                $line = $fileObj->current(); // Lê a linha atual
                if ($line !== false) {
                    $lines[] = $line; // Adiciona a linha ao array
                    $ctd++; // Incrementa o contador
                }
                $fileObj->next(); // Avança o ponteiro para a próxima linha
            }
        } catch (Exception $e) {
            // echo "Erro ao processar o arquivo: " . $e->getMessage();
            echo '<tr><td colspan="4">Error to process error log file.';
            echo '  ' . $e->getMessage();
            echo '</td></tr>';
            return;
        }
    } else {
        // Ler o restante das linhas e armazená-las no array
        $ctd = 0;
        //$file_path = 'caminho/para/seu/arquivo.log'; // Substitua pelo caminho do seu arquivo
        $ctd = 0;
        $lines = [];
        try {
            //$numLines = 3000;
            // $file_path = '/path/to/your/logfile.log'; // Atualize o caminho para o arquivo
            $transient_key = 'log_file_total_lines';
            // Verifica se o número de linhas está armazenado no transiente
            $totalLines = get_transient($transient_key);
            if ($totalLines === false) {
                // Conta o número total de linhas do arquivo
                $totalLines = 0;
                $file = fopen($file_path, 'r');
                if ($file === false) {
                    return '<tr><td colspan="4">Error to open log file!</td></tr>';
                }
                while (fgets($file) !== false) {
                    $totalLines++;
                }
                fclose($file);
                // Grava o total de linhas no transiente por 15 minutos
                set_transient($transient_key, $totalLines, 15 * MINUTE_IN_SECONDS);
            }
            // Verifica se o arquivo está vazio
            if ($totalLines === 0) {
                return '<tr><td colspan="4">The log file is empty!</td></tr>';
            }
            // Lê as últimas $numLines linhas
            $lines = [];
            $file = fopen($file_path, 'r');
            if ($file === false) {
                return '<tr><td colspan="4">Error to open log file!</td></tr>';
            }
            // Determina o ponto de início para ler as últimas linhas
            $startLine = max(0, $totalLines - $numLines);
            $currentLine = 0;
            while (($line = fgets($file)) !== false) {
                if ($currentLine >= $startLine) {
                    $lines[] = $line;
                }
                $currentLine++;
            }
            // Fecha o arquivo
            fclose($file);
            // Agora, $lines contém as últimas linhas do arquivo
            // Você pode processar ou exibir essas linhas conforme necessário
        } catch (Exception $e) {
            echo '<tr><td colspan="4">Error to process error log file.';
            echo '  ' . $e->getMessage();
            echo '</td></tr>';
            return;
        }
    }
    // Se o arquivo estiver vazio após o corte inicial
    if (empty($lines)) {
        return '<tr><td colspan="4">No errors found in the log file.</td></tr>';
    }
    $ctd = 0;
    //debug20($log_entry);
    foreach ($lines as $line) {
        $line_orig = $line;
        $found = false; // Rastrea se algum padrão correspondeu
        if (empty(trim($line)))
            continue;
        /*
        if (trim($line) == '-------------------------===-----------------------------------') {
            continue; // Pular para a próxima linha
        }
        if (trim($line) == '------------------------------------------------------------') {
            continue; // Pular para a próxima linha
        }
        if (strpos($line, 'Undefined variable $xx') !== false) {
            //  continue; // Pular para a próxima linha
        }
        if (strpos($line, 'Automatic plugin updates complete') !== false) {
            continue; // Pular para a próxima linha
        }
        if (strpos($line, 'Automatic updates complete') !== false) {
            continue; // Pular para a próxima linha
        }
        if (strpos($line, 'Automatic ') !== false) {
            continue; // Pular para a próxima linha
        }
        if (strpos($line, 'Não') !== false) {
            continue; // Pular para a próxima linha
        }
        if (strpos($line, 'abeç') !== false) {
            continue; // Pular para a próxima linha
        }
        */
        // ---------------------------
        //$pattern = "/PHP Stack trace:/";
        $pattern = "/Stack trace:/";
        if (preg_match($pattern, $line, $matches)) {
            continue;
        }
        if (substr($line, 0, 1) === '#') {
            // Stack trace
            continue; // Pular para a próxima linha
        }
        $pattern =
            "/\d{4}-\w{3}-\d{4} \d{2}:\d{2}:\d{2} UTC\] PHP \d+\./";
        if (preg_match($pattern, $line, $matches)) {
            // continue;
        }
        /* ---------- data  -------------------------- */
        $datePattern = '/\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}/'; // sem colchetes...
        $datePattern = '/\[\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}.*?\]/';
        $filteredDate = false;
        if (preg_match($datePattern, $line, $matches2)) {
            $filteredDate = $matches2[0]; // Data capturada pela regex, incluindo colchetes
        } else {


            $filteredDate = $line;
            if (is_string($line) && strpos($line, '[') !== false && strpos($line, ']') !== false) {
                $start = strpos($line, '[');
                $end = strpos($line, ']');
                // Continue seu processamento aqui

                if ($start !== false && $end !== false) {
                    $filteredDate = substr($line, $start, $end - $start + 1); // Extrai a data com colchetes
                } else {
                    // $filteredDate = false; // Nenhuma data encontrada
                }
            } else {
                // Trate a situação onde $line é inválido ou não contém os caracteres esperados
                //error_log("Invalid line or missing '[' or ']' in the string: " . var_export($line, true));
            }



            //$start = strpos($line, '[');
            // $end = strpos($line, ']');




        }
        //echo "Linha sem a data: $lineWithoutDate\n";
        /* ---------- data  -------------------------- */
        if ($filteredDate and wptools_has_two_digit_day($filteredDate)) {
            //// debug4($filteredDate);
            $line = str_replace("$filteredDate ", '', $line);
            //  $line = str_replace("$filteredDate ", '', $line ?? '');
        } else
            continue;


        $filteredDate =  wptools_transformarData($filteredDate);




        $apattern = [];
        $apattern = []; // Inicializando o array vazio
        $line = wptools_removeAccents($line);
        // deepok 
        $apattern[] = "/^(?:PHP\s+)?(.*?\b\w+\b)(.*)$/";
        $log_entry = array();
        for ($j = 0; $j < count($apattern); $j++) {
            if (
                preg_match($apattern[$j], $line, $matches)
            ) {
                $pattern = $apattern[$j];
                $found = true; // Rastrea se algum padrão correspondeu
                $type = replaceIfOnlyNumbers($matches[1]);
                $type = replaceMonthsWithOther($type);
                $description = removeNonAlphanumericStart($matches[2]);
                // trace
                if (preg_match('/^PHP\s+\d+\./', $line)) {
                    $log_entry = false;
                    break;
                }
                if (empty($description) or empty($type) or empty($filteredDate)) {
                    $log_entry = false;
                    break;
                    // continue;
                }
                //  // debug4();
                $log_entry = [
                    "Date" => $filteredDate,
                    "Type" => $type,
                    "Description" => wptools_strip_strong(
                        $description
                    ),
                ];
                break;
            }
        }
        $ctd++;
        if (!$log_entry)
            continue;
        if ($found) {
            $data = $log_entry["Date"];
            $type = $log_entry["Type"];
            $type = limparString($type);
            $type = replaceIfOnlyNumbers($type);
            $description  = $log_entry["Description"];
            //$description = htmlspecialchars($log_entry["Description"] ?? 'Unknown');
            //$url = htmlspecialchars($matches[4] ?? 'Unknown');
            // $line_number = htmlspecialchars($matches[5] ?? 'Unknown');
            // Adicionar a linha correspondente ao output
            $output .= "<tr>\n";
            $output .= "<td>{$data}</td>\n";
            //$type = str_replace(["[", "]"], "", $type);
            // $type = str_replace(["<", ">"], "", $type);
            $type = preg_replace("/[^a-zA-Z0-9\s]/", "", $type);
            $output .= "<td>{$type}</td>\n";
            //$description = htmlentities($description, ENT_QUOTES, 'UTF-8');
            $output .= "<td>{$description}</td>\n";
            $output .= "<td><button class='view-details'>" . esc_attr__('View', 'wptools') . "</button></td>\n";
            $output .= "</tr>\n";
        } else {
            // debug4($line);
            // debug4($line_orig);
            // debug3("Nenhuma correspondência para a linha: {$line}");
            $output .= "<tr>\n";
            $output .= "<td>N/A</td>\n";
            $output .= "<td>Unknown</td>\n";
            $output .= "<td>" . htmlspecialchars($line) . "</td>\n";
            $output .= "<td><button class='view-details'>View22222</button></td>\n";
            $output .= "</tr>\n";
        }
    }
    return $output;
}
function wptools_has_two_digit_day($line)
{
    // Padrão para verificar se há dois algarismos consecutivos para o dia
    $datePattern = '/\[(\d{2})-/';
    // Verifica se o padrão corresponde à data
    if (preg_match($datePattern, $line, $matches)) {
        // O dia é capturado em $matches[1]
        return true; // A data tem 2 algarismos para o dia
    }
    // Se não encontrar o padrão
    return false;
}
// Função para remover acentos de várias línguas
function wptools_removeAccents($string)
{
    // Mapa de caracteres com acentos para equivalentes sem acentos
    $unwantedChars = [
        // Letras maiúsculas
        'Á' => 'A',
        'À' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'É' => 'E',
        'È' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Í' => 'I',
        'Ì' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ó' => 'O',
        'Ò' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ú' => 'U',
        'Ù' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'Ð' => 'D',
        'Š' => 'S',
        'Ž' => 'Z',
        // Letras minúsculas
        'á' => 'a',
        'à' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ñ' => 'n',
        'ó' => 'o',
        'ò' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        'đ' => 'd',
        'š' => 's',
        'ž' => 'z',
        // Outros caracteres especiais
        'Œ' => 'OE',
        'œ' => 'oe',
        'Ŕ' => 'R',
        'ŕ' => 'r',
    ];
    // Substitui os caracteres usando o mapa
    return strtr($string, $unwantedChars);
}
function wptools_transformarData($data)
{
    // Remove colchetes, se existirem
    $data = trim($data, "[]");
    // Lista de formatos possíveis
    $formatos = [
        'd-M-Y H:i:s e',  // Exemplo: 30-May-2024 03:13:57 UTC
        'd-M-Y H:i:s',    // Exemplo: 30-May-2024 03:13:57
        'Y-m-d H:i:s',    // Exemplo: 2024-05-30 03:13:57
        'd/m/Y H:i:s',    // Exemplo: 30/05/2024 03:13:57
        'm/d/Y H:i:s',    // Exemplo: 05/30/2024 03:13:57
        'd-M-Y',          // Exemplo: 30-May-2024
        'Y-m-d',          // Exemplo: 2024-05-30
        'd/m/Y',          // Exemplo: 30/05/2024
        'm/d/Y',          // Exemplo: 05/30/2024
        'd.m.Y H:i:s',    // Exemplo: 30.05.2024 03:13:57
        'd.m.Y',          // Exemplo: 30.05.2024
        'Y.m.d',          // Exemplo: 2024.05.30
        'd M Y H:i:s',    // Exemplo: 30 May 2024 03:13:57
        'd M Y',          // Exemplo: 30 May 2024
        'M d, Y H:i:s',   // Exemplo: May 30, 2024 03:13:57
        'M d, Y',         // Exemplo: May 30, 2024
        'D, d M Y H:i:s', // Exemplo: Thu, 30 May 2024 03:13:57
        'D, d M Y',       // Exemplo: Thu, 30 May 2024
    ];
    // Tenta cada formato
    foreach ($formatos as $formato) {
        $dateTime = DateTime::createFromFormat($formato, $data);
        if ($dateTime !== false) {
            // Retorna a data no formato 'YYYY-MM-DD'
            // return $dateTime->format('Y-m-d');
            return $dateTime->format('Y-m-d H:i:s');
        }
    }
    // Se nenhum formato for válido, retorna a string original
    return $data;
}
function replaceIfOnlyNumbers($script)
{
    // Remove espaços em branco no início e fim
    $script = trim($script);
    // Verifica se o conteúdo contém apenas números
    if (preg_match('/^\d+$/', $script)) {
        $script = "Other";
    }
    return $script;
}
function limparString($string)
{
    // Remove todos os caracteres que não são letras ou números
    $stringLimpa = preg_replace('/[^a-zA-Z0-9]/', '', $string);
    return $stringLimpa;
}
function removeNonAlphanumericStart($input)
{
    // Verifica se a entrada é uma string
    if (!is_string($input)) {
        return $input; // Retorna a entrada original se não for string
    }
    // Verifica se o primeiro caractere não é letra ou número
    // if (!ctype_alnum($input[0])) {
    if (!empty($input) && !ctype_alnum($input[0])) {
        // Remove o primeiro caractere não alfanumérico
        $input = substr($input, 1);
    }
    return $input;
}
function replaceMonthsWithOther($string)
{
    // Lista dos meses do ano em inglês (em maiúsculas e minúsculas)
    $months = [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    // Verifica se a string contém qualquer um dos meses
    foreach ($months as $month) {
        if (stripos($string, $month) !== false) {
            return "Other";
        }
    }
    // Se não encontrar nenhum mês, retorna a string original
    return $string;
}
?>