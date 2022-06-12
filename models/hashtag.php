<?php

require_once 'models/post_hashtag.php';

/**
 * Функция возвращает хэштег из базы данных по его названию.
 * Функция возвращает хэштег в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * Ограничения: название хэштега должно представлять собой строку без пробелов,
 * приведенную к нижнему регистру.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $hashtag_name  - название хэштега
 *
 * @return null | array{
 *     id: int,
 *     name: string
 * } - хэштег
 */
function get_hashtag(mysqli $db_connection, string $hashtag_name)
{
    $sql = "
        SELECT
            id,
            name
        FROM hashtags
        WHERE name = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 's', $hashtag_name);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    $hashtag = mysqli_fetch_assoc($result);

    return $hashtag['id'] ? $hashtag : null;
}

/**
 * Функция возвращает массив хэштегов для заданной публикации по id.
 * Хэштеги представляются в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $post_id  -  id публикации
 *
 * @return null | array[int, array{
 *     id: int,
 *     name: string
 * }] - массив хэштегов
 */
function get_hashtags(mysqli $db_connection, int $post_id)
{
    $sql = "
        SELECT
            id,
            name
        FROM hashtags
        JOIN posts_hashtags
            ON hashtags.id = posts_hashtags.hashtag_id
        WHERE posts_hashtags.post_id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 's', $post_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция добавляет хэштег в базу данных.
 * Функция приниманет название хэштега и возвращает id созданного хэштега.
 * В случае неуспешного создания возвращается null.
 *
 * Ограничения: название хэштега должно представлять собой строку без пробелов,
 * приведенную к нижнему регистру.
 *
 * @param  mysqli  $db_connection
 * @param  string  $name
 *
 * @return int | null - id созданного хэштега
 */
function create_hashtag(mysqli $db_connection, string $name)
{
    $name = mysqli_real_escape_string($db_connection, $name);

    $sql = "INSERT INTO hashtags (name) VALUES (?)";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 's', $name);
    mysqli_stmt_execute($statement);

    if (mysqli_error($db_connection)) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}


// todo: add transaction ?
/**
 * Функция добавляет хэштег к существующей публикации.
 * Функция принимает название хэштега и id публикации. В случае, если
 * переданных хэштег отсутвует в базе данных, то он добавляется в базу данных
 * с присвоением уникального id. После определения id хэштега в базу данных
 * добавляется связь между публикацией и хэштегом, и в случае успешного запроса
 * возвращается true.
 * В случае неуспешного запроса возвращается false.
 *
 * Ограничения: название хэштега должно представлять собой строку без пробелов,
 * приведенную к нижнему регистру.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $name  - название хэштега
 * @param  int  $post_id  - id публикации
 *
 * @return bool - результат запроса
 */
function add_hashtag_to_post(
    mysqli $db_connection,
    string $name,
    int $post_id
): bool {
    $existent_hashtag = get_hashtag($db_connection, $name);

    $hashtag_id = is_array($existent_hashtag) && $existent_hashtag['id']
        ? $existent_hashtag['id'] : create_hashtag($db_connection, $name);

    if (!$hashtag_id) {
        return false;
    }

    return create_post_hashtag($db_connection, $post_id, $hashtag_id);
}
