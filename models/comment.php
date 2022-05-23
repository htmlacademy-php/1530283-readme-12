<?php

/**
 * Функция получает список комментариев к заданной публикации из базы данных.
 * В случае успешного запроса функция возвращается массив
 * комментариев в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  ресурс соединения с базой данных
 * @param  int  $post_id  id публикации
 *
 * @return null | array<int, array{
 *     id: int,
 *     created_at: string,
 *     content: string,
 *     author_login: string,
 *     author_avatar: string
 * }>
 */
function get_comments(mysqli $db_connection, int $post_id)
{
    $sql = "
        SELECT 
            comments.id,
            comments.created_at,
            comments.content,
            users.login as author_login,
            users.avatar_url as author_avatar
        FROM comments
            JOIN users
                ON comments.author_id = users.id
        WHERE comments.post_id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'i', $post_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
