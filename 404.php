<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

if (session_id() == '') {
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

$is_forbidden = false;
if (isset($_GET["err"])) {
    $is_forbidden = true;
    http_response_code(403);
}

//формирую основной контент тега <main>
$content = include_template('content-404.php', 
    [
    'categories'   => $categories,
    'is_forbidden' => $is_forbidden
    ]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Ошибка", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => ''
    ]);

print($layout_content);