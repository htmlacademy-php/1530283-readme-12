<?php

/**
 * Функция проверяет наличие подписки в базе данных на заданного пользователя
 * и подписчика.
 * В случае ошибки запроса возвращается отрицательный результат (false).
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id подписчика
 * @param  int  $observable_id  - id пользователя, на которого оформлена подписка
 *
 * @return bool - результат проверки наличия подписки
 */
function check_subscription(
    mysqli $db_connection,
    int $user_id,
    int $observable_id
): bool {
    $sql = "
        SELECT subscriber_id, observable_id
        FROM subscriptions
        WHERE subscriber_id = ? AND observable_id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $user_id, $observable_id);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return false;
    }

    return boolval(mysqli_fetch_assoc($result));
}

/**
 * Функция добаляет подписку в базу данных.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id подписчика
 * @param  int  $observable_id  - id пользователя, на которого оформляется
 * подписка
 *
 * @return bool - успешность выполнения операции
 */
function create_subscription(
    mysqli $db_connection,
    int $user_id,
    int $observable_id
): bool {
    $sql =
        "INSERT INTO subscriptions (subscriber_id, observable_id) VALUES (?, ?)";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $user_id, $observable_id);

    return mysqli_stmt_execute($statement);
}

/**
 * Функция удаляет подписку из базы данных.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id подписчика
 * @param  int  $observable_id  - id пользователя, от которого производится
 * отписка
 *
 * @return bool - успешность выполнения операции
 */
function delete_subscription(
    mysqli $db_connection,
    int $user_id,
    int $observable_id
): bool {
    $sql =
        "DELETE FROM subscriptions WHERE subscriber_id = ? AND observable_id = ?";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $user_id, $observable_id);

    return mysqli_stmt_execute($statement);
}

/**
 * Функция измнения состояния подписки. В случае отсутствия в базе данных
 * подписки для заданного пользователя и подписчика - производится добавление
 * подписки, в случае наличия - удаление.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $user_id  - id подписчика
 * @param  int  $observable_id  - id пользователя, относительно которого
 * изменяется статус подписки
 *
 * @return bool - успешность выполнения операции
 */
function toggle_subscription(
    mysqli $db_connection,
    int $user_id,
    int $observable_id
): bool {
    $is_subscribed =
        check_subscription($db_connection, $user_id, $observable_id);

    $change_status =
        $is_subscribed ? 'delete_subscription' : 'create_subscription';

    return $change_status($db_connection, $user_id, $observable_id);
}

// todo: add phpDoc to users
/**
 * @param  mysqli  $db_connection
 * @param  int  $user_id
 *
 * @return array|null
 */
function get_observable_users(
    mysqli $db_connection,
    int $user_id,
    int $ref_user_id
) {
    $sql = "
        SELECT
            users.id AS id,
            users.avatar_url AS avatar_url,
            users.login AS login,
            users.created_at AS created_at,
            (SELECT
                JSON_CONTAINS(JSON_ARRAYAGG(sub_subscriptions.subscriber_id), ?)
            FROM users sub_users
            LEFT JOIN subscriptions sub_subscriptions
                ON sub_users.id = sub_subscriptions.observable_id
            WHERE sub_subscriptions.observable_id = users.id
            GROUP BY sub_users.id) AS is_observable,
            (SELECT COUNT(posts.id)
            FROM posts
            WHERE posts.author_id = users.id) AS posts_count,
            (SELECT COUNT(sub_subscriptions.subscriber_id)
            FROM subscriptions sub_subscriptions
            WHERE sub_subscriptions.observable_id = users.id) AS subscribers_count
        FROM subscriptions
        JOIN users ON subscriptions.observable_id = users.id
        WHERE subscriptions.subscriber_id = ?
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        'si',
        $ref_user_id,
        $user_id
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
