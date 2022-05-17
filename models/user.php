<?php

/**
 * Функция получает пользователя из базы данных по заданному id.
 * В случае успешного запроса функция возвращается публикация
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  ресурс соединения с базой данных
 * @param  int  $id  id пользователя
 *
 * return null | array{
 *     id: int,
 *     created_at: string,
 *     login: string,
 *     email: string,
 *     avatar_url: string,
 *     subscribers_count: int,
 *     posts_count: int,
 * }
 */
function get_user(mysqli $db_connection, int $id)
{
    $id = mysqli_real_escape_string($db_connection, $id);

    $sql = "
        SELECT
            users.id,
            users.created_at,
            users.login,
            users.email,
            users.avatar_url,
            COUNT(DISTINCT  subscriptions.subscriber_id) as subscribers_count,
            COUNT(DISTINCT posts.id) as posts_count
        FROM users
            LEFT JOIN subscriptions
                ON users.id = subscriptions.observable_id
            LEFT JOIN posts
                ON users.id = posts.author_id
        WHERE users.id = $id
        GROUP BY users.id
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    $user = mysqli_fetch_assoc($result);

    return $user['id'] ? $user : null;
}
