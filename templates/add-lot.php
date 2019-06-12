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
  <form class="form form--add-lot container <?php if (count($errors)) { print("form--invalid"); } ?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
      <div class="form__item <?php if (isset($errors["lot-name"])) { print("form__item--invalid"); } ?>"> <!-- form__item--invalid -->
        <label for="lot-name">Наименование <sup>*</sup></label>
        <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= $lot_name; ?>">
        <span class="form__error"><?php if (isset($errors["lot-name"])) { print(htmlspecialchars($errors["lot-name"])); } ?></span>
      </div>
      <div class="form__item <?php if (isset($errors["category"])) { print("form__item--invalid"); } ?>">
        <label for="category">Категория <sup>*</sup></label>
        <select id="category" name="category">
            <option>Выберите категорию</option>
            <?php foreach ($categories as $category) { 
                if ($post_cat == $category["cat_name"]) {
                    print("<option selected>");
                } 
                else {
                    print("<option>");
                }
                print(htmlspecialchars($category["cat_name"]));                
                print("</option>");
            }
            ?>          
        </select>
        <span class="form__error"><?php if (isset($errors["category"])) { print(htmlspecialchars($errors["category"])); } ?></span>
      </div>
    </div>
    <div class="form__item form__item--wide <?php if (isset($errors["message"])) { print("form__item--invalid"); } ?>">
      <label for="message">Описание <sup>*</sup></label>
      <textarea id="message" name="message" placeholder="Напишите описание лота"><?= htmlspecialchars($message); ?></textarea>
      <span class="form__error"><?php if (isset($errors["message"])) { print(htmlspecialchars($errors["message"])); } ?></span>
    </div>
    <div class="form__item form__item--file">
      <label>Изображение <sup>*</sup></label>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
        <label for="lot-img">
          Добавить
        </label>
      </div>
    </div>
    <div class="form__container-three">
      <div class="form__item form__item--small <?php if (isset($errors["lot-rate"])) { print("form__item--invalid"); } ?>">
        <label for="lot-rate">Начальная цена <sup>*</sup></label>
        <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= htmlspecialchars($lot_rate); ?>">
        <span class="form__error"><?php if (isset($errors["lot-rate"])) { print(htmlspecialchars($errors["lot-rate"])); } ?></span>
      </div>
      <div class="form__item form__item--small <?php if (isset($errors["lot-step"])) { print("form__item--invalid"); } ?>">
        <label for="lot-step">Шаг ставки <sup>*</sup></label>
        <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= htmlspecialchars($lot_step); ?>">
        <span class="form__error"><?php if (isset($errors["lot-step"])) { print(htmlspecialchars($errors["lot-step"])); } ?></span>
      </div>
      <div class="form__item <?php if (isset($errors["lot-date"])) { print("form__item--invalid"); } ?>">
        <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
        <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= htmlspecialchars($lot_date); ?>">
        <span class="form__error"><?php if (isset($errors["lot-date"])) { print(htmlspecialchars($errors["lot-date"])); } ?></span>
      </div>
    </div>
    <span class="form__error form__error--bottom"><?php if (isset($errors["file"])) { print($errors["file"]); } else { print("Пожалуйста, исправьте ошибки в форме."); } ?></span>
    <button type="submit" class="button">Добавить лот</button>
  </form>
</main>