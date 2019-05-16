<?php

require_once("helpers.php");

//Проверка параметра запроса
if (isset($_GET["id"])) {
    $id = htmlspecialchars($_GET["id"]);
}
else {
    header("Location: pages/404.html");
}

//соединение с базой данных и установка кодировки
$database_connection = mysqli_connect("localhost", "root", "", "yeticave");
if ($database_connection == false) {
    print_r("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
mysqli_set_charset($database_connection, "utf8");

// получение списка актуальных лотов
$sql_lots = "SELECT l.id AS lot_id, lot_name, description, start_price, 
    lot_image_src, current_price, category_code, dt_add, cat_name, dt_end
FROM lots l
LEFT JOIN categories
ON category_code = character_code
WHERE dt_end > NOW()
AND l.id=".$id.";";
$lots_object = mysqli_query($database_connection, $sql_lots);
$lot_info = mysqli_fetch_all($lots_object, MYSQLI_ASSOC)[0];

//проверка наличия записи в БД
if ($lot_info === NULL) {
    header("Location: pages/404.html");
}

// получение списка категорий
$sql_categories = "SELECT cat_name, character_code FROM categories";
$categories_object = mysqli_query($database_connection, $sql_categories);
$categories = mysqli_fetch_all($categories_object, MYSQLI_ASSOC);

//functions from helpers.php
$time_to_lot_expiration = get_time_to_expiration($lot_info["dt_end"]);
$is_time_finishing = is_time_to_midnight_finishing($time_to_lot_expiration);

//формирую основной контент тега <main> --> описание лота
$content = include_template('lotinfo.php', 
    [
    'categories' => $categories, 
    'lot_info' => $lot_info,
    'time_to_lot_expiration' => $time_to_lot_expiration,
    'is_time_finishing' => $is_time_finishing
    ]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => $lot_info["lot_name"], 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories
    ]);

print($layout_content);