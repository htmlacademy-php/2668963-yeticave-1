<?php

date_default_timezone_set("Europe/Samara"); // Выставление часового пояса

require_once 'vendor/autoload.php';
require_once 'helpers.php';
require_once 'db_functions.php';
require_once 'formHelper.php';
$db = require_once 'db.php'; // Подключение файла доступа к БД


session_start();

if (isset($_SESSION['username'])) {
    $userName = $_SESSION['username'];
    $userId = $_SESSION['id']; 
    $userEmail = $_SESSION['email']; 
}


$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, 'utf8');
if (!$link) {
    http_response_code(400);
    echo mysqli_connect_error();
    exit;
}

$categories = getCategories($link);
$categoriesIds = array_column($categories, 'id');
$usersEmailsList = getUsersEmails($link);


function parseMaxBets($link, $userId, $bets) {
    
    $maxBets = [];
    foreach ($bets as $bet) {
        $maxBets[$bet["lot_id"]] = getMaxBet($link, $bet["lot_id"]);
    }
    return $maxBets;

}

$source = $_GET['source'] ?? null;


require_once 'getwinner.php';

switch ($source) {

    case 'sign-up':
        if (isset($_SESSION['username'])) {
            http_response_code(403);
            echo "Нет доступа";
            exit;
        }
        $errors = regFormValidate($link, $usersEmailsList);
        $pageContent = include_template('sign-up.php',[
            'errors' => $errors
        ]);
        break;

    case 'login':
        $errors = loginFormValidate($link, $usersEmailsList);
        $pageContent = include_template('login.php',[
            'errors' => $errors
        ]);
        break;

    case 'logout':
        $pageContent = include_template('logout.php',[

        ]);
        break;
    
    case 'my-bets':
        $bets = getUserBets($link, $userId);
        $maxBets = parseMaxBets($link, $userId, $bets);

        $pageContent = include_template('my-bets.php',[
            'bets' => $bets,
            'maxBets' => $maxBets
        ]);
        break;

    case 'add':
        if (!isset($_SESSION['username'])) {
            http_response_code(403);
            echo "Нет доступа";
            exit;
        }
        $errors = addLotFormValidate($link, $categoriesIds, $userId);
        $pageContent = include_template('add.php',[
            'categories' => $categories,
            'errors' => $errors
        ]);
        break;

    case 'lots-by-category':     
        $totalLots = countAdsListByCategory($link);

        $limit = 9; // лотов на странице
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $offset = ($currentPage - 1) * $limit;
        $totalPages = ceil($totalLots / $limit);

        $ads = getAdsListByCategory($link, $offset, $limit);

        $pageContent = include_template('lots-by-category.php',[
            'categories' => $categories,
            'ads' => $ads,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
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
        $lotBets = getLotBets($link, $id);

        $errors = addBetFormValidate($link, $bet, $lotBets);

        $pageContent = include_template('lot.php',[
            'ad' => $ad,
            'bet' => $bet,
            'lotBets' => $lotBets,
            'errors' => $errors
        ]);
        break;

    case 'search-page':

        $totalLots = countFoundAds($link);

        $limit = 9; // лотов на странице
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $offset = ($currentPage - 1) * $limit;
        $totalPages = ceil($totalLots / $limit);

        $ads = getFoundAds($link, $offset, $limit);

        $pageContent = include_template('search-page.php',[
            'ads' => $ads,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
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
    'userName' => $userName ?? null,
    'categories' => $categories
]);

print($layoutContent);
