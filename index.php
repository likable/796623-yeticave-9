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

//формирую основной контент тега <main>
$content = include_template('index.php', 
    [
    'categories' => $categories, 
    'stuff' => $stuff,
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