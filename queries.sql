INSERT INTO users (email, name, password, contact) VALUES ('s1@mail.ru', 'Sally', 's1p123', '+79998887766');
INSERT INTO users (email, name, password, contact) VALUES ('k2@mail.ru', 'Kris', 'k2p123', '+71112223344');
INSERT INTO users (email, name, password, contact) VALUES ('m3@mail.ru', 'Max', 'm3p123', '+77777777777');


INSERT INTO categories (title, code) VALUES ('Доски и лыжи', 'boards');
INSERT INTO categories (title, code) VALUES ('Крепления', 'attachment');
INSERT INTO categories (title, code) VALUES ('Ботинки', 'boots');
INSERT INTO categories (title, code) VALUES ('Одежда', 'clothing');
INSERT INTO categories (title, code) VALUES ('Инструменты', 'tools');
INSERT INTO categories (title, code) VALUES ('Разное', 'other');


INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('2014 Rossignol District Snowboard', 'Отличное состояние', 'img/lot-1.jpg', 10999, '2025-09-20', 11099, 1, 1);

INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('DC Ply Mens 2016/2017 Snowboard', 'Хорошее состояние', 'img/lot-2.jpg', 159999, '2025-09-24', 159999, 2, 1);

INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('Крепления Union Contact Pro 2015 года размер L/XL', 'Пол года использования', 'img/lot-3.jpg', 8000, '2025-09-27', 8000, 1, 2);

INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('Ботинки для сноуборда DC Mutiny Charocal', 'Не ношенные', 'img/lot-4.jpg', 10999, '2025-09-21', 10999, 2, 3);

INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('Куртка для сноуборда DC Mutiny Charocal', 'Естественный износ', 'img/lot-5.jpg', 7500, '2025-09-23', 8500, 1, 4);

INSERT INTO lots (title, about, img_url, start_price, expiration_date, bet_step, author_id, category_id) 
    VALUES ('Маска Oakley Canopy', 'Резинку лучше заменить', 'img/lot-6.jpg', 5400, '2025-09-18', 5500, 2, 6);


INSERT INTO bets (amount, user_id, lot_id) VALUES (11099, 3, 1);
INSERT INTO bets (amount, user_id, lot_id) VALUES (8000, 2, 5);
INSERT INTO bets (amount, user_id, lot_id) VALUES (5500, 1, 6);
INSERT INTO bets (amount, user_id, lot_id) VALUES (8500, 3, 5);


/* получаем все категории */
SELECT * FROM categories;

/* получаем самые новые, открытые лоты. Каждый лот должен включать:
- название
- стартовую цену
- ссылку на изображение
- цену, 
- название категории */
SELECT l.title, start_price, img_url, bet_step, c.title AS category
FROM lots l 
JOIN categories c ON category_id = c.id
WHERE expiration_date > CURDATE();

/* Получаем лот по его ID + название категории, к которой принадлежит лот */
SELECT l.*, c.title AS category_title
FROM lots l 
JOIN categories c ON category_id = c.id
WHERE l.id = 1;

/* Обновление название лота по его идентификатору */
UPDATE lots SET title = '2025 Rossignol District Snowboard' WHERE id = '1';

/* Получаем список ставок для лота по его идентификатору с сортировкой по дате */
SELECT * 
FROM bets b
WHERE b.lot_id = 5
ORDER BY date_add DESC;