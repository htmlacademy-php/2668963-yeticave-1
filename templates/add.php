<link href="../css/normalize.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
<link href="../css/flatpickr.min.css" rel="stylesheet">

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $rules = [
    'lot-name' => function() {
        return validateFilled('lot-name');
    },
    'category' => function() use ($categoryId) {
        return validateCategory('category', $categoryId);
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

    if (!empty($_FILES['lot-img']['name'])) {
        $tmp_name = $_FILES['lot-img']['tmp_name'];
        $path = $_FILES['lot-img']['name'];
        $filename = uniqid() . '.jpg';

        $finfo = finfo_open (FILEINFO_MIME_TYPE) ;
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
            $errors['file'] = 'Загрузите картинку в формате JPG или PNG';
        }
        else {
            move_uploaded_file($tmp_name, 'uploads/' . $filename);
            $lot['img_url'] = 'uploads/' . $filename;
        }
    } 
    else {
        $errors ['file'] = 'Файл не загружен';
    }

    if (!count($errors)) {
        $sql = 'INSERT INTO lots (date_add, title, category_id, about, start_price, bet_step, expiration_date, author_id, img_url) '
            . 'VALUES (NOW(), ?, ?, ?, ?, ?, ?, 1, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, $lot);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($link);
            header (header: "Location: index.php?source=lot&id=" . $lot_id);
        }
    }   
}
?>


<?php $classname = isset($errors) ? 'form--invalid' : ''; ?>
<form class="form form--add-lot container <?= $classname; ?>" action="index.php?source=add" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
    <?php $classname = isset($errors['lot-name']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item <?= $classname; ?>"> <!-- form__item--invalid -->
        <label for="lot-name">Наименование <sup>*</sup></label>
        <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= getPostVal('lot-name'); ?>">
        <span class="form__error">Введите наименование лота</span>
    </div>
    <?php $classname = isset($errors['category']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="category">Категория <sup>*</sup></label>
        <select id="category" name="category">
            <option>Выберите категорию</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['id']) ?>"
            <?php if (htmlspecialchars($category['id']) == getPostVal('category')) : ?> selected<?php endif; ?>>
                <?= htmlspecialchars($category['title']) ?>
            </option>
        <?php endforeach; ?>
        </select>
        <span class="form__error">Выберите категорию</span>
    </div>
    </div>
    <?php $classname = isset($errors['message']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item form__item--wide <?= $classname; ?>">
    <label for="message">Описание <sup>*</sup></label>
    <textarea id="message" name="message" placeholder="Напишите описание лота"><?= htmlspecialchars(getPostVal('message')); ?></textarea>
    <span class="form__error">Напишите описание лота</span>
    </div>
    <div class="form__item form__item--file">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file">
        <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
        <label for="lot-img">
        Добавить
        </label>
    </div>
    </div>
    <div class="form__container-three">
        <?php $classname = isset($errors['lot-rate']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item form__item--small <?= $classname; ?>">
        <label for="lot-rate">Начальная цена <sup>*</sup></label>
        <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= getPostVal('lot-rate'); ?>">
        <span class="form__error">Введите начальную цену</span>
    </div>
    <?php $classname = isset($errors['lot-step']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item form__item--small <?= $classname; ?>">
        <label for="lot-step">Шаг ставки <sup>*</sup></label>
        <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= getPostVal('lot-step'); ?>">
        <span class="form__error">Введите шаг ставки</span>
    </div>
    <?php $classname = isset($errors['lot-date']) ? 'form__item--invalid' : ''; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
        <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
        <span class="form__error">Введите дату завершения торгов</span>
    </div>
    </div>
    
    <?php if (isset($errors)): ?>
        <div class="form__errors">
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
            <ul>
                <?php foreach ($errors as $val): ?>
                    <li><strong><?= $val; ?></strong></li>
                <?php endforeach; ?>
            </uL>
        </div>
    <?php endif; ?>
    <button type="submit" class="button">Добавить лот</button>
</form>

<script src="../flatpickr.js"></script>
<script src="../script.js"></script>