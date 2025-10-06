<?php

require_once 'helpers.php';
require_once 'db_functions.php';
$db = require_once 'db.php'; // Подключение файла доступа к БД


date_default_timezone_set("Europe/Samara"); // Выставление часового пояса
# Заглушка логина
$isAuth = rand(0, 1);
$userName = 'Gera'; // укажите здесь ваше имя


$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, 'utf8');
if (!$link) {
    http_response_code(400);
    echo mysqli_connect_error();
    exit;
}

$categories = getCategories($link);
$categoryId = array_column($categories, 'id');

$source = $_GET['source'] ?? null;
switch ($source) {

    case 'add':
        $pageContent = include_template('add.php',[
            'link' => $link,
            'categories' => $categories,
            'categoryId' => $categoryId    
        ]);

        break;
    case 'lot':     
        
        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit($id)) {
            http_response_code(404);
            echo "404 — Лот не найден";
            exit;
        }
       
        $ad = getAd($link, $id);
        if (!$ad) {
            http_response_code(404);
            echo "404 — Лот не найден";
            exit;
        }

        $bet = getMaxBet($link, $id);  
        $pageContent = include_template('lot.php',[
            'ad' => $ad,
            'bet' => $bet
        ]);

        break;
    default:

        $ads = getAdsList($link);        
        $pageContent = include_template('main.php',[
            'categories' => $categories,
            'ads' => $ads
        ]);
}


$layoutContent = include_template('layout.php', [
    'content' => $pageContent, 
    'title' => 'Главная',
    'isAuth' => $isAuth,
    'userName' => $userName,
    'categories' => $categories
]);

print($layoutContent);
