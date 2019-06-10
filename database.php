<?php

$database_connection = mysqli_connect("localhost", "root", "", "yeticave");

if (!$database_connection) {
    print_r("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
mysqli_set_charset($database_connection, "utf8");

// получение списка категорий
$sql_categories = "SELECT cat_name, character_code FROM categories";
$categories_object = mysqli_query($database_connection, $sql_categories);
$categories = mysqli_fetch_all($categories_object, MYSQLI_ASSOC);

// получение списка из девяти самых новых лотов
$sql_lots = "SELECT l.id AS lot_id, lot_name, start_price, lot_image_src, 
    current_price, category_code, dt_add, cat_name, dt_end
FROM lots l
LEFT JOIN categories
ON category_code = character_code
WHERE dt_end > NOW()
ORDER BY dt_add DESC
LIMIT 9;";
$lots_object = mysqli_query($database_connection, $sql_lots);
$stuff = mysqli_fetch_all($lots_object, MYSQLI_ASSOC);