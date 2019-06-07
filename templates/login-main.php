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
  <form class="form container <?php if (count($errors)) { print("form--invalid"); } ?>" action="login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <div class="form__item <?php if ($errors["email"]) { print("form__item--invalid"); } ?>"> <!-- form__item--invalid -->
      <label for="email">E-mail <sup>*</sup></label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $email; ?>">
      <span class="form__error"><?= htmlspecialchars($errors["email"]); ?></span>
    </div>
    <div class="form__item form__item--last <?php if ($errors["password"]) { print("form__item--invalid"); } ?>">
      <label for="password">Пароль <sup>*</sup></label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $password; ?>">
      <span class="form__error"><?= htmlspecialchars($errors["password"]); ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
  </form>
</main>

