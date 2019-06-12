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
  <section class="lot-item container">
    <h2><?= htmlspecialchars($lot_info["lot_name"]); ?></h2>
    <div class="lot-item__content">
      <div class="lot-item__left">
        <div class="lot-item__image">
          <img src="../<?= htmlspecialchars($lot_info["lot_image_src"]); ?>" width="730" height="548" alt="<?= htmlspecialchars($lot_info["lot_name"]); ?>">
        </div>
        <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot_info["cat_name"]); ?></span></p>
        <p class="lot-item__description"><?= htmlspecialchars($lot_info["description"]); ?></p>
      </div>      
      <div class="lot-item__right">
          
        <?php if ($is_auth) :?>
        <div class="lot-item__state">
          <div class="lot-item__timer timer <?php if ($is_time_finishing) { print("timer--finishing"); }; ?>">
            <?= $time_to_lot_expiration; ?>
          </div>
          <div class="lot-item__cost-state">
            <div class="lot-item__rate">
              <span class="lot-item__amount">Текущая цена</span>
              <span class="lot-item__cost"><?= htmlspecialchars($current_price); ?></span>
            </div>
            <div class="lot-item__min-cost">
              Мин. ставка <span><?= htmlspecialchars($min_bet); ?></span>
            </div>
          </div>
          <form class="lot-item__form" action="/lot.php?id=<?= $id; ?>" method="post" autocomplete="off">
            <p class="lot-item__form-item form__item form__item--invalid">
              <label for="cost">Ваша ставка</label>
              <input id="cost" type="text" name="cost" placeholder="<?= htmlspecialchars($min_bet); ?>" value="<?= $cost; ?>">
              <span class="form__error"><?php if (isset($errors["cost"])) { print(htmlspecialchars($errors["cost"])); }; ?></span>
            </p>
            <button type="submit" class="button">Сделать ставку</button>
          </form>
        </div>
        <?php endif; ?>
          
        <div class="history">
          <h3>История ставок (<span>10</span>)</h3>
          <table class="history__list">
            
            <?php foreach ($lot_bets as $lot_bet): 
                //function from helpers.php
                $dt_when_was_bet = when_was_bet($lot_bet["dt_bet"]);
            ?>
            <tr class="history__item">
              <td class="history__name"><?= htmlspecialchars($lot_bet["user_name"]); ?></td>
              <td class="history__price"><?= htmlspecialchars($lot_bet["price"]); ?></td>
              <td class="history__time"><?= $dt_when_was_bet; ?></td>
            </tr>
            <?php endforeach; ?>
            
          </table>
        </div>
      </div>
    </div>
  </section>
</main>