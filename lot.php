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

//Проверка параметра запроса
if (empty($_GET["id"])) {
    header("Location: 404.php");
}
else {
    $id = htmlspecialchars($_GET["id"]);
    $int_id = (int) $id;
}

//получение лота по его id, если он ещё актуален
$sql_lot_by_id = "SELECT l.id AS lot_id, lot_name, description, start_price, 
    lot_image_src, current_price, category_code, dt_add, cat_name, dt_end,
    price_step
    FROM lots l
    LEFT JOIN categories
    ON category_code = character_code
    WHERE dt_end > NOW()
    AND l.id=?;";
$stmt = mysqli_prepare($database_connection, $sql_lot_by_id);
mysqli_stmt_bind_param($stmt, 'i', $int_id);
mysqli_stmt_execute($stmt);
$sql_lot_by_id_result = mysqli_stmt_get_result($stmt);
if ($sql_lot_by_id_result) {
    $lot_info = mysqli_fetch_assoc($sql_lot_by_id_result);
    $price_step = $lot_info["price_step"];
    $current_price = $lot_info["current_price"] ?? $lot_info["start_price"];
}

$min_bet = $current_price + $price_step;

//проверка наличия записи в БД
if ($lot_info === NULL) {
    header("Location: 404.php");
}

//получение всех ставок к этому лоту
$sql_lot_bets = "SELECT user_name, price, dt_bet 
    FROM bets b 
    LEFT JOIN users u ON better_id = u.id 
    WHERE lot_id=? 
    ORDER BY dt_bet DESC
    LIMIT 10;";
$stmt_bets = mysqli_prepare($database_connection, $sql_lot_bets);
mysqli_stmt_bind_param($stmt_bets, 'i', $int_id);
mysqli_stmt_execute($stmt_bets);
$sql_bets_result = mysqli_stmt_get_result($stmt_bets);
if ($sql_bets_result) {
    $lot_bets = mysqli_fetch_all($sql_bets_result, MYSQLI_ASSOC);
}

//functions from helpers.php
$time_to_lot_expiration = get_time_to_expiration($lot_info["dt_end"]);
$is_time_finishing = is_time_to_midnight_finishing($time_to_lot_expiration);

$errors = [];

//проверка отправления формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //создание массива обязательных к заполнению полей
    $required = ["cost" => "Заполните это поле"];
    
    //сохранение введённых в поля значений
    $cost = htmlspecialchars($_POST["cost"]) ?? "";

    //заполняю массив ошибок
    foreach ($required as $field_name => $error_text) {
        //проверка на заполненность полей
        if (empty($_POST[$field_name])) {
            $errors[$field_name] = $error_text;
        }
        //проверки правильности заполнения полей
        elseif ($field_name == "cost") {
            if (!is_numeric($_POST[$field_name]) || 
                    $_POST[$field_name] <= 0 || 
                    !is_int($_POST[$field_name] + 0)) {
                $errors[$field_name] = 
                    "Цена должна быть целым положительным числом";
            }
            elseif ($_POST[$field_name] < $min_bet) {
                $errors[$field_name] = 
                    "Ваша ставка меньше минимальной";
            }
        }        
    }
           
    //валидация
    if (count($errors)) {
        //формирую основной контент тега <main> --> ошибки заполнения формы
        $content = include_template('lotinfo.php', 
            [
            'categories'             => $categories, 
            'errors'                 => $errors,
            'cost'                   => $cost,
            'lot_info'               => $lot_info,
            'time_to_lot_expiration' => $time_to_lot_expiration,
            'is_time_finishing'      => $is_time_finishing,
            'is_auth'                => $is_auth,
            'min_bet'                => $min_bet,
            'current_price'          => $current_price,
            'id'                     => $id,
            'lot_bets'               => $lot_bets
            ]);
    }    
    else {
        //нет ошибок формы
        //приведение данных к формату полей БД
        $int_cost = (int) $cost;
        
        //добавление ставки в БД в таблицу ставок
        $sql_add_bet = "INSERT INTO bets "
                . "(price, better_id, lot_id) "
                . "VALUES (?, ?, ?);";

        $stmt_bet = mysqli_prepare($database_connection, $sql_add_bet);

        if ($stmt_bet === false) {
            $errorMsg = "STMT error: " . mysqli_error($database_connection);
            die($errorMsg);
        }

        mysqli_stmt_bind_param($stmt_bet, 'iii', $int_cost, $user_id, 
                $int_id);
        mysqli_stmt_execute($stmt_bet);

        if (mysqli_errno($database_connection) > 0) {
            $errorMsg = 'Неувязочка: ' . mysqli_error($database_connection);
            die($errorMsg);
        }
        
        //обновление актуальной цены лота
        $sql_add_bet_into_lot = "UPDATE lots SET current_price=? "
                . "WHERE id=?;";
        $stmt_bet_into_lot = mysqli_prepare($database_connection, 
                $sql_add_bet_into_lot);

        if ($stmt_bet_into_lot === false) {
            $errorMsg = "STMT error: " . mysqli_error($database_connection);
            die($errorMsg);
        }

        mysqli_stmt_bind_param($stmt_bet_into_lot, 'ii', $int_cost, $int_id);
        mysqli_stmt_execute($stmt_bet_into_lot);

        if (mysqli_errno($database_connection) > 0) {
            $errorMsg = 'Неувязочка: ' . mysqli_error($database_connection);
            die($errorMsg);
        }
        
        //редирект на этот же лот
        header("Location: /lot.php?id=".$id);        
    }    
}
else {    
    //формирую основной контент тега <main> --> первоначальное состояние формы
    $content = include_template('lotinfo.php', 
        [
        'categories'             => $categories,
        'errors'                 => [],
        'cost'                   => $cost,
        'lot_info'               => $lot_info,
        'time_to_lot_expiration' => $time_to_lot_expiration,
        'is_time_finishing'      => $is_time_finishing,
        'is_auth'                => $is_auth,
        'min_bet'                => $min_bet,
        'current_price'          => $current_price,
        'id'                     => $id,
        'lot_bets'               => $lot_bets
        ]);
}

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