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
        <?php if ($is_forbidden): ?>
            <h2>403 Доступ запрещён</h2>
            <p>У вас нет права доступа к этой странице.</p>
        <?php else: ?>
            <h2>404 Страница не найдена</h2>
            <p>Данной страницы не существует на сайте.</p>
        <?php endif; ?>
    </section>
</main>
