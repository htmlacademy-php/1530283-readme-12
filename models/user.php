<?php

require_once 'utils/functions.php';

/**
 * Функция получает пользователя из базы данных по заданному id.
 * В случае успешного запроса функция возвращается публикация
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int  $reference_user_id  - id пользователя, относительно которого
 * проверяется статус подписки
 *
 * return null | array{
 *     id: int,
 *     created_at: string,
 *     login: string,
 *     email: string,
 *     avatar_url: string,
 *     subscribers_count: int,
 *     posts_count: int,
 *     is_observable: bool
 * }
 */
function get_user(mysqli $db_connection, int $user_id, int $reference_user_id)
{
    $sql = "
        SELECT
            users.id,
            users.created_at,
            users.login,
            users.email,
            users.avatar_url,
            COUNT(DISTINCT subscriptions.subscriber_id) as subscribers_count,
            COUNT(DISTINCT posts.id) as posts_count,
        JSON_CONTAINS(JSON_ARRAYAGG(subscriptions.subscriber_id), ?) AS is_observable
        FROM users
            LEFT JOIN subscriptions
                ON users.id = subscriptions.observable_id
            LEFT JOIN posts
                ON users.id = posts.author_id
        WHERE users.id = ?
        GROUP BY users.id
    ";

    $result = execute_select_query(
        $db_connection,
        $sql,
        'si',
        $reference_user_id,
        $user_id
    );

    if (!$result) {
        return null;
    }

    $user = mysqli_fetch_assoc($result);

    return isset($user['id']) ? $user : null;
}

/**
 * Функция добавляет пользователя в базу данных.
 * Функция возвращает id созданноого пользователя.
 * В случае неуспешного создания возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  array{
 *     email: string,
 *     login: string,
 *     password_hash: string,
 *     avatar_url: int,
 * }  $user_data - данные для добавления пользователя
 *
 * @return int | null id созданного пользователя
 */
function create_user(mysqli $db_connection, array $user_data)
{
    $email = $user_data['email'] ?? null;
    $login = $user_data['login'] ?? null;
    $password_hash = $user_data['password_hash'] ?? null;
    $avatar_url = $user_data['avatar_url'] ?? null;

    if (!$email || !$login || !$password_hash) {
        return null;
    }

    $sql = "
        INSERT INTO users (
            email,
            login,
            password_hash,
            avatar_url
        ) VALUES (?, ?, ?, ?)
    ";

    if (!execute_non_select_query(
        $db_connection,
        $sql,
        'ssss',
        $email,
        $login,
        $password_hash,
        $avatar_url
    )
    ) {
        return null;
    }

    return mysqli_insert_id($db_connection);
}

/**
 * Функция получает пользователя из базы данных по заданному email.
 * В случае успешного запроса функция возвращает пользователя
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $user_email  -  email пользователя
 *
 * @return null | array{
 *    id: int,
 *    email: string,
 *    password_hash: string
 * } - данные пользователя
 */
function get_user_by_email(mysqli $db_connection, string $user_email)
{
    $sql = "
        SELECT
               id,
               email,
               login,
               created_at,
               avatar_url,
               password_hash
        FROM users
        WHERE email = ?
    ";

    $result = execute_select_query($db_connection, $sql, 's', $user_email);

    if (!$result) {
        return null;
    }

    $user = mysqli_fetch_assoc($result);

    return isset($user['id']) ? $user : null;
}

/**
 * Функция проверяет наличие пользователя в базе данных по заданному id.
 * В случае ошибки запроса возвращается отрицательный результат (false).
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id публикации
 *
 * @return bool - результат проверки
 */
function check_user(mysqli $db_connection, int $user_id): bool
{
    $sql = "SELECT users.id FROM users WHERE users.id = ?";

    $result = execute_select_query($db_connection, $sql, 'i', $user_id);

    if (!$result) {
        return false;
    }

    $user = mysqli_fetch_assoc($result);

    return isset($user['id']);
}
