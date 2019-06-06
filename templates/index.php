<?php require_once("helpers.php"); ?>

<main class="container">   
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">

            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $category): ?>
                <li class="promo__item promo__item--<?= htmlspecialchars($category["character_code"]); ?>">
                    <a class="promo__link" href="pages/all-lots.html"><?= htmlspecialchars($category["cat_name"]); ?></a>
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
            <?php foreach ($stuff as $stuff_item):
                //functions from helpers.php
                $time_to_lot_expiration = get_time_to_expiration($stuff_item["dt_end"]);
                $is_time_finishing = is_time_to_midnight_finishing($time_to_lot_expiration);
            ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($stuff_item["lot_image_src"]); ?>" width="350" height="260" alt="<?= htmlspecialchars($stuff_item["lot_name"]); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($stuff_item["cat_name"]); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $stuff_item["lot_id"]; ?>"><?= htmlspecialchars($stuff_item["lot_name"]); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена: <?= htmlspecialchars($stuff_item["start_price"]); ?></span>
                            <span class="lot__cost">
                                <?= get_price_formatting(htmlspecialchars($stuff_item["current_price"] ? $stuff_item["current_price"] : $stuff_item["start_price"])); ?>
                            </span>
                        </div>
                        <div class="lot__timer timer <?php if ($is_time_finishing) { print("timer--finishing"); }; ?>">
                            <?= $time_to_lot_expiration; ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>

        </ul>
    </section>
</main>