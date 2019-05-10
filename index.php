<?php

require_once("helpers.php");

$is_auth = rand(0, 1);

$user_name = 'Виталий'; // укажите здесь ваше имя

//helpers
$database_connection = mysqli_connect("localhost", "root", "", "yeticave");

if ($database_connection == false) {
    print_r("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
else {
    //print_r("Соединение с базой данных установлено.");
}

mysqli_set_charset($database_connection, "utf8");


// получение списка новых лотов
$sql_lots = "SELECT lot_name, start_price, lot_image_src, 
    current_price, category_code, dt_add, cat_name
FROM lots
LEFT JOIN categories
ON category_code = character_code
WHERE dt_end > NOW()
ORDER BY dt_add DESC
LIMIT 9;";
$lots_object = mysqli_query($database_connection, $sql_lots);
$stuff = mysqli_fetch_all($lots_object, MYSQLI_ASSOC);

// получение списка категорий
$sql_categories = "SELECT cat_name, character_code FROM categories";
$categories_object = mysqli_query($database_connection, $sql_categories);
$categories = mysqli_fetch_all($categories_object, MYSQLI_ASSOC);


function get_price_formatting($unformatted_price) {
    $ceil_unformatted_price = ceil($unformatted_price);
    $formatted_price = number_format($ceil_unformatted_price, 0, '.', ' ');
    $formatted_price .= " ₽";
    return $formatted_price;
}

function get_time_to_midnight() {
    $time_now = date_create("now");
    $time_midnight = date_create("tomorrow");
    $time_difference = date_diff($time_now, $time_midnight);
    $time_formatted = date_interval_format($time_difference, "%H:%I");
    return $time_formatted;
}

$time_to_midnight = get_time_to_midnight();

function is_time_to_midnight_finishing($time) {
    $time = str_replace(":", "", $time);
    $time = (int)$time;
    if ($time <= 100) {
        return true;
    }
    return false;
}

$is_time_finishing = is_time_to_midnight_finishing($time_to_midnight);

//формирую основной контент тега <main>
$content = include_template('index.php', 
    [
    'categories' => $categories, 
    'stuff' => $stuff,
    'time_to_midnight' => $time_to_midnight,
    'is_time_finishing' => $is_time_finishing
    ]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => 'Title', 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories
    ]);

print($layout_content);

?>
