<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

$errors = [];

//проверка отправления формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //создание массива обязательных к заполнению полей
    $required = ["email" => "Введите адрес вашей электронной почты",
                 "password" => "Введите пароль"
                ];
    
    //сохранение введённых в поля значений
    $email     = htmlspecialchars($_POST["email"]) ?? "";
    $password  = htmlspecialchars($_POST["password"]) ?? "";

    //заполняю массив ошибок
    foreach ($required as $field_name => $error_text) {
        //проверка на заполненность полей
        if (empty($_POST[$field_name])) {
            $errors[$field_name] = $error_text;
        }
        //проверка правильности заполнения адреса электронной почты
        elseif ($field_name == "email") {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[$field_name] = "Введён некорректный e-mail";
            }            
        }
    }

    //получение данных пользователя по почтовому адресу        
    $sql_user = "SELECT * FROM users WHERE email=?;";
    $stmt = mysqli_prepare($database_connection, $sql_user);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $sql_user_result = mysqli_stmt_get_result($stmt);
    if ($sql_user_result) {
        $user_data = mysqli_fetch_assoc($sql_user_result);
    }
    
    //проверка существования пользователя с введённым email в БД 
    if (isset($user_data)) {
        $pass_hash = $user_data["password"];
    }
    elseif (empty($errors["email"])) {        
        $errors["email"] = "Пользователя с таким e-mail не обнаружено";
    }
    
    //проверка пароля на соответствие 
    $is_password_match = password_verify($password, $pass_hash);
    if (!$is_password_match && empty($errors["password"])) {
        $errors["password"] = "Вы ввели неверный пароль";
    }
    
    //валидация
    if (count($errors)) {
        //формирую основной контент тега <main> --> ошибки заполнения формы
        $content = include_template('login-main.php', 
            [
            'categories' => $categories, 
            'errors'     => $errors,
            'email'      => $email,
            'password'   => $password             
            ]);
    }    
    else {
        //нет ошибок формы
        //открываю сессию
        session_start();
        $_SESSION["id"] = $user_data["id"];
        
        //редирект на главную страницу
        header("Location: /index.php");
        
    } 
    //*/
}
else {    
    //формирую основной контент тега <main> --> первоначальное состояние формы
    $content = include_template('login-main.php', 
        [
        'categories' => $categories,
        'errors'     => []
        ]);
}

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Вход", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => $search
    ]);

print($layout_content);