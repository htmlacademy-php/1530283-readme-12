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
 * @param  array[
 *                                 'sort_type' => 'views_count' | 'likes_count' | 'created_at' | null,
 *                                 'is_order_reversed' => bool | null,
 *                                 'content_type_id' => int | null] $config параметры запроса
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

    $content_type_id = $config['content_type_id'] ? mysqli_real_escape_string(
        $db_connection,
        $config['content_type_id']
    ) : null;

    $is_order_reversed = $config['is_order_reversed'] ?? false;

    $order_direction_sql = $is_order_reversed ? 'ASC' : 'DESC';

    $filter_sql = $content_type_id
        ? "WHERE content_types.id = $content_type_id"
        : '';

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
            content_types.icon AS content_type,
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

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция получает публикацию из базы данных по заданному id.
 * В случае успешного запроса функция возвращается публикация
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  ресурс соединения с базой данных
 * @param  int     $id             id публикации
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
 * }
 */
function get_post(mysqli $db_connection, int $id)
{
    $id = mysqli_real_escape_string($db_connection, $id);

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
            content_types.icon AS content_type,
            COUNT(DISTINCT likes.author_id) AS likes_count,
            COUNT(DISTINCT comments.id) AS comments_count
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
        WHERE posts.id = $id
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    $post = mysqli_fetch_assoc($result);

    return $post['id'] ? $post : null;
}

// todo: add phpDoc
function create_post(mysqli $db_connection, array $post_data)
{
    $title = mysqli_real_escape_string($db_connection, $post_data['title']);
    $string_content =
        mysqli_real_escape_string($db_connection, $post_data['string_content']);
    $text_content =
        mysqli_real_escape_string($db_connection, $post_data['text_content']);
    $author_id =
        mysqli_real_escape_string($db_connection, $post_data['author_id']);
    $content_type_id =
        mysqli_real_escape_string(
            $db_connection,
            $post_data['content_type_id']
        );
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
        ) VALUES (
            $author_id,
            $content_type_id,
            '$title',
            '$text_content',
            '$string_content'
        )
    ";

    $result = mysqli_query($db_connection, $sql);

    if (mysqli_error($db_connection))
    {
        var_dump(mysqli_error($db_connection));
        exit();
    }

    if (!$result) {
        return null;
    }

    $post_id = mysqli_insert_id($db_connection);

    foreach ($tags as $tag) {
        $hashtag_id = add_hashtag($db_connection, $tag);

        if ($hashtag_id) {
            create_post_hashtag($db_connection, $post_id, $hashtag_id);
        }
    }

    return $post_id;
}
