 <div class="container">
      <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= $_GET['search'] ?></span>»</h2>
        <ul class="lots__list">
        <?php if ($ads): ?>
          <?php foreach ($ads as $ad): ?>
          <li class="lots__item lot">
              <div class="lot__image">
                  <img src="<?= htmlspecialchars($ad["img_url"]) ?>" width="350" height="260" alt="">
              </div>
              <div class="lot__info">
                  <span class="lot__category"><?= htmlspecialchars($ad["category"]) ?></span>
                  <h3 class="lot__title"><a class="text-link" href="index.php?source=lot&id=<?= $ad["id"] ?>"><?= htmlspecialchars($ad["title"]) ?></a></h3>
                  <div class="lot__state">
                      <div class="lot__rate">
                          <span class="lot__amount">Стартовая цена</span>
                          <span class="lot__cost"><?= htmlspecialchars(formatPrice($ad["start_price"])) ?></span>
                      </div>
                      <div class="lot__timer timer<?= getTimeToDate($ad["expiration_date"])[0] < 1 ? ' timer--finishing' : '' ?>">
                          <?= str_pad(getTimeToDate($ad["expiration_date"])[0], 2, '0', STR_PAD_LEFT).':'.str_pad(getTimeToDate($ad["expiration_date"])[1], 2, '0', STR_PAD_LEFT) ?>
                      </div>
                  </div>
              </div>
          </li>
          <?php endforeach; ?>
        <?php else: ?>
          <div>Ничего не найдено по вашему запросу</div>
        <?php endif; ?>
        </ul>
      </section>

      <?php if ($totalPages > 1): ?>
        <ul class="pagination-list">
          <?php if ($currentPage > 1): ?>
            <li class="pagination-item pagination-item-prev">
              <a href="?source=search-page&page=<?= $currentPage - 1 ?>&search=<?= $_GET['search'] ?>&find=Найти">Назад</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="pagination-item <?= $i === $currentPage ? 'pagination-item-active' : '' ?>">
              <a href="?source=search-page&page=<?= $i ?>&search=<?= $_GET['search'] ?>&find=Найти"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($currentPage < $totalPages): ?>
            <li class="pagination-item pagination-item-next">
              <a href="?source=search-page&page=<?= $currentPage + 1 ?>&search=<?= $_GET['search'] ?>&find=Найти">Вперед</a>
            </li>
          <?php endif; ?>
        </ul>
      <?php endif; ?>
    </div>