<?php

require_once 'helpers.php';

$isAuth = rand(0, 1);
$userName = 'Gera'; // укажите здесь ваше имя
$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$ads = 
[
["name"=>"2014 Rossignol District Snowboard", "cat"=>$categories[0], "price"=>10999, "urlImg"=>"img/lot-1.jpg"],
["name"=>"DC Ply Mens 2016/2017 Snowboard", "cat"=>$categories[0], "price"=>159999, "urlImg"=>"img/lot-2.jpg"],
["name"=>"Крепления Union Contact Pro 2015 года размер L/XL", "cat"=>$categories[1], "price"=>8000, "urlImg"=>"img/lot-3.jpg"],
["name"=>"Ботинки для сноуборда DC Mutiny Charocal", "cat"=>$categories[2], "price"=>10999, "urlImg"=>"img/lot-4.jpg"],
["name"=>"Куртка для сноуборда DC Mutiny Charocal", "cat"=>$categories[3], "price"=>7500, "urlImg"=>"img/lot-5.jpg"],
["name"=>"Маска Oakley Canopy", "cat"=>$categories[5], "price"=>5400, "urlImg"=>"img/lot-6.jpg"]
];

function formatPrice($price) {
    $price = ceil($price);

    if ($price < 1000) {
        return $price." ₽";
    }

    return number_format($price, 0, '', ' ')." ₽";
}


$pageContent = include_template('main.php',[
    'categories' => $categories,
    'ads' => $ads
]);

$layoutContent = include_template('layout.php', [
    'content' => $pageContent, 
    'title' => 'Главная',
    'isAuth' => $isAuth,
    'userName' => $userName,
    'categories' => $categories
]);

print($layoutContent);



