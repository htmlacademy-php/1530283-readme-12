<?php

/**
 * Функция проверяет наличие лайка в базе данных для заданного пользователя
 * и публикации.
 * В случае ошибки запроса возвращается отрицательный результат (false).
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $user_id - id пользователя
 * @param  int  $post_id - id публикации
 *
 * @return bool - результат проверки наличия лайка
 */
function check_like(mysqli $db_connection, int $user_id, int $post_id): bool
{
    $sql = "
        SELECT author_id, post_id
        FROM likes
        WHERE post_id = ? AND author_id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $post_id, $user_id);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return false;
    }

    return boolval(mysqli_fetch_assoc($result));
}

/**
 * Функция добаляет лайк в базу данных.
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $user_id - id пользователя
 * @param  int  $post_id - id публикации
 *
 * @return bool - успешность выполнения операции
 */
function create_like(mysqli $db_connection, int $user_id, int $post_id): bool
{
    $sql = "INSERT INTO likes (post_id, author_id) VALUES (?, ?)";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $post_id, $user_id);

    return mysqli_stmt_execute($statement);
}

/**
 * Функция удаляет лайк из базы данных.
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $user_id - id пользователя
 * @param  int  $post_id - id публикации
 *
 * @return bool - успешность выполнения операции
 */
function delete_like(mysqli $db_connection, int $user_id, int $post_id): bool
{
    $sql = "DELETE FROM likes WHERE post_id = ? AND author_id = ?";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $post_id, $user_id);

    return mysqli_stmt_execute($statement);
}

/**
 * Функция измнения состояния лайка. В случае отсутствия в базе данных лайка
 * для заданного пользователя и публикации - производится добавления лайка,
 * в случае наличия - удаление.
 *
 * @param  mysqli  $db_connection - ресурс соединения с базой данных
 * @param  int  $user_id - id пользователя
 * @param  int  $post_id - id публикации
 *
 * @return bool - успешность выполнения операции
 */
function toggle_like(mysqli $db_connection, int $user_id, int $post_id): bool
{
    $is_liked = check_like($db_connection, $user_id, $post_id);

    $change_status = $is_liked ? 'delete_like' : 'create_like';

    return $change_status($db_connection, $user_id, $post_id);
}
