<?php

require_once 'models/post.php';

/**
 * Функция добавляет репост в базу данных.
 * Функция возвращает id созданного репоста.
 * В случае неуспешной операции функция возвращает null.
 *
 * Ограничения:
 * 1. Данные оригинальной публикаци должны быть достаточными для
 * добавления публикации в базу данных при помощи функции create_post.
 * 2. Запрещено создавать репосты собственных публикаций
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  array  $post_data  - данные оригинальной публикации
 *
 * @return null | int - id репоста
 */
function create_repost(
    mysqli $db_connection,
    int $user_id,
    array $post_data
) {
    if ($post_data['author_id'] = $user_id) {
        return null;
    }

    $original_post_id = $post_data['id'];
    $post_data['author_id'] = $user_id;

    mysqli_begin_transaction($db_connection);

    $repost_id = create_post($db_connection, $post_data);

    $sql = "INSERT INTO reposts (original_post_id, repost_id) VALUES (?, ?)";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $original_post_id, $repost_id);
    mysqli_stmt_execute($statement);

    if (mysqli_error($db_connection)) {
        mysqli_rollback($db_connection);

        return null;
    }

    mysqli_commit($db_connection);

    return $repost_id;
}
