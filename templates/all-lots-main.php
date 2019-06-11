<main>
    <nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/all-lots.php?category=<?= $category["cat_name"]; ?>"><?= htmlspecialchars($category["cat_name"]); ?></a>
            </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="container">
        
      <section class="lots">
          
        <h2>Все лоты в категории «<span><?= htmlspecialchars($search_cat); ?></span>»</h2>
        
        <?php if ($searched_lots_count > 0): ?>
        
            <ul class="lots__list">
            <?php foreach ($searched_lots as $searched_lot): 
                $bets_count = $bets_count_array[$searched_lot["lot_id"]];
                //functions from helpers.php
                $time_to_lot_expiration = get_time_to_expiration($searched_lot["dt_end"]);
                $is_time_finishing = is_time_to_midnight_finishing($time_to_lot_expiration);
                $right_bets = get_noun_plural_form($bets_count, " ставка", " ставки", " ставок");
            ?>
                <li class="lots__item lot">
                  <div class="lot__image">
                    <img src="<?= $searched_lot["lot_image_src"]; ?>" width="350" height="260" alt="<?= $searched_lot["lot_name"]; ?>">
                  </div>
                  <div class="lot__info">
                    <span class="lot__category"><?= $searched_lot["cat_name"]; ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= $searched_lot["lot_id"]; ?>"><?= $searched_lot["lot_name"]; ?></a></h3>
                    <div class="lot__state">
                      <div class="lot__rate">
                        <span class="lot__amount"><?php $bets_count ? print($bets_count . $right_bets) : print("Стартовая цена"); ?></span>
                        <span class="lot__cost">
                            <?php $searched_lot["current_price"] ? print($searched_lot["current_price"]) : print($searched_lot["start_price"]); ?><b class="rub">р</b>
                        </span>
                      </div>
                      <div class="lot__timer timer <?php if ($is_time_finishing) { print('timer--finishing'); } ?>">
                        <?= $time_to_lot_expiration; ?>
                      </div>
                    </div>
                  </div>
                </li>
            <?php endforeach; ?>
            </ul>
        
        <?php elseif ($searched_lots_count === 0): ?>
            <p>Выбранная категория пуста.</p>
        <?php elseif ($searched_lots_count === -1): ?>
            <p>Выберите категорию.</p>
        <?php endif; ?>
        
      </section>
      
      <?php if ($searched_lots_count > 9): ?>
        <ul class="pagination-list">
          <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
          <li class="pagination-item pagination-item-active"><a>1</a></li>
          <li class="pagination-item"><a href="#">2</a></li>
          <li class="pagination-item"><a href="#">3</a></li>
          <li class="pagination-item"><a href="#">4</a></li>
          <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
      <?php endif; ?>
    </div>
</main>