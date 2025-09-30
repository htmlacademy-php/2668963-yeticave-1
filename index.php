<?php
require_once 'helpers.php';

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, 'utf8');

if (!$link) {
    $error = mysqli_connect_error();
    $contant = include_templates('error.php', ['error' => $error]);
} else {
    /* пишем текст запроса */
    $sql = 'SELECT * FROM categories';
    /* получаем все категории */
    $result = mysqli_query($link, $sql);
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql = 'SELECT l.id, l.title, start_price, img_url, l.expiration_date, c.title AS category FROM lots l '
         . 'JOIN categories c ON category_id = c.id '
         . 'WHERE expiration_date > CURDATE() '
         . 'ORDER BY date_add DESC LIMIT 6';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $ads = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

if (!$link) {
    $error = mysqli_connect_error();
    $contant = include_templates('error.php', ['error' => $error]);
} else {

    if (isset($_GET['source'])) {
        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit($id)) {
            http_response_code(404);
            echo "404 — Лот не найден";
            exit;
        }
    }

    $sql = 'SELECT l.id, l.title, start_price, bet_step, img_url, l.expiration_date, c.title AS category, about FROM lots l '
         . 'JOIN categories c ON category_id = c.id '
         . 'WHERE l.id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_GET['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ad = mysqli_fetch_assoc($result);

    $sql = 'SELECT l.id, l.title, start_price, bet_step, COALESCE(MAX(b.amount), start_price) AS current_price '
         . 'FROM lots l '
         . 'LEFT JOIN bets b ON b.lot_id = l.id '
         . 'WHERE l.id = ? GROUP BY l.id';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_GET['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $bet = mysqli_fetch_assoc($result);
}

if (isset($_GET['source']) && $_GET['source'] === 'lot') {
    if (!$ad) {
        http_response_code(404);
        echo "404 — Лот не найден";
        exit;
    }
    $pageContent = include_template('lot.php',[
        'ad' => $ad,
        'bet' => $bet
    ]);
} else {
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
