<?php

require_once 'utils/functions.php';

/**
 * Функция получает список комментариев к заданной публикации из базы данных.
 * В случае успешного запроса функция возвращается массив
 * комментариев в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $post_id  - id публикации
 * @param  int | null  $limit  - ограничение по количеству (опционально)
 *
 * @return null | array<int, array{
 *     id: int,
 *     created_at: string,
 *     content: string,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     },
 * }>
 */
function get_comments(mysqli $db_connection, int $post_id, int $limit = null)
{
    $limit_sql = $limit ? 'LIMIT ?' : '';
    $sql = "
        SELECT 
            comments.id,
            comments.created_at,
            comments.content,
            JSON_OBJECT(
                'id', users.id,
                'login', users.login,
                'avatar_url', users.avatar_url
            ) AS author
        FROM comments
            JOIN users
                ON comments.author_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at DESC
        $limit_sql
    ";

    $result = is_null($limit) ? execute_select_query(
        $db_connection,
        $sql,
        'i',
        $post_id
    ) : execute_select_query($db_connection, $sql, 'ii', $post_id, $limit);

    if (!$result) {
        return null;
    }

    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($comments as &$comment) {
        $comment['author'] = json_decode($comment['author'], true);
    }

    return $comments;
}

/**
 * Функция добавляет комментарий к публикации в базу данных.
 * Функция возвращает id созданного комментария.
 * В случае неуспешного создания возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  array  $comment_data  - данные для добавления комментария
 *
 * @return int | null - id созданного комментария
 */
function create_comment(mysqli $db_connection, array $comment_data)
{
    $sql = "
        INSERT INTO comments (
            author_id,
            post_id,
            content
        ) VALUES (?, ?, ?)
    ";

    if (!execute_non_select_query(
        $db_connection,
        $sql,
        'iis',
        $comment_data['author_id'],
        $comment_data['post_id'],
        $comment_data['content']
    )
    ) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}
