<?php

require_once("helpers.php");
require_once("database.php");

//Проверка параметра запроса
if (empty($_GET["id"])) {
    header("Location: 404.php");    
}
else {
    $id = htmlspecialchars($_GET["id"]);
}

// получение лота по его id, если он ещё актуален
$sql_lot_by_id = "SELECT l.id AS lot_id, lot_name, description, start_price, 
    lot_image_src, current_price, category_code, dt_add, cat_name, dt_end
FROM lots l
LEFT JOIN categories
ON category_code = character_code
WHERE dt_end > NOW()
AND l.id=".$id.";";
$lot_by_id_object = mysqli_query($database_connection, $sql_lot_by_id);
$lot_info = mysqli_fetch_assoc($lot_by_id_object);

//проверка наличия записи в БД
if ($lot_info === NULL) {
    header("Location: 404.php");
}

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