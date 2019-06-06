<?php

require_once("helpers.php");
require_once("database.php");

//формирую основной контент тега <main>
$content = include_template('content-404.php', 
    [
    'categories' => $categories 
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

