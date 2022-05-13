<?php

// todo: add phpDoc
function create_post_hashtag(
    mysqli $db_connection,
    int $post_id,
    int $hashtag_id
) {
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
