-- Заполнение таблиц
-- Добавляю данные в таблицу категорий
INSERT INTO categories 
(cat_name, character_code) 
VALUES 
("Доски и лыжи", "boards"),
("Крепления", "attachment"),
("Ботинки", "boots"),
("Одежда", "clothing"),
("Инструменты", "tools"),
("Разное", "other");

-- Добавляю пользователей
INSERT INTO users
(email, user_name, password)
VALUES
("user_1@mail.ru", "User_1", "User_1_password"),
("user_2@mail.ru", "User_2", "User_2_password"),
("user_3@mail.ru", "User_3", "User_3_password");

-- Добавляю список объявлений
INSERT INTO lots 
(lot_name, description, lot_image_src, start_price, author_id, category_code)
VALUES
("2014 Rossignol District Snowboard",
"",
"img/lot-1.jpg",
10999,
1,
"boards"),
("DC Ply Mens 2016/2017 Snowboard", 
"Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив
 снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух
 направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью,
 а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит
 уверенно держать высокие скорости. А если к концу катального дня сил совсем
 не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика
 от Шона Кливера еще никого не оставляла равнодушным.",
"img/lot-2.jpg",
159999,
1,
"boards"),
("Крепления Union Contact Pro 2015 года размер L/XL",
"",
"img/lot-3.jpg",
8000,
1,
"attachment"),
("Ботинки для сноуборда DC Mutiny Charocal",
"",
"img/lot-4.jpg",
10999,
1,
"boots"),
("Куртка для сноуборда DC Mutiny Charocal",
"",
"img/lot-5.jpg",
7500,
1,
"clothing"),
("Маска Oakley Canopy",
"",
"img/lot-6.jpg",
5400,
1,
"other");

-- Добавляю ставки
INSERT INTO bets
(price, better_id, lot_id)
VALUES
(12000, 2, 1),
(6000, 2, 6),
(8000, 3, 5);

-- Запросы
-- Получить все категории
SELECT cat_name FROM categories;

-- Получить самые новые, открытые лоты.
-- Название, стартовую цену, ссылку на изображение, цену, название категории.
SELECT 
lot_name, start_price, lot_image_src, current_price, category_code, dt_add
FROM lots
WHERE dt_end > NOW()
ORDER BY dt_add DESC
LIMIT 3;

-- Показать лот по его id. 
-- Получить также название категории, к которой принадлежит лот.
SELECT lots.*, cat_name FROM lots
LEFT JOIN categories c
ON lots.category_code = c.character_code
WHERE lots.id = 1;

-- Обновить название лота по его идентификатору.
UPDATE lots 
SET lot_name = "Маска Oakley Canopy"
WHERE id = 6;

-- Получить список самых свежих ставок для лота по его идентификатору.
SELECT * FROM bets
WHERE lot_id = 1
ORDER BY dt_bet DESC
LIMIT 3; 