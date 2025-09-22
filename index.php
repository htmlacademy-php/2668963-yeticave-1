<?php
date_default_timezone_set("Europe/Samara");
require_once 'helpers.php';
$db = require_once 'db.php';

$isAuth = rand(0, 1);
$userName = 'Gera'; // укажите здесь ваше имя

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, 'utf8');

if (!$link) {
    $error = mysqli_connect_error();
    $contant = include_remplates('error.php', ['error' => $error]);
} else {
    /* пишем текст запроса */
    $sql = 'SELECT * FROM categories';
    /* получаем все категории */
    $result = mysqli_query($link, $sql);
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql = 'SELECT l.title, start_price, img_url, bet_step, l.expiration_date, c.title AS category FROM lots l '
         . 'JOIN categories c ON category_id = c.id '
         . 'WHERE expiration_date > CURDATE() '
         . 'ORDER BY date_add DESC LIMIT 6';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $ads = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}


function formatPrice($price) {
    $price = ceil($price);

    if ($price < 1000) {
        return $price." ₽";
    }

    return number_format($price, 0, '', ' ')." ₽";
}

function getTimeToDate($date) {
    $currentDate = date_create("now");
    $finishDate = date_create($date);
    $remainingTime = date_diff($currentDate, $finishDate);

    $hours = $remainingTime->days * 24 + $remainingTime->h;
    $minutes = $remainingTime->i;
     
    return [$hours, $minutes];
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
