<?php

require_once("helpers.php");
require_once("database.php");

$is_auth = rand(0, 1);
$user_name = 'Виталий'; // укажите здесь ваше имя

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

?>
