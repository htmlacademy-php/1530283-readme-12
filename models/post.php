<?php

/**
 * Функция принимает ресурс соединения с базой данный
 * и ассоциативныый массив с параметрами запроса
 * и возвращает массив с публикациями.
 * Параметры запроса позволяют задавать фильтрацию и сортировку публикаций.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  array[
 *                                 'sort' => 'views_count' | null,
 *                                 'content_type_id' => int | null] $config - параметры запроса
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
    list(
        'sort' => $sort,
        'content_type_id' => $content_type_id
        )
        = $config;

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
            COUNT(likes.author_id) AS likes_count,
            COUNT(comments.id) AS comments_count
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
    ";

    // todo: add sql injection escape
    if ($content_type_id) {
        $sql .= " WHERE content_types.id = $content_type_id";
    }

    $sql .= " GROUP BY posts.id";

    if ($sort === 'views_count') {
        $sql .= " ORDER BY posts.views_count";
    }

    $result = mysqli_query($db_connection, $sql);

    if ( ! $result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * @param  mysqli  $db_connection
 * @param  int     $id
 *
 * return
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
            users.login AS author_login,
            users.avatar_url AS author_avatar,
            content_types.icon AS content_type,
            COUNT(likes.author_id) AS likes_count,
            COUNT(comments.id) AS comments_count
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON posts.id = comments.post_id
        WHERE posts.id = $id
    ";

    $result = mysqli_query($db_connection, $sql);

    if ( ! $result) {
        return null;
    }

    return mysqli_fetch_assoc($result);
}
