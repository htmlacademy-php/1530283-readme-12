<?php

require_once 'utils/functions.php';
require_once 'models/conversation.php';

/**
 * Функция получает сообщения из базы данных для заданного разговора.
 * В случае неуспешного запроса функция возвращает null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
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
 *     },
 *     is_own: bool
 * }> - массив сообщений
 */
function get_messages(mysqli $db_connection, int $user_id, int $conversation_id)
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
               )) AS author,
               (users.id = ?) AS is_own
        FROM messages
        JOIN users on messages.author_id = users.id
        WHERE messages.conversation_id = ?
        ORDER BY messages.created_at
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'ii',
        $user_id,
        $conversation_id
    );

    if (!$result) {
        return null;
    }

    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($messages as &$message) {
        $message['author'] = json_decode($message['author'], true);
    }

    return $messages;
}

/**
 * Функция добавляет в базу данных сообщения к разговора от имени заданного
 * пользователя. В случае успешного добавления функция возвращает id созданного
 * сообщения. В случае неуспешного запроса - функция возвращает null.
 *
 * Ограничения:
 * Пользователь может добавляет сообщения только к тем разговорам, к которым
 * у него есть доступ. В случае отсутствия доступа функция вернет null.
 *
 * @param  mysqli  $db_connection  - ресурс соедения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  array  $message_data  - данные сообщения
 *
 * @return int | null - id созданного сообщения
 */
function create_message(
    mysqli $db_connection,
    int $user_id,
    array $message_data
) {
    $content = $message_data['content'] ?? null;
    $conversation_id = $message_data['conversation_id'] ?? null;

    if (!$content || !$conversation_id) {
        return null;
    }

    $is_access =
        check_conversation_access($db_connection, $user_id, $conversation_id);

    if (!$is_access) {
        return null;
    }

    $sql = "
        INSERT INTO messages (author_id, conversation_id, content)
        VALUES (?, ?, ?)
     ";

    if (!execute_non_select_query(
        $db_connection,
        $sql,
        'iis',
        $user_id,
        $conversation_id,
        $content
    )
    ) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}

/**
 * Функция получает из базы данных количество непрочитанных сообщений
 * для заданного пользователя.
 * В случае ошибки запроса функция возвращает 0.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 *
 * @return int - количество непрочитанных сообщений
 */
function get_unread_messages_count(mysqli $db_connection, int $user_id)
{
    $sql = "
        SELECT
            COUNT(DISTINCT messages.id) AS unread_messages_count
        FROM messages
        JOIN conversations
            ON messages.conversation_id = conversations.id
        WHERE (conversations.interlocutor_id = ? OR
               conversations.initiator_id = ?) AND
              messages.author_id != ? AND
              !messages.is_read
    ";

    $result =
        execute_select_query(
            $db_connection,
            $sql,
            'iii',
            $user_id,
            $user_id,
            $user_id
        );

    if (!$result) {
        return 0;
    }

    $count = mysqli_fetch_assoc($result);

    return $count['unread_messages_count'] ?? 0;
}

/**
 * Функция помечает все входящие сообщения как прочитанные для заданного
 * пользователя и разговороа.
 * Функция возвращает результат выполнения операции в булевом формате.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int  $conversation_id  - id разговора
 *
 * @return bool - результат выполнения операции
 */
function read_conversation_messages(
    mysqli $db_connection,
    int $user_id,
    int $conversation_id
): bool {
    $sql = "
        UPDATE messages
        SET messages.is_read = true
        WHERE messages.conversation_id = ? AND
              messages.author_id != ? AND
              !messages.is_read
    ";

    return execute_non_select_query(
        $db_connection,
        $sql,
        'ii',
        $conversation_id,
        $user_id
    );
}
