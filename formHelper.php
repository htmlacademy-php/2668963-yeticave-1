<?php
/**
 * @param array<array-key, string> $usersEmailsList
 */
function regFormValidate(mysqli $link, array $usersEmailsList){
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $rules = [
        'email' => function() use ($usersEmailsList) {
            return validateEmail('email', $usersEmailsList);
        },
        'password' => function() {
            return validateFilled('password');
        },
        'name' => function() {
            return validateFilled('name');
        },
        'message' => function() {
            return validateFilled('message');
        }
    ];

    $newUser = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT, 
    'name' => FILTER_DEFAULT, 'message' => FILTER_DEFAULT], true);

    foreach ($newUser as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    $errors = array_filter($errors);

    if (!count($errors)) {
        $newUser['password'] = password_hash($newUser['password'], PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (date_add, email, password, name, contact) '
            . 'VALUES (NOW(), ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, $newUser);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            header (header: "Location: index.php?source=login");
        }
    }
    
    return $errors;
}



/**
 * @param array<array-key, string> $usersEmailsList
 */
function loginFormValidate(mysqli $link, array $usersEmailsList){
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $rules = [
        'email' => function() use ($usersEmailsList) {
            return validateLoginEmail('email', $usersEmailsList);
        },
        'password' => function() {
            return validateFilled('password');
        }
    ];

    $user = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);

    foreach ($user as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    $errors = array_filter($errors);

    if (!count($errors)) {
        $sql = 'SELECT id, name, password FROM users '
            . 'WHERE email = ?';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $user['email']);
        mysqli_stmt_execute($stmt);    
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $hashPassword = $row['password'];
        $userName = $row['name'];
        $userId = $row['id'];

        if (password_verify($user['password'], $hashPassword)) {
            $_SESSION['username'] = $userName;
            $_SESSION['id'] = $userId;
            header (header: "Location: index.php");
        } else {
            $errors['password'] = "Неверный пароль";
        }

        
        
    }
    
    return $errors;
}



/**
 * @param array<array-key, string> $categoriesIdsList
 */
function addLotFormValidate(mysqli $link, array $categoriesIdsList, int $userId) {
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $rules = [
        'lot-name' => function() {
            return validateFilled('lot-name');
        },
        'category' => function() use ($categoriesIdsList) {
            return validateCategory('category', $categoriesIdsList);
        },
        'message' => function() {
            return validateFilled('message');
        },
        'lot-rate' => function() {
            return isCorrectPrice('lot-rate');
        },
        'lot-step' => function() {
            return isCorrectBet('lot-step');
        },
        'lot-date' => function() {
            
            $date = $_POST['lot-date'];   
            if (!is_date_valid($date)) {
                return "Неверный формат поля: Дата окончания торгов";
            }
            
            $today = strtotime('today');
            if (strtotime($date) <= $today) {
                return "Дата должна быть позже сегодняшней хотя бы на один день.";
            }

            return null;
        }
    ];
    
    $lot = filter_input_array(INPUT_POST, ['lot-name' => FILTER_DEFAULT, 'category' => FILTER_DEFAULT, 
    'message' => FILTER_DEFAULT, 'lot-rate' => FILTER_DEFAULT, 
    'lot-step' => FILTER_DEFAULT, 'lot-date' => FILTER_DEFAULT], true);

    foreach ($lot as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    $errors = array_filter($errors);

    $lot['authorId'] = $userId;

    if (!empty($_FILES['lot-img']['name'])) {
        $tmp_name = $_FILES['lot-img']['tmp_name'];
        $path = $_FILES['lot-img']['name'];
        $filename = uniqid() . '.jpg';

        $finfo = finfo_open (FILEINFO_MIME_TYPE) ;
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
            $errors['file'] = 'Загрузите картинку в формате JPG или PNG';
        } else {
            move_uploaded_file($tmp_name, 'uploads/' . $filename);
            $lot['img_url'] = 'uploads/' . $filename;
        }
    } else {
        $errors ['file'] = 'Файл не загружен';
    }

    if (!count($errors)) {
        $sql = 'INSERT INTO lots (date_add, title, category_id, about, start_price, bet_step, expiration_date, author_id, img_url) '
            . 'VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, $lot);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($link);
            header (header: "Location: index.php?source=lot&id=" . $lot_id);
        }
    }   

    return $errors;
}