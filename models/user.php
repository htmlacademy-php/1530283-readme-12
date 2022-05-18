<?php

/**
 * Функция получает пользователя из базы данных по заданному id.
 * В случае успешного запроса функция возвращается публикация
 * в виде ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $id  - id пользователя
 *
 * return null | array{
 *     id: int,
 *     created_at: string,
 *     login: string,
 *     email: string,
 *     avatar_url: string,
 *     subscribers_count: int,
 *     posts_count: int,
 * }
 */
function get_user(mysqli $db_connection, int $id)
{
    $id = mysqli_real_escape_string($db_connection, $id);

    $sql = "
        SELECT
            users.id,
            users.created_at,
            users.login,
            users.email,
            users.avatar_url,
            COUNT(DISTINCT  subscriptions.subscriber_id) as subscribers_count,
            COUNT(DISTINCT posts.id) as posts_count
        FROM users
            LEFT JOIN subscriptions
                ON users.id = subscriptions.observable_id
            LEFT JOIN posts
                ON users.id = posts.author_id
        WHERE users.id = $id
        GROUP BY users.id
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    $user = mysqli_fetch_assoc($result);

    return $user['id'] ? $user : null;
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
    $email = mysqli_real_escape_string($db_connection, $user_data['email']);
    $login =
        mysqli_real_escape_string($db_connection, $user_data['login']);
    $password_hash =
        mysqli_real_escape_string($db_connection, $user_data['password_hash']);
    $avatar_url = $user_data['avatar_url'];

    $sql = "
        INSERT INTO users (
            email,
            login,
            password_hash,
            avatar_url
        ) VALUES (
            '$email',
            '$login',
            '$password_hash',
            '$avatar_url'
        )
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    $user_id = mysqli_insert_id($db_connection);

    return $user_id ? intval($user_id) : null;
}


/**
 * Функция проверяет наличие в базе данных пользователя с заданным email.
 * В случае наличия пользователя вовзращается true, при отсутсвии пользователя,
 * либо при ошибке запроса возвращается false.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $email  -  проверяемый email
 *
 * @return bool результат проверки
 */
function check_email_existence(mysqli $db_connection, string $email): bool
{
    $email = mysqli_real_escape_string($db_connection, $email);

    $sql = "SELECT id FROM users WHERE email = '$email'";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return false;
    }

    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        return false;
    }

    return boolval($user['id']);
}
