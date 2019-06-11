<?php

require_once("helpers.php");
require_once("database.php");

session_start();

$user_id = $_SESSION["id"];
$is_auth = false;
$user_name = "";

if (isset($user_id)) {
    $is_auth = true;
    $user_name = get_user_name_from_id($database_connection, $user_id);    
}

//получение всех ставок пользователя
$sql_bets_by_id = "SELECT price, dt_bet, lot_name, lot_image_src, 
    cat_name, dt_end, lot_id
    FROM bets b
    LEFT JOIN lots l ON lot_id = l.id
    LEFT JOIN categories c ON category_code = character_code 
    WHERE better_id=?
    ORDER BY dt_bet DESC;";
$stmt_bets = mysqli_prepare($database_connection, $sql_bets_by_id);
mysqli_stmt_bind_param($stmt_bets, 'i', $user_id);
mysqli_stmt_execute($stmt_bets);
$sql_bets_by_id_result = mysqli_stmt_get_result($stmt_bets);
if ($sql_bets_by_id_result) {
    $my_bets = mysqli_fetch_all($sql_bets_by_id_result, MYSQLI_ASSOC);
}

//формирую основной контент тега <main>
$content = include_template('my-bets-main.php', 
    [
    'categories'             => $categories,
    'my_bets'                => $my_bets
    ]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Мои ставки", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => $search
    ]);

print($layout_content);