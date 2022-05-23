# создает список типов контента для поста
INSERT INTO content_types (name, type) VALUES
   ('Картинка', 'photo'),
   ('Видео', 'video'),
   ('Текст', 'text'),
   ('Цитата', 'quote'),
   ('Ссылка', 'link');

# создает трех пользователей
INSERT INTO users (email, login, password_hash, avatar_url) VALUES
    ('ivan_ivanov@mail.com', 'user_ivan', 'user_ivan_password_hash', 'img/userpic-larisa-small.jpg'),
    ('petr_petrov@mail.com', 'user_petr', 'user_petr_password_hash', 'img/userpic.jpg'),
    ('sidr_sidorov@mail.com', 'user_sidr', 'user_sidr_password_hash', 'img/userpic-mark.jpg');

# создает существующий список постов
INSERT INTO posts (author_id, content_type_id, title, text_content, string_content, views_count) VALUES
    (1, 4, 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор', 3),
    (2, 3, 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', 0),
    (3, 1, 'Наконец, обработал фотки!', '', 'img/rock-medium.jpg', 2),
    (1, 1, 'Моя мечта', '', 'img/coast-medium.jpg', 10),
    (2, 5, 'Лучшие курсы', '', 'www.htmlacademy.ru', 5);

# создает пару комментариев к разным постам
INSERT INTO comments (author_id, post_id, content) VALUES
    (2, 1, 'Классная цитата! Давай еще таких!'),
    (3, 2, 'Крутые фотки! Ждем новых!');

# получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT
    posts.*,
    users.login AS author_name,
    content_types.name AS content_name
FROM posts
     JOIN users
          ON posts.author_id = users.id
     JOIN content_types
          ON posts.content_type_id = content_types.id
ORDER BY posts.views_count DESC;

# получить список постов для конкретного пользователя (id = 1)
SELECT * from posts WHERE author_id = 1;

# получить список комментариев для одного поста (id = 1), в комментариях должен быть логин пользователя
SELECT
    comments.*,
    users.login AS comment_author_login
FROM comments
    JOIN users
        ON comments.author_id = users.id
WHERE post_id = 1;

# добавить лайк к посту. Пользователь с id = 2 ставит лайк к посту с id = 1.
INSERT INTO likes (post_id, author_id) VALUES (1, 2);

# подписаться на пользователя. Пользователь с id = 1 подписывается на пользователя с id = 2.
INSERT INTO subscriptions (subscriber_id, observable_id) VALUES (1 ,2);
