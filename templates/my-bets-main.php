<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($categories as $category): ?>
        <li class="nav__item">
            <a href="all-lots.html"><?= htmlspecialchars($category["cat_name"]); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        
        <?php foreach ($my_bets as $my_bet): 
            //functions from helpers.php
            $time_to_lot_expiration = get_time_to_expiration($my_bet["dt_end"]);
            $is_time_finishing = is_time_to_midnight_finishing($time_to_lot_expiration);
            $dt_when_was_bet = when_was_bet($my_bet["dt_bet"]);
        ?>
            <tr class="rates__item">
              <td class="rates__info">
                <div class="rates__img">
                    <img src="<?= $my_bet["lot_image_src"]; ?>" width="54" height="40" alt="<?= htmlspecialchars($my_bet["lot_name"]); ?>">
                </div>
                <h3 class="rates__title"><a href="/lot.php?id=<?= $my_bet["lot_id"]; ?>"><?= htmlspecialchars($my_bet["lot_name"]); ?></a></h3>
              </td>
              <td class="rates__category">
                <?= htmlspecialchars($my_bet["cat_name"]); ?>
              </td>
              <td class="rates__timer">
                <div class="timer <?php if ($is_time_finishing) { print("timer--finishing"); } ?>">
                  <?= $time_to_lot_expiration; ?>
                </div>
              </td>
              <td class="rates__price">
                <?= htmlspecialchars($my_bet["price"]); ?> р
              </td>
              <td class="rates__time">
                <?= $dt_when_was_bet; ?>
              </td>
            </tr> 
        <?php endforeach; ?>
      
    </table>
  </section>
</main>