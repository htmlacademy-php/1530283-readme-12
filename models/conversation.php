<?php

require_once 'utils/functions.php';

// todo: add phpDoc
function get_conversations(
    mysqli $db_connection,
    int $user_id,
    int $current_conversation_id = null
) {
    $sql = "
        SELECT id, initiator_id, interlocutor_id FROM conversations
        WHERE (initiator_id = ?) OR (interlocutor_id = ?)
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'ii',
        $user_id,
        $user_id
    );

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result);
}

// todo: add phpDoc
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

// todo: add phpDoc
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
