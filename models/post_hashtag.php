<?php

/**
 * Функция связывает хэштег с публикацией в базе данных.
 * Функция принимает id публикации и id хэштега и возвращает true,
 * в случае успешного создания записи о связи.
 * В случае неуспешного запроса возвращается false.
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $post_id - id публикации
 * @param  int  $hashtag_id - id хэштега
 *
 * @return bool - результат запроса
 */
function create_post_hashtag(
    mysqli $db_connection,
    int $post_id,
    int $hashtag_id
): bool {
    $post_id = mysqli_real_escape_string($db_connection, $post_id);
    $hashtag_id = mysqli_real_escape_string($db_connection, $hashtag_id);

    $sql = "
        INSERT INTO posts_hashtags (
            post_id,
            hashtag_id)
        VALUES (
            '$post_id',
            '$hashtag_id'
        );
    ";

    $result = mysqli_query($db_connection, $sql);

    return boolval($result);
}
