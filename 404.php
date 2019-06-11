<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

//формирую основной контент тега <main>
$content = include_template('content-404.php', 
    [
    'categories' => $categories 
    ]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => "Ошибка", 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    'search'     => $search
    ]);

print($layout_content);