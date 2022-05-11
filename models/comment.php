<?php

/**
 * @param  mysqli  $db_connection
 * @param  int     $post_id
 *
 * @return array|null
 */
function get_comments(mysqli $db_connection, int $post_id)
{
    $sql = "
        SELECT 
            comments.id,
            comments.created_at,
            comments.content,
            users.login as author_login,
            users.avatar_url as author_avatar
        FROM comments
            JOIN users
                ON comments.author_id = users.id
        WHERE comments.post_id = $post_id
    ";

    $result = mysqli_query($db_connection, $sql);

    if ( ! $result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
