<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

if(session_id() == '') {
    session_start();
}

if (isset($_SESSION["id"])) {
   $user_id = $_SESSION["id"];
}

$is_auth = false;
$user_name = "";

if (isset($user_id)) {
    $is_auth = true;
    $user_name = get_user_name_from_id($database_connection, $user_id);    
}
else {
    header("Location: /404.php?err=403");
}

$errors = [];

//проверка отправления формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //создание массива обязательных к заполнению полей
    $required = ["lot-name" => "Введите наименование лота",
        "category" => "Выберите категорию",
        "message"  => "Напишите описание лота",
        "lot-rate" => "Введите начальную цену",
        "lot-step" => "Введите шаг ставки", 
        "lot-date" => "Введите дату завершения торгов"];
    
    //сохранение введённых в поля значений
    $lot_name = htmlspecialchars($_POST["lot-name"]) ?? "";
    $post_cat = htmlspecialchars($_POST["category"]);
    $message  = htmlspecialchars($_POST["message"]) ?? "";
    $lot_rate = htmlspecialchars($_POST["lot-rate"]) ?? "";
    $lot_step = htmlspecialchars($_POST["lot-step"]) ?? "";
    $lot_date = htmlspecialchars($_POST["lot-date"]) ?? "";

    //заполняю массив ошибок
    foreach ($required as $field_name => $error_text) {
        //проверка на заполненность полей
        if (empty($_POST[$field_name]) || 
                $_POST[$field_name] === "Выберите категорию") {
            $errors[$field_name] = $error_text;
        }
        //проверки правильности заполнения полей
        elseif ($field_name == "lot-rate") {
            if (!is_numeric($_POST[$field_name]) || 
                    $_POST[$field_name] <= 0 || 
                    !is_int($_POST[$field_name] + 0)) {
                $errors[$field_name] = 
                        "Цена должна быть целым положительным числом";
            }
        }
        elseif ($field_name == "lot-step") {
            if (!is_numeric($_POST[$field_name]) || 
                    $_POST[$field_name] <= 0 || 
                    !is_int($_POST[$field_name] + 0)) {
                $errors[$field_name] = 
                        "Шаг ставки должен быть целым положительным числом";
            }
        }
        elseif ($field_name == "lot-date") {
            if (!is_date_valid($_POST[$field_name]) || 
                    strtotime($_POST[$field_name]) < strtotime("+1 day")) {
                $errors[$field_name] = 
                        "Дата окончания торгов должна отличаться от текущей".
                        " минимум на один день и быть в формате ГГГГ-ММ-ДД";
            }
        }
        elseif ($field_name == "lot-name") {
            if (strlen($_POST[$field_name]) > 127) {
                $errors[$field_name] = "Наименование лота слишком длинное";
            }
        }
    }
        
    //проверка файла изображения
    $file = $_FILES["lot-img"];
    $name = $file["name"];
    $file_choosed = !empty($name);
    if ($file_choosed) {
        $tmp_name = $file["tmp_name"];
        $file_type = mime_content_type($tmp_name);
        if ($file_type !== "image/png" && $file_type !== "image/jpeg") {
            $errors["file"] = "Формат выбранного изображения не jpg, jpeg, png";
        }
        else {
            $path = "uploads/" . time() . $name;
        }                    
    }
    else {
        $errors["file"] = "Загрузите картинку в формате jpg, jpeg или png";
    }
   
    //валидация
    if (count($errors)) {
        //формирую основной контент тега <main> --> ошибки заполнения формы
        $content = include_template('add-lot.php', 
            [
            'categories' => $categories, 
            'errors'     => $errors,
            'lot_name'   => $lot_name,
            'post_cat'   => $post_cat,
            'message'    => $message,
            'lot_rate'   => $lot_rate,
            'lot_step'   => $lot_step,
            'lot_date'   => $lot_date               
            ]);
    }    
    else {
        //нет ошибок формы
        //перемещение изображения из временной папки в постоянную
        move_uploaded_file($tmp_name, $path);
        
        //приведение данных к формату полей БД
        $int_rate = (int) $lot_rate;
        $int_step = (int) $lot_step;
        
        //преобразование категории лота к формату поля БД
        $sql_cat = "SELECT character_code FROM categories "
                . "WHERE cat_name=?;";
        $stmt_cat = mysqli_prepare($database_connection, $sql_cat);
        mysqli_stmt_bind_param($stmt_cat, 's', $post_cat);
        mysqli_stmt_execute($stmt_cat);
        $sql_cat_result = mysqli_stmt_get_result($stmt_cat);
        if ($sql_cat_result) {
            $cat = mysqli_fetch_assoc($sql_cat_result)["character_code"];
        }
        
        //добавление лота в БД
        $sql_add_lot = "INSERT INTO lots "
                . "(lot_name, description, lot_image_src, start_price, "
                . "dt_end, price_step, author_id, category_code) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        
        $stmt = mysqli_prepare($database_connection, $sql_add_lot);
        
        if ($stmt === false) {
            $errorMsg = "STMT error: " . mysqli_error($database_connection);
            die($errorMsg);
        }
        
        mysqli_stmt_bind_param($stmt, 'sssisiis', $lot_name, $message, $path,
                $int_rate, $lot_date, $int_step, $user_id, $cat);
        
        mysqli_stmt_execute($stmt);
        
        if (mysqli_errno($database_connection) > 0) {
            $errorMsg = 'Неувязочка: ' . mysqli_error($database_connection);
            die($errorMsg);
        }

        //получение id добавленного лота
        $sql_new_id = "SELECT id FROM lots WHERE lot_image_src=?;";
        $stmt_id = mysqli_prepare($database_connection, $sql_new_id);
        mysqli_stmt_bind_param($stmt_id, 's', $path);
        mysqli_stmt_execute($stmt_id);
        $sql_new_id_result = mysqli_stmt_get_result($stmt_id);
        if ($sql_new_id_result) {
            $new_id = mysqli_fetch_assoc($sql_new_id_result)["id"];
        }
        
        //редирект на новый лот
        header("Location: /lot.php?id=".$new_id);
    }    
}
else {    
    //формирую основной контент тега <main> --> первоначальное состояние формы
    $content = include_template('add-lot.php', 
        [
        'categories' => $categories, 
        'errors'     => [],
        'lot_name'   => '',
        'post_cat'   => '',
        'message'    => '',
        'lot_rate'   => '',
        'lot_step'   => '',
        'lot_date'   => ''
        ]);
}

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Добавление лота", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => ''
    ]);

print($layout_content);