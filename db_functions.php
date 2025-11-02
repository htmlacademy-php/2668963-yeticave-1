<?php

/**
 * Получение категорий
 * @param mysql $link Связь с БД
 * @return array Массив категории, пустой - если их нет
 */
function getCategories(mysqli $link): array
{
    $sql = 'SELECT * FROM categories';
    $result = mysqli_query($link, $sql);
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

/**
 * Получение имейлов
 * @param mysql $link Связь с БД
 * @return array Массив имейлов, пустой - если их нет
 */
function getUsersEmails(mysqli $link): array
{
    $sql = 'SELECT email FROM users';
    $result = mysqli_query($link, $sql);
    
    return $result ? array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'email') : [];
}

/**
 * Получение списка категорий на главную
 * @param mysql $link Связь с БД
 * @return array Массив категорий, пустой - если их нет
 */
function getAdsList(mysqli $link): array
{
    $sql = 'SELECT l.id, l.title, start_price, img_url, l.expiration_date, c.title AS category FROM lots l '
                . 'JOIN categories c ON category_id = c.id '
                . 'WHERE expiration_date > CURDATE() '
                . 'ORDER BY date_add DESC LIMIT 6';
    $result = mysqli_query($link, $sql);
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

/**
 * Подсчёт лотов в категории
 * @param mysql $link Связь с БД
 * @return int Количество лотов в категории, 0 - если их нет
 */
function countAdsListByCategory(mysqli $link): int
{
    $category = trim($_GET['cat'] ?? '');
    
    $sql = 'SELECT c.code, COUNT(lots.id) AS total FROM lots '
            .'JOIN categories c ON category_id = c.id '
            .'WHERE expiration_date > CURDATE() AND c.code = ? '
            .'GROUP BY c.code';
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row ? $row['total'] : 0 ;
}

/**
 * Получение лотов в категории
 * @param mysql $link Связь с БД
 * @param int $offset Смещение (количество записей, которые нужно пропустить)
 * @param int $limit Количество лотов для выборки (на одной странице)
 * @return array Массив лотов, пустой - если их нет
 */
function getAdsListByCategory(mysqli $link, int $offset, int $limit): array
{
    $category = trim($_GET['cat'] ?? '');

    if ($category) {
        $sql = 'SELECT l.id, l.title, start_price, img_url, l.expiration_date, c.code, c.title AS category FROM lots l '
                    . 'JOIN categories c ON category_id = c.id '
                    . 'WHERE expiration_date > CURDATE() AND c.code = ? '
                    . 'ORDER BY date_add DESC LIMIT ? OFFSET ?';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $category, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {
        return [];
    }
}

/**
 * Подсчёт лотов при поиске
 * @param mysql $link Связь с БД
 * @return int Количество найденых лотов
 */
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

/**
 * Получение лотов при поиске
 * @param mysql $link Связь с БД
 * @param int $offset Смещение (количество записей, которые нужно пропустить)
 * @param int $limit Количество лотов для выборки (на одной странице)
 * @return array Массив лотов, пустой - если их нет
 */
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

/**
 * Получение лота
 * @param mysql $link Связь с БД
 * @param int $id ID лота
 * @return array|null Лот в виде массива или null, если лота нет
 */
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

/**
 * Получение максимальной ставки
 * @param mysql $link Связь с БД
 * @param int $id ID лота
 * @return array|null Ставка в виде массива или null, если ставки нет
 */
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

/**
 * Получение ставок пользолвателя
 * @param mysql $link Связь с БД
 * @param int $id ID пользователя
 * @return array|null Ставки в виде массива или null, если ставок нет
 */
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

/**
 * Получение ставок лота
 * @param mysql $link Связь с БД
 * @param int $id ID лота
 * @return array|null Ставки в виде массива или null, если ставок нет
 */
function getLotBets(mysqli $link, int $id): ?array
{
    $sql = 'SELECT l.id AS lot_id, u.name, b.user_id, b.amount, b.date_add '
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

/**
 * Получение максимальных ставок истёкших лотов
 * @param mysql $link Связь с БД
 * @return array|null Ставки в виде массива или null, если ставок нет
 */
function getExpirationLotsMaxBetList(mysqli $link): array
{
    $sql = 'SELECT u.email, u.name, l.id AS lotId, l.title AS title, l.winner_id, b.id AS betId, b.user_id AS betUserID, b.amount '
                . 'FROM lots l '
                . 'JOIN bets b ON l.id = b.lot_id '
                . 'JOIN users u ON b.user_id = u.id '
                . 'WHERE l.expiration_date <= CURDATE() '
                . 'AND l.winner_id IS NULL '
                . 'AND b.amount = ('
                . 'SELECT MAX(b2.amount) '
                . 'FROM bets b2 '
                . 'WHERE b2.lot_id = l.id)';
    $result = mysqli_query($link, $sql);
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}
