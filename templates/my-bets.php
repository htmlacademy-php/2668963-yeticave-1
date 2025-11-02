<section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
            <?php foreach ($bets as $bet): ?>
                <?php                                         
                    $classname = ''; 
                    $isLotEnded = strtotime($bet["expiration_date"]) < time();
                    $isUserWinner = $maxBets[$bet['lot_id']]["betUser"] === $_SESSION["id"];
                    $isLastBet = isset($maxBets[$bet['lot_id']]["date_add"]) && $bet["date_add"] === $maxBets[$bet['lot_id']]["date_add"];

                    if ($isLotEnded && $isUserWinner && $isLastBet) {
                        $classname = 'rates__item--win';
                    } elseif ($isLotEnded) {
                        $classname = 'rates__item--end';
                    }
                
                ?>
                <tr class="rates__item <?= $classname; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= $bet['img_url'] ?>" width="54" height="40" alt="Сноуборд">
                    </div>
                    <div>
                        <h3 class="rates__title"><a href="index.php?source=lot&id=<?= $bet["lot_id"] ?>"><?= $bet['title'] ?></a></h3>
                        <?php if ($isLotEnded && $isUserWinner && $isLastBet): ?>
                            <p><?= $bet["contact"] ?></p>
                        <?php endif; ?>
                    </div>

                </td>
                <td class="rates__category">
                    <?= $bet['category'] ?>
                </td>
                <td class="rates__timer">
                    <?php if ($isLotEnded && $isUserWinner && $isLastBet): ?>
                        <div class="timer timer--win">Ставка выиграла</div>
                    <?php elseif ($isLotEnded ): ?>
                        <div class="timer timer--end">Торги окончены</div>
                    <?php else: ?>
                        <div class="timer <?= getTimeToDate($bet["expiration_date"])[0] < 10 ? ' timer--finishing' : '' ?>">
                            <?= str_pad(getTimeToDate($bet["expiration_date"])[0], 2, '0', STR_PAD_LEFT).':'.str_pad(getTimeToDate($bet["expiration_date"])[1], 2, '0', STR_PAD_LEFT) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= formatPrice($bet["amount"]) ?>
                </td>
                <td class="rates__time">
                    <?= formatTimeAgo($bet["date_add"]) ?>
                </td>
                </tr>
            <?php endforeach; ?>
      </table>
    </section>