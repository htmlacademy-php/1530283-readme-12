<?php

require_once 'utils/functions.php';

/**
 * Функция получает сообщения из базы данных для заданного разговора.
 * В случае неуспешного запроса функция возвращает null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $conversation_id  - id разговора
 *
 * @return null | array<int, array{
 *     id: int,
 *     content: string,
 *     created_at: string,
 *     author: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     }
 * }> - массив сообщений
 */
function get_messages(mysqli $db_connection, int $conversation_id)
{
    $sql = "
        SELECT 
               messages.id AS id,
               messages.content AS content,
               messages.created_at AS created_at,
               (JSON_OBJECT(
                   'id', users.id,
                   'login', users.login,
                   'avatar_url', users.avatar_url
               )) AS author
        FROM messages
        JOIN users on messages.author_id = users.id
        WHERE messages.conversation_id = ?
        ORDER BY messages.created_at DESC
    ";

    $result = execute_select_query($db_connection, $sql, 'i', $conversation_id);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// todo: write sql query, bind params, add phpDoc
function create_message(
    mysqli $db_connection,
    int $user_id,
    array $message_data
) {
    $sql = "";

    if (!execute_non_select_query($db_connection, $sql, '', $message_data)) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}
