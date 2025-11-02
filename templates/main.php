<section class="container">
    <section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?= htmlspecialchars($category['code']) ?>">
                <a class="promo__link" href="/index.php?source=lots-by-category&cat=<?= $category['code'] ?>"><?= htmlspecialchars($category['title']) ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
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
        </ul>
    </section>

</section>