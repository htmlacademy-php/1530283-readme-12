<?php

require_once 'utils/functions.php';
require_once 'models/hashtag.php';
require_once 'models/post_hashtag.php';

/**
 * Функция получает список публикаций из базы данных для страницы 'Популярное'.
 * Функция принимает ресурс соединения с базой данный
 * и ассоциативныый массив с параметрами запроса.
 * Параметры запроса позволяют задавать фильтрацию и сортировку публикаций.
 * В случае успешного запроса функция возвращает массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  array{
 *     sort_type: 'views_count' | 'likes_count' | 'created_at' | null,
 *     is_order_reversed: bool | null,
 *     content_type_id: int | null,
 *     limit: int,
 *     offset: int
 * } $config - параметры запроса
 *
 * @return null | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     is_liked: bool
 * }>
 */
function get_popular_posts(mysqli $db_connection, int $user_id, $config = [])
{
    $sort_type = $config['sort_type'] ? mysqli_real_escape_string(
        $db_connection,
        $config['sort_type']
    ) : null;
    $content_type_id = $config['content_type_id'] ?? '';
    $is_order_reversed = $config['is_order_reversed'] ?? false;
    list('limit' => $limit, 'offset' => $offset) = $config;

    $filter_sql =
        $config['content_type_id'] ? "WHERE content_types.id = ?" : '';
    $order_direction_sql = $is_order_reversed ? 'ASC' : 'DESC';
    $sort_sql = $sort_type ? "ORDER BY $sort_type $order_direction_sql" : '';

    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
        $filter_sql
        GROUP BY posts.id
        $sort_sql
        LIMIT ? OFFSET ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);

    if ($filter_sql) {
        mysqli_stmt_bind_param(
            $statement,
            'siii',
            $user_id,
            $content_type_id,
            $limit,
            $offset
        );
    } else {
        mysqli_stmt_bind_param(
            $statement,
            'sii',
            $user_id,
            $limit,
            $offset
        );
    }

    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($posts as &$post) {
        $post['author'] = json_decode($post['author'], true);
    }

    return $posts;
}

/**
 * Функция получает список публикаций из базы данных для странцы 'Моя лента'.
 * Функция принимает ресурс соединения с базой данный
 * и ассоциативныый массив с параметрами запроса.
 * Параметры запроса позволяют задавать фильтрацию и сортировку публикаций.
 * В случае успешного запроса функция возвращается массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  array{
 *     content_type_id: int | null
 * } $config - параметры запроса
 *
 * @return null | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     reposts_count: int,
 *     hashtags: array,
 *     is_liked: bool
 * }>
 */
function get_feed_posts(mysqli $db_connection, int $user_id, $config = [])
{
    $content_type_id = $config['content_type_id'] ?? '';

    $filter_sql = "WHERE subscriptions.subscriber_id = ?";

    if ($config['content_type_id']) {
        $filter_sql .= ' AND content_types.id = ?';
    }

    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            COUNT(DISTINCT reposts.repost_id) AS reposts_count,
            JSON_ARRAYAGG(hashtags.name) AS hashtags,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            JOIN subscriptions ON posts.author_id = subscriptions.observable_id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN reposts ON posts.id = reposts.original_post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        $filter_sql
        GROUP BY posts.id
    ";

    $statement = mysqli_prepare($db_connection, $sql);

    if ($config['content_type_id']) {
        mysqli_stmt_bind_param(
            $statement,
            'sii',
            $user_id,
            $user_id,
            $content_type_id
        );
    } else {
        mysqli_stmt_bind_param($statement, 'si', $user_id, $user_id);
    }

    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($posts as &$post) {
        $post['hashtags'] = decode_json_array_agg($post['hashtags']);
        $post['author'] = json_decode($post['author'], true);
    }

    return $posts;
}

/**
 * Функция получается публикации из базы данных по строке запроса
 * с применением полнотекстового поиска по полям заголовка и контента.
 * В случае успешного запроса функция возвращается массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соедения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  string  $query  - строка запроса
 *
 * @return null | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     reposts_count: int,
 *     hashtags: array,
 *     is_liked: bool,
 *     is_own: bool
 * }>
 */
function get_posts_by_query(mysqli $db_connection, int $user_id, string $query)
{
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            COUNT(DISTINCT reposts.repost_id) AS reposts_count,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked,
            MATCH(posts.title, posts.string_content, posts.text_content)
                AGAINST(? IN BOOLEAN MODE) AS score,
            JSON_ARRAYAGG(hashtags.name) AS hashtags,
            (posts.author_id = ?) AS is_own
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN reposts ON posts.id = reposts.original_post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        WHERE MATCH(posts.title, posts.string_content, posts.text_content)
            AGAINST(? IN BOOLEAN MODE)
        GROUP BY posts.id
        ORDER BY score DESC
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        'ssis',
        $user_id,
        $query,
        $user_id,
        $query
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($posts as &$post) {
        $post['hashtags'] = decode_json_array_agg($post['hashtags']);
        $post['author'] = json_decode($post['author'], true);
    }

    return $posts;
}

/**
 * Функция получает публикации из базы данных по заданному названию хэштега.
 * В случае успешного запроса функция возвращается массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соедения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  string  $hashtag  - название хэштега
 *
 * @return null | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     reposts_count: int,
 *     hashtags: array,
 *     is_liked: bool,
 *     is_own: bool
 * }>
 */
function get_posts_by_hashtag(
    mysqli $db_connection,
    int $user_id,
    string $hashtag
) {
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            COUNT(DISTINCT reposts.repost_id) AS reposts_count,
            JSON_ARRAYAGG(hashtags.name) AS hashtags,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked,
            (posts.author_id = ?) AS is_own
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN reposts ON posts.id = reposts.original_post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        GROUP BY posts.id, posts.created_at
        HAVING LOCATE(?, hashtags) > 0
        ORDER BY posts.created_at DESC 
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'sis', $user_id, $user_id, $hashtag);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($posts as &$post) {
        $post['hashtags'] = decode_json_array_agg($post['hashtags']);
        $post['author'] = json_decode($post['author'], true);
    }

    return $posts;
}

/**
 * Функция получается публикации из базы данных по заданному id автора
 * с применением полнотекстового поиска по полям заголовка и контента.
 * В случае успешного запроса функция возвращается массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соедения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int  $author_id  - id автора
 *
 * @return null | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 *     original_post: array{
 *         author_id: int,
 *         author_login: string,
 *         author_avatar_url: string,
 *         created_at: string
 *     },
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     reposts_count: int,
 *     hashtags: array,
 *     is_liked: bool,
 *     is_own: bool
 * }>
 */
function get_posts_by_author(
    mysqli $db_connection,
    int $user_id,
    int $author_id
) {
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author,
            (SELECT
                JSON_OBJECT(
                     'author_id', sub_users.id,
                     'author_login', sub_users.login,
                     'author_avatar_url', sub_users.avatar_url,
                     'created_at', sub_posts.created_at
                ) FROM reposts sub_reposts
                JOIN posts sub_posts
                    ON sub_reposts.original_post_id = sub_posts.id
                JOIN users sub_users
                    ON sub_posts.author_id = sub_users.id
                WHERE sub_reposts.repost_id = posts.id
            ) AS original_post,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            COUNT(DISTINCT reposts.repost_id) AS reposts_count,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked,
            JSON_ARRAYAGG(hashtags.name) AS hashtags,
            (posts.author_id = ?) AS is_own
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN reposts ON posts.id = reposts.original_post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        WHERE posts.author_id = ?
        GROUP BY posts.id, posts.created_at
        ORDER BY posts.created_at DESC 
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'sii', $user_id, $user_id, $author_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($posts as &$post) {
        $post['hashtags'] = decode_json_array_agg($post['hashtags']);
        $post['author'] = json_decode($post['author'], true);
        $post['original_post'] = json_decode($post['original_post'], true);
    }

    return $posts;
}

/**
 * Функция получает публикацию из базы данных по заданному id.
 * В случае успешного запроса функция возвращает публикацию
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int  $post_id  - id публикации
 *
 * return null | array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author_id: int,
 *     author_login: string,
 *     author_avatar: string,
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 *     reposts_count: int,
 *     hashtags: array,
 *     is_liked: bool,
 *     is_own: bool
 * } - данные публикации
 */
function get_post(mysqli $db_connection, int $user_id, int $post_id)
{
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            posts.author_id,
            users.login AS author_login,
            users.avatar_url AS author_avatar,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count,
            COUNT(DISTINCT reposts.repost_id) AS reposts_count,
            JSON_ARRAYAGG(hashtags.name) AS hashtags,
            (JSON_CONTAINS(JSON_ARRAYAGG(likes.author_id), ?) = 1) AS is_liked,
            (posts.author_id = ?) AS is_own
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN reposts ON posts.id = reposts.original_post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        WHERE posts.id = ?
        GROUP BY posts.id
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'sii', $user_id, $user_id, $post_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $post = mysqli_fetch_assoc($result);

    if (!$post['id']) {
        return null;
    }

    $post['hashtags'] = decode_json_array_agg($post['hashtags']);

    return $post;
}

/**
 * Функция добавляет публикацию в базу данных.
 * Функция возвращает id созданной публикации.
 * В случае неуспешного создания возвращается null.
 *
 * Ограничения:
 * Теги должны быть представлены в виде единой строки, разделенной
 * одинарными пробелами. Пробелы в начале и конце строки не допускаются.
 * Строка должна быть приведена к нижнему регистру.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  array{
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     author_id: int,
 *     content_type_id: int,
 *     tags: string;
 * }  $post_data - данные для добавления публикации
 *
 * @return int | null id созданной публикации
 */
function create_post(mysqli $db_connection, array $post_data)
{
    mysqli_begin_transaction($db_connection);

    $tags = $post_data['tags'] ? explode(
        TEXT_SEPARATOR,
        mysqli_real_escape_string($db_connection, $post_data['tags'])
    ) : [];

    $sql = "
        INSERT INTO posts (
            author_id,
            content_type_id,
            title,
            text_content,
            string_content
        ) VALUES (?, ?, ?, ?, ?)
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        'iisss',
        $post_data['author_id'],
        $post_data['content_type_id'],
        $post_data['title'],
        $post_data['text_content'],
        $post_data['string_content']
    );
    mysqli_stmt_execute($statement);

    $post_id = mysqli_insert_id($db_connection);

    if (!$post_id) {
        return null;
    }

    foreach ($tags as $tag) {
        $hashtag_success = add_hashtag_to_post($db_connection, $tag, $post_id);

        if (!$hashtag_success) {
            mysqli_rollback($db_connection);

            return null;
        }
    }

    mysqli_commit($db_connection);

    return $post_id;
}

/**
 * Функция проверяет наличие публикации в базе данных по заданному id.
 * В случае ошибки запроса возвращается отрицательный результат (false).
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $post_id  - id публикации
 *
 * @return bool - результат проверки
 */
function check_post(mysqli $db_connection, int $post_id): bool
{
    $sql = "SELECT posts.id FROM posts WHERE posts.id = ?";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'i', $post_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return false;
    }

    $post = mysqli_fetch_assoc($result);

    return boolval($post['id']);
}

/**
 * Функция получает основные данные для заданнной публикации из базы данных.
 * Функция возвращает данные в виде ассоциативного массива.
 * В случае неуспешного запроса функция возвращает null.
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $post_id - id публикации
 *
 * @return null | array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string
 *     created_at: string;
 *     views_count: int,
 *     author_id: int,
 *     content_type_id: int
 * }  - данные публикации
 */
function get_basic_post_data(mysqli $db_connection, int $post_id)
{
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            posts.author_id,
            posts.content_type_id
        FROM posts
        WHERE posts.id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'i', $post_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result) ?? null;
}

