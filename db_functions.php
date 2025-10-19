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


function countFoundAds(mysqli $link): int
{
    $search = trim($_GET['search'] ?? '');
    
    $sql = 'SELECT COUNT(*) AS total FROM lots WHERE MATCH(title, about) AGAINST(?) AND expiration_date > CURDATE()';
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'];
}


function getFoundAds(mysqli $link, int $offset, int $limit): array
{
    $search = trim($_GET['search'] ?? '');

    if ($search) {
        $sql = 'SELECT l.id, l.title, start_price, img_url, l.expiration_date, c.title AS category FROM lots l '
            . 'JOIN categories c ON category_id = c.id '
            . 'WHERE MATCH(l.title, about) AGAINST(?) AND expiration_date > CURDATE() '
            . 'ORDER BY date_add DESC LIMIT ? OFFSET ?';
                
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $search, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {
        return [];
    }
}


function getAd(mysqli $link, int $id): ?array
{
    $sql = 'SELECT l.id, l.title, start_price, bet_step, img_url, l.author_id, l.expiration_date, c.title AS category, about FROM lots l '
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
    $sql = '
        SELECT 
            l.id, 
            l.title, 
            l.start_price, 
            l.bet_step,
            COALESCE(b.amount, l.start_price) AS current_price,
            u.id AS betUser,
            b.date_add
        FROM lots l
        LEFT JOIN bets b 
            ON b.id = (
                SELECT b2.id 
                FROM bets b2 
                WHERE b2.lot_id = l.id 
                ORDER BY b2.amount DESC, b2.date_add ASC 
                LIMIT 1
            )
        LEFT JOIN users u ON u.id = b.user_id
        WHERE l.id = ?
    ';

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}




function getUserBets(mysqli $link, int $id): ?array
{
    $sql = 'SELECT u.id AS user_id, a.contact AS contact, l.id AS lot_id, l.img_url, l.title, c.title AS category, l.expiration_date, b.amount, b.date_add '
                . 'FROM lots l '
                . 'LEFT JOIN categories c ON c.id = l.category_id '
                . 'LEFT JOIN bets b ON b.lot_id = l.id '
                . 'LEFT JOIN users u ON u.id = b.user_id '
                . 'LEFT JOIN users a ON a.id = l.author_id '
                . 'WHERE u.id = ? ORDER BY b.date_add DESC';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function getLotBets(mysqli $link, int $id): ?array
{
    $sql = 'SELECT l.id AS lot_id, u.name, b.amount, b.date_add '
                . 'FROM lots l '
                . 'RIGHT JOIN bets b ON b.lot_id = l.id '
                . 'LEFT JOIN users u ON u.id = b.user_id '
                . 'WHERE l.id = ? ORDER BY b.date_add DESC';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}