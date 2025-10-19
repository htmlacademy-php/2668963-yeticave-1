<section class="lot-item container">
  <h2><?= htmlspecialchars($ad['title']) ?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="<?= htmlspecialchars($ad["img_url"]) ?>" width="730" height="548" alt="Сноуборд">
      </div>
      <p class="lot-item__category">Категория: <span><?= htmlspecialchars($ad["category"]) ?></span></p>
      <p class="lot-item__description"><?= htmlspecialchars($ad["about"]) ?></p>
    </div>
    <div class="lot-item__right">
      <?php if (isset($_SESSION['username'])): ?>
        <div class="lot-item__state">
          <div class="lot-item__timer timer<?= getTimeToDate($ad["expiration_date"])[0] < 1 ? ' timer--finishing' : '' ?>">
            <?= str_pad(getTimeToDate($ad["expiration_date"])[0], 2, '0', STR_PAD_LEFT).':'.str_pad(getTimeToDate($ad["expiration_date"])[1], 2, '0', STR_PAD_LEFT) ?>
          </div>
          <div class="lot-item__cost-state">
            <div class="lot-item__rate">
              <span class="lot-item__amount">Текущая цена</span>
              <span class="lot-item__cost"><?= htmlspecialchars(formatPrice($bet["current_price"])) ?></span>
            </div>
            <div class="lot-item__min-cost">
              Мин. ставка <span><?= htmlspecialchars(formatPrice($ad["bet_step"])) ?></span>
            </div>
          </div>
          <?php if ($ad["author_id"] != $_SESSION["id"]): ?>
            <?php $classname = isset($errors) ? 'form--invalid' : ''; ?>
            <form class="lot-item__form <?= $classname; ?>" action="index.php?source=lot&id=<?= $_GET['id'] ?>" method="post" autocomplete="off">
              <?php $classname = isset($errors['cost']) ? 'form__item--invalid' : ''; ?>
              <p class="lot-item__form-item form__item <?= $classname; ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="От <?= htmlspecialchars(formatPrice($bet["current_price"] + $bet["bet_step"])) ?>">
                <span class="form__error">Введите ставку</span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="history">
        <h3>История ставок (<span><?= count($lotBets); ?></span>)</h3>
          <table class="history__list">
            <?php foreach ($lotBets as $lBet):?>
            <tr class="history__item">
              <td class="history__name"><?= $lBet['name']; ?></td>
              <td class="history__price"><?= formatPrice($lBet['amount']); ?></td>
              <td class="history__time"><?= formatTimeAgo($lBet['date_add']); ?></td>
            </tr>
            <? endforeach; ?>
          </table>
      </div>
    </div>
  </div>
</section>