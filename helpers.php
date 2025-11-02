<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && !date_get_last_errors();
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует цену: 159999 -> 159 999 ₽
 * @param string $price Строка с числом
 * @return string Итоговая отформатированная строка
 */
function formatPrice(string $price) {
    $price = ceil($price);

    if ($price < 1000) {
        return $price." ₽";
    }

    return number_format($price, 0, '', ' ')." ₽";
}

/**
 * Расчитывает время до определённой даты в формате ЧЧ:ММ
 * @param string $date Строка с датой в виде "2025-11-29 00:00:00"
 * @return array Массив вида [ЧЧ, ММ]
 */
function getTimeToDate(string $date) {
    $currentDate = date_create("now");
    $finishDate = date_create($date);
    $remainingTime = date_diff($currentDate, $finishDate);

    $hours = $remainingTime->days * 24 + $remainingTime->h;
    $minutes = $remainingTime->i;
     
    return [$hours, $minutes];
}

/**
 * Проверяет поле на заполнение
 * @param string $fieldName Имя поля формы
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function validateFilled(string $fieldName) {
    if (empty($_POST[$fieldName])) {
        return "Поле $fieldName должно быть заполнено";
    }
    return null;
}

/**
 * Проверка существования категории при добавлении лота
 * @param string $fieldName Имя поля формы
 * @param array<array-key, string> $allowedCatIdsList Массив допустимых ID категорий из БД
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function validateCategory(string $fieldName, array $allowedCatIdsList) {
    $fieldValue = $_POST[$fieldName];
    
    if (!in_array($fieldValue, $allowedCatIdsList)) {
        return "Указана несуществующая категория";
    }
    return null;
}

/**
 * Проверка стоимости товара при добавлении лота
 * @param string $fieldName Имя поля формы
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function isCorrectPrice(string $fieldName) {
    $fieldValue = $_POST[$fieldName];

    if ($fieldValue <= 0 || is_int($fieldValue)) {
        return "Цена товара должна быть целым числом больше 0";
    }
    return null;
}

/**
 * Проверка ставки на товар при добавлении лота
 * @param string $fieldName Имя поля формы
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function isCorrectBet(string $fieldName) {
    $fieldValue = $_POST[$fieldName];

    if ($fieldValue <= 0 || is_int($fieldValue)) {
        return "Ставка на товар должна быть целым числом больше 0";
    }
    return null;
}

/**
 * Проверка корректности имейла при регистрации
 * @param string $fieldName Имя поля формы
 * @param array<array-key, string> $notAllowedEmails Массив недопустимых имейлов
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function validateEmail(string $fieldName, array $notAllowedEmails) {
    $fieldValue = $_POST[$fieldName];

    if (empty($fieldValue)) {
        return validateFilled($fieldName);
    }

    if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }

    if (in_array($fieldValue, $notAllowedEmails)) {
        return "Пользователь существует, пожалуйста, войдите или сбросьте пароль";
    }

    return null;
}

/**
 * Проверка корректности имейла при входе
 * @param string $fieldName Имя поля формы
 * @param array<array-key, string> $userEmails Массив зарегистрированных имейлов
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function validateLoginEmail(string $fieldName, array $userEmails) {
    $fieldValue = $_POST[$fieldName];
    $flipedUserEmails = array_flip($userEmails);

    if (empty($fieldValue)) {
        return validateFilled($fieldName);
    }

    if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }

    if (isset($flipedUserEmails[$fieldValue])) {
        return null;
    }

    return "Пользователь не существует, пожалуйста, зарегистрируйтесь";
}

/**
 * Получение значения по параметру из массива $_POST
 * @param string $fieldName Имя поля формы
 * @return string Значение поля или пустая строка, если поле не установлено
 */
function getPostVal(string $fieldName) {
    return $_POST[$fieldName] ?? '';
}

/**
 * Получение значения по параметру из массива $_GET
 * @param string $fieldName Имя поля формы
 * @return string Значение поля или пустая строка, если поле не установлено
 */
function getGetVal(string $fieldName)
{
    return $_GET[$fieldName] ?? '';
}

/**
 * Проверка корректности ставки на лот
 * @param string $fieldName Имя поля формы
 * @param array<array-key, array<array-key, mixed>> $currentBet Текущая максимальная ставка
 * @param array<array-key, array<array-key, mixed>> $lotBetsList Все ставки на лот
 * @return string|null Сообщение об ошибке или null, если ошибок нет
 */
function validateBet(string $fieldName, array $currentBet, array $lotBetsList) {
    $fieldValue = $_POST[$fieldName];

    if (empty($fieldValue)) {
        return validateFilled($fieldName);
    }

    if ($lotBetsList && ($lotBetsList[0]['user_id'] === $_SESSION['id'])) {
         return "Ваша ставка уже добавлена";
    }

    if ((filter_var($fieldValue, FILTER_VALIDATE_INT) === false) || ($fieldValue < $currentBet["current_price"] + $currentBet["bet_step"])) {
        return "Введите целое число больше текущей цены";
    }

    return null;
}

/**
 * Преобразует дату в формат "5 минут назад", "вчера, в 21:30" и т.д.
 * @param string $date Дата в формате "Y-m-d H:i:s"
 * @return string Итоговая строка
 */
function formatTimeAgo(string $date): string
{
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Меньше минуты назад';
    }

    $minutes = floor($diff / 60);
    $hours = floor($diff / 3600);
    $days = floor($diff / 86400);

    if ($minutes < 60) {
        return $minutes . ' ' . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ' назад';
    }

    if ($hours < 24) {
        return $hours . ' ' . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
    }

    if ($days === 1.0) {
        return 'Вчера, в ' . date('H:i', $timestamp);
    }

    return date('d.m.y \в H:i', $timestamp);
}