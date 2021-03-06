# создает список типов контента для поста
INSERT INTO content_types (name, type) VALUES
   ('Картинка', 'photo'),
   ('Видео', 'video'),
   ('Текст', 'text'),
   ('Цитата', 'quote'),
   ('Ссылка', 'link');

# создает трех пользователей
INSERT INTO users (email, login, password_hash, avatar_url) VALUES
    ('larisa_ivanova@mail.com', 'Лариса Иванова', 'user_larisa_password_hash', 'img/userpic-larisa-small.jpg'),
    ('petr_kuztentsov@mail.com', 'Петр Кузнецов', 'user_petr_password_hash', 'img/userpic.jpg'),
    ('ivan_sidorov@mail.com', 'Иван Сидоров', 'user_ivan_password_hash', 'img/userpic-mark.jpg');

# создает существующий список постов
INSERT INTO posts (author_id, content_type_id, title, text_content, string_content) VALUES
    (1, 4, 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор'),
    (2, 3, 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', ''),
    (3, 1, 'Наконец, обработал фотки!', '', 'img/rock-medium.jpg'),
    (1, 1, 'Моя мечта', '', 'img/coast-medium.jpg'),
    (2, 5, 'Лучшие курсы', '', 'https://htmlacademy.ru');

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

# добавить лайки к постам:
# пользователь с id = 2 ставит лайк к посту с id = 1,
# пользователь с id = 3 ставит лайк к посту с id = 1,
# пользователь с id = 1 ставит лайк к посту с id = 2,
# пользователь с id = 1 ставит лайк к посту с id = 3.
INSERT INTO likes (post_id, author_id) VALUES
    (1, 2), (1, 3), (2, 1), (3, 1);

# оформление подписок на пользователей:
# пользователь с id = 1 подписывается на пользователя с id = 2,
# пользователь с id = 1 подписывается на пользователя с id = 3,
# пользователь с id = 2 подписывается на пользователя с id = 3,
# пользователь с id = 3 подписывается на пользователя с id = 1.
INSERT INTO subscriptions (subscriber_id, observable_id) VALUES
    (1 ,2), (1, 3), (2, 3), (3, 1);
