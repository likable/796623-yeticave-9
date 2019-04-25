<?php

require_once("helpers.php");

$is_auth = rand(0, 1);

$user_name = 'Виталий'; // укажите здесь ваше имя

$categories = [
    "Доски и лыжи", 
    "Крепления", 
    "Ботинки", 
    "Одежда", 
    "Инструменты", 
    "Разное"
];

$stuff = [
    [
        "title" => "2014 Rossignol District Snowboard",
        "cat"   => "Доски и лыжи",
        "price" => 10999,
        "url"   => "img/lot-1.jpg"
    ],
    [
        "title" => "DC Ply Mens 2016/2017 Snowboard",
        "cat"   => "Доски и лыжи",
        "price" => 159999,
        "url"   => "img/lot-2.jpg"
    ],
    [
        "title" => "Крепления Union Contact Pro 2015 года размер L/XL",
        "cat"   => "Крепления",
        "price" => 8000,
        "url"   => "img/lot-3.jpg"
    ],
    [
        "title" => "Ботинки для сноуборда DC Mutiny Charocal",
        "cat"   => "Ботинки",
        "price" => 10999,
        "url"   => "img/lot-4.jpg"
    ],
    [
        "title" => "Куртка для сноуборда DC Mutiny Charocal",
        "cat"   => "Одежда",
        "price" => 7500,
        "url"   => "img/lot-5.jpg"
    ],
    [
        "title" => "Маска Oakley Canopy",
        "cat"   => "Разное",
        "price" => 5400,
        "url"   => "img/lot-6.jpg"
    ]
];

function get_price_formatting($unformatted_price) {
    $ceil_unformatted_price = ceil($unformatted_price);
    $formatted_price = number_format($ceil_unformatted_price, 0, '.', ' ');
    $formatted_price .= " ₽";
    return $formatted_price;
}

//формирую основной контент тега <main>
$content = include_template('index.php', 
    ['categories' => $categories, 'stuff' => $stuff]);

//формирую layout
$layout_content = include_template('layout.php', 
    [
    'title'      => 'Title', 
    'is_auth'    => $is_auth, 
    'user_name'  => $user_name, 
    'content'    => $content, 
    'categories' => $categories,
    ]);

print($layout_content);

?>
