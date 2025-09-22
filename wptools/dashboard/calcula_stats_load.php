<?php
/**
 * @author William Sergio Minossi
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


global $wpdb;
$table_name = $wpdb->prefix . 'wptools_page_load_times';

$query = "SELECT DATE(timestamp) AS date, AVG(load_time) AS average_load_time
          FROM $table_name
          WHERE timestamp >= CURDATE() - INTERVAL 6 DAY
            AND NOT page_url LIKE 'wp-admin'
          GROUP BY DATE(timestamp)
          ORDER BY date";
$results9 = $wpdb->get_results($query, ARRAY_A);

////////////////////////////////////////////

if ($wpdb->last_error) {
    error_log("Query error: " . $wpdb->last_error);
    // Se o erro for porque a tabela não existe, tenta recriar
    if (strpos($wpdb->last_error, "doesn't exist") !== false) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            page_url VARCHAR(255) NOT NULL,
            load_time FLOAT NOT NULL,
            timestamp DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX page_url_index (page_url),
            INDEX timestamp_index (timestamp)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // ⭐⭐ VERIFICA SE A TABELA FOI CRIADA COM SUCESSO ⭐⭐
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            error_log("Tabela $table_name criada com sucesso");
            // ⭐⭐ NÃO EXECUTA A QUERY NOVAMENTE - TABELA ESTÁ VAZIA ⭐⭐
            $wptools_empty = true;
            return;
        } else {
            error_log("Falha ao criar tabela $table_name");
            $wptools_empty = true;
            return;
        }
    } else {
        // Outro tipo de erro
        $wptools_empty = true;
        return;
    }
}

/////////////////////////////////////////

if ($results9) {
    $total = count($results9);
    if($total < 1 ) {
      $wptools_empty = true;
      return;
    }

} else {
    $wptools_empty = true;
    return;
}

$results8 = json_decode(json_encode($results9), true);
unset($results9);

$x = 0;
$d = 7;

for ($i = $d; $i > 0; $i--) {
    $timestamp = time();
    $tm = 86400 * ($x); // 60 * 60 * 24 = 86400 = 1 day in seconds
    $tm = $timestamp - $tm;
    $array7ld[$x] = date("Y-m-d", $tm); // Adjust the format to match the database
    $search_value = trim($array7ld[$x]);
    $array7ld[$x] = date("Y-m-d", $tm);
    
    // Use 'date' instead of 'error_day' for comparison
    $mykey = array_search($array7ld[$x], array_column($results8, 'date'));

    $the_day = date("d", $tm);
    $this_month = date('m', $tm);
    $array7ld[$x] = $this_month . $the_day;

    if ($mykey !== false) {
        $awork = $results8[$mykey]['average_load_time'];
        $array7l[$x] = round($awork, 1); // Arredonda para 2 casas decimais

    } else {
        $array7l[$x] = 0;
    }
    $x++;
}

$array7l = array_reverse($array7l);
$array7ld = array_reverse($array7ld);
// die(var_export($array7l));