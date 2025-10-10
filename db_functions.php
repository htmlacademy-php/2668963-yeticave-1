<?php

function getCategories(mysqli $link): array
{
    $sql = 'SELECT * FROM categories';
    $result = mysqli_query($link, $sql);
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function getUsersEmails(mysqli $link): array
{
    $sql = 'SELECT email FROM users';
    $result = mysqli_query($link, $sql);
    
    return $result ? array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'email') : [];
}


function getAdsList(mysqli $link): array
{
    $sql = 'SELECT l.id, l.title, start_price, img_url, l.expiration_date, c.title AS category FROM lots l '
                . 'JOIN categories c ON category_id = c.id '
                . 'WHERE expiration_date > CURDATE() '
                . 'ORDER BY date_add DESC LIMIT 6';
    $result = mysqli_query($link, $sql);
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function getAd(mysqli $link, int $id): ?array
{
    $sql = 'SELECT l.id, l.title, start_price, bet_step, img_url, l.expiration_date, c.title AS category, about FROM lots l '
                . 'JOIN categories c ON category_id = c.id '
                . 'WHERE l.id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}


function getMaxBet(mysqli $link, int $id): ?array
{
    $sql = 'SELECT l.id, l.title, start_price, bet_step, COALESCE(MAX(b.amount), start_price) AS current_price '
                . 'FROM lots l '
                . 'LEFT JOIN bets b ON b.lot_id = l.id '
                . 'WHERE l.id = ? GROUP BY l.id';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}