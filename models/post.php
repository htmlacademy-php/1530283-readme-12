<?php

require_once 'models/hashtag.php';
require_once 'models/post_hashtag.php';

/**
 * Функция получает список публикаций из базы данных.
 * Функция принимает ресурс соединения с базой данный
 * и ассоциативныый массив с параметрами запроса.
 * Параметры запроса позволяют задавать фильтрацию и сортировку публикаций.
 * В случае успешного запроса функция возвращается массив
 * публикаций в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  ресурс соединения с базой данных
 * @param  array{
 *     sort_type: 'views_count' | 'likes_count' | 'created_at' | null,
 *     is_order_reversed: bool | null,
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
 *     author_login: string,
 *     author_avatar: string,
 *     content_type: string,
 *     likes_count: int,
 *     comments_count: int,
 * }>
 */
function get_posts(mysqli $db_connection, $config = [])
{
    $sort_type = $config['sort_type'] ? mysqli_real_escape_string(
        $db_connection,
        $config['sort_type']
    ) : null;
    $content_type_id = $config['content_type_id'] ?? '';
    $is_order_reversed = $config['is_order_reversed'] ?? false;

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
            users.login AS author_login,
            users.avatar_url AS author_avatar,
            content_types.type AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
        $filter_sql
        GROUP BY posts.id
        $sort_sql
    ";

    $statement = mysqli_prepare($db_connection, $sql);

    if ($filter_sql) {
        mysqli_stmt_bind_param($statement, 'i', $content_type_id);
    }

    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция получает публикацию из базы данных по заданному id.
 * В случае успешного запроса функция возвращает публикацию
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $id  - id публикации
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
 *     hashtags: array
 * } - данные публикации
 */
function get_post(mysqli $db_connection, int $id)
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
            GROUP_CONCAT(DISTINCT hashtags.name) AS hashtags_string
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
            LEFT JOIN posts_hashtags ON posts.id = posts_hashtags.post_id
            LEFT JOIN hashtags ON posts_hashtags.hashtag_id = hashtags.id
        WHERE posts.id = ?
        GROUP BY posts.id
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'i', $id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $post = mysqli_fetch_assoc($result);

    if (!$post['id']) {
        return null;
    }

    $post['hashtags'] =
        $post['hashtags_string'] ? explode(',', $post['hashtags_string']) : [];
    unset($post['hashtags_string']);

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

    if (mysqli_error($db_connection)) {
        return null;
    }

    $post_id = mysqli_insert_id($db_connection);

    if (!$post_id) {
        return null;
    }

    foreach ($tags as $tag) {
        add_hashtag_to_post($db_connection, $tag, $post_id);
    }

    return $post_id;
}
