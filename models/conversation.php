<?php

require_once 'utils/functions.php';

/**
 * Функция получает из базы данных массив разговоров для заданного пользователя.
 * Функция возвращает разговоры, для которых создано хотя бы одно сообщения,
 * а также текущий разговор.
 * В случае ошибки запроса функция возвращает null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int|null  $current_conversation_id  - id текущего разговора
 *
 * @return null | array<int, array{
 *     id: int,
 *     interlocutor: array{
 *         id: int,
 *         login: string,
 *         avatar_url: string
 *     }
 * }> - массив разгворов
 */
function get_conversations(
    mysqli $db_connection,
    int $user_id,
    int $current_conversation_id = null
) {
    $sql = "
        SELECT
            conversations.id AS id,
            (IF(initiator_id = ?,
                JSON_OBJECT(
                    'id', interlocutors.id,
                    'login', interlocutors.login,
                    'avatar_url', interlocutors.avatar_url
                ),
                JSON_OBJECT(
                    'id', initiators.id,
                    'login', initiators.login,
                    'avatar_url', initiators.avatar_url
                ))) AS interlocutor,
               (SELECT
                       JSON_OBJECT(
                           'id', messages.id,
                           'content', messages.content,
                           'is_own', messages.author_id = ?,
                           'created_at', messages.created_at
                           ) as last_message_json
                   FROM messages   
                   WHERE messages.conversation_id = conversations.id
                   ORDER BY messages.created_at DESC
                   LIMIT 1) AS last_message,
               COUNT(DISTINCT
                   IF(!messages.is_read AND messages.author_id != ?,
                       messages.id, null)
                   ) AS unread_messages_count
        FROM conversations
        JOIN users initiators
            ON conversations.initiator_id = initiators.id
        JOIN users interlocutors
            ON conversations.interlocutor_id = interlocutors.id
        LEFT JOIN messages
            ON conversations.id = messages.conversation_id
        WHERE ((initiator_id = ?) OR (interlocutor_id = ?))
        GROUP BY conversations.id
        ORDER BY conversations.id = ? DESC,
            (SELECT messages.created_at
                FROM messages   
                WHERE messages.conversation_id = conversations.id
                ORDER BY messages.created_at DESC
                LIMIT 1) DESC
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'iiiiii',
        $user_id,
        $user_id,
        $user_id,
        $user_id,
        $user_id,
        $current_conversation_id
    );

    if (!$result) {
        return null;
    }

    $conversations = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($conversations as &$conversation) {
        $conversation['interlocutor'] =
            json_decode($conversation['interlocutor'], true);
        $conversation['last_message'] =
            json_decode($conversation['last_message'], true);
    }

    return array_filter(
        $conversations,
        function ($conversation) use ($current_conversation_id) {
            return $conversation['last_message']
                   || $conversation['id'] === $current_conversation_id;
    },
        ARRAY_FILTER_USE_BOTH
    );
}

/**
 * Функция получает из базы данных id разговора для заданных собеседников.
 * Порядок собеседников разговора не имеет значения.
 * В случае отсутствия разговора в базе данных, либо ошибки запроса,
 * функция возвращает null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id_1  - id первого собеседника разговора
 * @param  int  $user_id_2  - id второго собеседника разговора
 *
 * @return null | int - id разговора
 */
function get_conversation_id_by_users(
    mysqli $db_connection,
    int $user_id_1,
    int $user_id_2
) {
    $sql = "
        SELECT id FROM conversations
        WHERE
            (initiator_id = ? AND interlocutor_id = ?) OR
            (initiator_id = ? AND interlocutor_id = ?)
        LIMIT 1
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'iiii',
        $user_id_1,
        $user_id_2,
        $user_id_2,
        $user_id_1
    );

    if (!$result) {
        return null;
    }

    $conversation = mysqli_fetch_assoc($result);

    return $conversation ? $conversation['id'] : null;
}

/**
 * Функция проверяет наличие доступа к заданному разговору для заданного
 * пользователя. Пользователь имеет доступ к разговору если он является
 * одним из его участников.
 *
 * @param $db_connection  - ресурс соединения с базой данных
 * @param $user_id  - id пользователя
 * @param $conversation_id  - id разговора
 *
 * @return bool - наличие доступа к разговору
 */
function check_conversation_access(
    $db_connection,
    $user_id,
    $conversation_id
): bool {
    $sql = "
        SELECT id FROM conversations
        WHERE conversations.id = ? AND (
            conversations.initiator_id = ? OR conversations.interlocutor_id = ?
        )
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'iii',
        $conversation_id,
        $user_id,
        $user_id
    );

    if (!$result) {
        return false;
    }

    return boolval(mysqli_fetch_assoc($result));
}

/**
 * Функция добавляет в базу данных разговор между заданным пользователем и
 * заданным собеседником. Функция возвращает id созданого разговора. В случае,
 * если разговор для заданных собеседников уже существует в базе данных,
 * функция возвращает id существующего разговора.
 * В случае неуспешного выполнения запроса функция возвращает null.
 *
 * @param  mysqli  $db_connection
 * @param  int  $user_id
 * @param  int  $interlocutor_id
 *
 * @return null | int - id разговора
 */
function create_conversation(
    mysqli $db_connection,
    int $user_id,
    int $interlocutor_id
) {
    $existent_id = get_conversation_id_by_users(
        $db_connection,
        $user_id,
        $interlocutor_id
    );

    if ($existent_id) {
        return $existent_id;
    }

    $sql = "
        INSERT INTO conversations (initiator_id, interlocutor_id)
        VALUES (?, ?);   
    ";

    if (!execute_non_select_query(
        $db_connection,
        $sql,
        'ii',
        $user_id,
        $interlocutor_id
    )
    ) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}
