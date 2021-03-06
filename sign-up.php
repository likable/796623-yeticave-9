<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

$errors = [];

//проверка отправления формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //создание массива обязательных к заполнению полей
    $required = ["email" => "Введите адрес вашей электронной почты",
                 "password" => "Введите пароль",
                 "name"  => "Введите ваше имя",
                 "message" => "Напишите как с вами связаться"
                ];
    
    //сохранение введённых в поля значений
    $email     = htmlspecialchars($_POST["email"]) ?? "";
    $password  = htmlspecialchars($_POST["password"]) ?? "";
    $firstname = htmlspecialchars($_POST["name"]) ?? "";
    $message   = htmlspecialchars($_POST["message"]) ?? "";

    //заполняю массив ошибок
    foreach ($required as $field_name => $error_text) {
        //проверка на заполненность полей
        if (empty($_POST[$field_name])) {
            $errors[$field_name] = $error_text;
        }
        //проверка правильности заполнения адреса электронной почты
        elseif ($field_name == "email") {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[$field_name] = "Введён некорректный email";
            }
        }
    }
     
    //проверка на существование адреса почты в БД
    $sql_get_email = "SELECT * FROM users WHERE email=?;";
    $stmt_get_email = mysqli_prepare($database_connection, $sql_get_email);
    mysqli_stmt_bind_param($stmt_get_email, 's', $email);
    mysqli_stmt_execute($stmt_get_email);
    $sql_get_email_result = mysqli_stmt_get_result($stmt_get_email);
    if ($sql_get_email_result) {
        $old_email = mysqli_fetch_assoc($sql_get_email_result);
    }
    if (isset($old_email)) {
        $errors["email"] = "email занят другим пользователем";
    }
    
    //валидация
    if (count($errors)) {
        //формирую основной контент тега <main> --> ошибки заполнения формы
        $content = include_template('sign-up-main.php', 
            [
            'categories' => $categories, 
            'errors'     => $errors,
            'email'      => $email,
            'password'   => $password,
            'firstname'  => $firstname,
            'message'    => $message              
            ]);
    }    
    else {
        //нет ошибок формы
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $avatar_src = "avatar src";
              
        //добавление пользователя в БД
        $sql_add_user = "INSERT INTO users "
                . "(email, user_name, password, avatar_src, contacts) "
                . "VALUES (?, ?, ?, ?, ?);";
        
        $stmt = mysqli_prepare($database_connection, $sql_add_user);
        
        if ($stmt === false) {
            $errorMsg = "STMT error: " . mysqli_error($database_connection);
            die($errorMsg);
        }
        
        mysqli_stmt_bind_param($stmt, 'sssss', $email, $firstname, 
                $password_hash, $avatar_src, $message);
        
        mysqli_stmt_execute($stmt);
        
        if (mysqli_errno($database_connection) > 0) {
            $errorMsg = 'Неувязочка: ' . mysqli_error($database_connection);
            die($errorMsg);
        }
        
        //редирект на страницу входа
        header("Location: /login.php");
    }    
}
else {    
    //формирую основной контент тега <main> --> первоначальное состояние формы
    $content = include_template('sign-up-main.php', 
        [
        'categories' => $categories, 
        'errors'     => [],
        'email'      => '',
        'password'   => '',
        'firstname'  => '',
        'message'    => ''
        ]);
}

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Регистрация", 
    'is_auth'    => false, 
    'user_name'  => '', 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => ''
    ]);

print($layout_content);