<main>
    <?php $classname = isset($errors) ? 'form--invalid' : ''; ?>
    <form class="form container <?= $classname; ?>" action="index.php?source=sign-up" method="post" autocomplete="off"> <!-- form
    --invalid -->
      <h2>Регистрация нового аккаунта</h2>
      
      <?php $classname = isset($errors['email']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item <?= $classname; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>">
        <span class="form__error">Введите e-mail</span>
      </div>
      
      <?php $classname = isset($errors['password']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item <?= $classname; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error">Введите пароль</span>
      </div>

      <?php $classname = isset($errors['name']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item <?= $classname; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= getPostVal('name'); ?>">
        <span class="form__error">Введите имя</span>
      </div>

      <?php $classname = isset($errors['message']) ? 'form__item--invalid' : ''; ?>
      <div class="form__item <?= $classname; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"></textarea>
        <span class="form__error">Напишите как с вами связаться</span>
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


      <button type="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="index.php?source=login">Уже есть аккаунт</a>
    </form>
</main>



