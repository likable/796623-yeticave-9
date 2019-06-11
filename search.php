<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

session_start();

$user_id = $_SESSION["id"];
$is_auth = false;
$user_name = "";

if (isset($user_id)) {
    $is_auth = true;
    $user_name = get_user_name_from_id($database_connection, $user_id);    
}

//проверка отправления формы
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    //сохранение введённых в поля значений
    $search = htmlspecialchars($_GET["search"]) ?? "";
    $search = trim($search);

    //валидация
    if (!empty($search)) {
        //поле поиска не пустое
        //получение списка лотов - результат поиска
        $sql_search = "SELECT l.id AS lot_id, lot_name, lot_image_src, 
            start_price, current_price, dt_end, cat_name 
            FROM lots l 
            LEFT JOIN categories c ON category_code = character_code 
            WHERE MATCH(lot_name, description) AGAINST(?)
            ORDER BY dt_add DESC;";
        $stmt_search = mysqli_prepare($database_connection, $sql_search);
        mysqli_stmt_bind_param($stmt_search, 's', $search);
        mysqli_stmt_execute($stmt_search);
        $sql_search_result = mysqli_stmt_get_result($stmt_search);
        if ($sql_search_result) {
            $srched_lots = mysqli_fetch_all($sql_search_result, MYSQLI_ASSOC);
            $searched_lots = array_slice($srched_lots, 0, 9);
            $searched_lots_count = mysqli_num_rows($sql_search_result);
        }
        
        $bets_count_array = [];
        
        //получение количества ставок к каждому найденному лоту
        foreach ($searched_lots as $searched_lot) {
            $searched_lot_id = $searched_lot["lot_id"];
            $sql_b_count = "SELECT * FROM bets WHERE lot_id=?";
            $stmt_b_count = mysqli_prepare($database_connection, $sql_b_count);
            mysqli_stmt_bind_param($stmt_b_count, 'i', $searched_lot_id);
            mysqli_stmt_execute($stmt_b_count);
            $sql_bets_count_result = mysqli_stmt_get_result($stmt_b_count);
            if ($sql_bets_count_result) {
                $bets_count = mysqli_num_rows($sql_bets_count_result);
                $bets_count_array[$searched_lot_id] = $bets_count;
            }
        }
        
        //формирую основной контент тега <main>
        $content = include_template('search-main.php', 
            [
            'categories'          => $categories, 
            'search'              => $search,
            'searched_lots'       => $searched_lots,
            'bets_count_array'    => $bets_count_array,
            'searched_lots_count' => $searched_lots_count
            ]);
    }    
    else {
        //поле поиска пустое
        $content = include_template('search-main.php', 
        [
        'categories'          => $categories,
        'searched_lots_count' => -1
        ]);
    }    
}
else {    
    //формирую основной контент тега <main> --> форма не отправлена
    $content = include_template('search-main.php', 
        [
        'categories'          => $categories,
        'searched_lots_count' => -1
        ]);
}

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Результаты поиска", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => $search
    ]);

print($layout_content);