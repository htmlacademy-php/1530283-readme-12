<?php

// todo: add phpDoc
function get_hashtag(mysqli $db_connection, string $name)
{
    $name = mysqli_real_escape_string($db_connection, $name);

    $sql = "
        SELECT
            id,
            name
        FROM hashtags
        WHERE name = $name
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    $hashtag = mysqli_fetch_assoc($result);

    return $hashtag['id'] ? $hashtag : null;
}

// todo: add phpDoc
function get_hashtags(mysqli $db_connection, int $post_id) {
    $post_id = mysqli_real_escape_string($db_connection, $post_id);

    $sql = "
        SELECT
            id,
            name
        FROM hashtags
        JOIN posts_hashtags
            ON hashtags.id = posts_hashtags.hashtag_id
        WHERE posts_hashtags.post_id = $post_id
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// todo: add phpDoc
function create_hashtag(mysqli $db_connection, string $name)
{
    $name = mysqli_real_escape_string($db_connection, $name);

    $sql = "INSERT INTO hashtags (name) VALUES ('$name')";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}


// todo: add phpDoc
function add_hashtag(mysqli $db_connection,string $name)
{
    $hashtag_id = get_hashtag($db_connection, $name);

    if ($hashtag_id) {
        return $hashtag_id;
    }

    return create_hashtag($db_connection, $name);
}
