<main>
    <?php $classname = isset($errors) ? 'form--invalid' : ''; ?>
    <form class="form container <?= $classname; ?>" action="index.php?source=login" method="post"> <!-- form--invalid -->
      <h2>Вход</h2>
      <?php $classname = isset($errors['email']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item <?= $classname; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>">
        <span class="form__error">Введите e-mail</span>
      </div>
      <?php $classname = isset($errors['password']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item form__item--last <?= $classname; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error">Введите пароль</span>
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
      <button type="submit" class="button">Войти</button>
    </form>
</main>