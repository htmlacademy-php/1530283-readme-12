<?php

require_once 'utils/functions.php';
require_once 'models/post.php';
require_once 'models/hashtag.php';

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
    $original_author_id = $post_data['author_id'] ?? null;
    if (!$original_author_id || $original_author_id === $user_id) {
        return null;
    }

    $original_post_id = $post_data['id'] ?? null;
    if (!$original_post_id) {
        return null;
    }

    $post_data['author_id'] = $user_id;

    mysqli_begin_transaction($db_connection);

    $hashtags = get_hashtags($db_connection, $original_post_id);
    if (!is_array($hashtags)) {
        mysqli_rollback($db_connection);

        return null;
    }

    $post_data['tags'] =
        implode(TEXT_SEPARATOR, array_column($hashtags, 'name'));

    $repost_id = create_post($db_connection, $post_data);

    if (!$repost_id) {
        mysqli_rollback($db_connection);

        return null;
    }

    $sql = "INSERT INTO reposts (original_post_id, repost_id) VALUES (?, ?)";

    if (!execute_non_select_query(
        $db_connection,
        $sql,
        'ii',
        $original_post_id,
        $repost_id
    )
    ) {
        mysqli_rollback($db_connection);

        return null;
    }

    mysqli_commit($db_connection);

    return $repost_id;
}
