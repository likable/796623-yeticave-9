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
  <form class="form container <?php if (count($errors)) { print("form--invalid"); } ?>" action="sign-up.php" method="post" autocomplete="off"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?php if (isset($errors["email"])) { print("form__item--invalid"); } ?>"> <!-- form__item--invalid -->
      <label for="email">E-mail <sup>*</sup></label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $email; ?>">
      <span class="form__error"><?php if (isset($errors["email"])) { print(htmlspecialchars($errors["email"])); } ?></span>
    </div>
    <div class="form__item <?php if (isset($errors["password"])) { print("form__item--invalid"); } ?>">
      <label for="password">Пароль <sup>*</sup></label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $password; ?>">
      <span class="form__error"><?php if (isset($errors["password"])) { print(htmlspecialchars($errors["password"])); } ?></span>
    </div>
    <div class="form__item <?php if (isset($errors["name"])) { print("form__item--invalid"); } ?>">
      <label for="name">Имя <sup>*</sup></label>
      <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= $firstname; ?>">
      <span class="form__error"><?php if (isset($errors["name"])) { print(htmlspecialchars($errors["name"])); } ?></span>
    </div>
    <div class="form__item <?php if (isset($errors["message"])) { print("form__item--invalid"); } ?>">
      <label for="message">Контактные данные <sup>*</sup></label>
      <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= $message; ?></textarea>
      <span class="form__error"><?php if (isset($errors["message"])) { print(htmlspecialchars($errors["message"])); } ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/login.php">Уже есть аккаунт</a>
  </form>
</main>