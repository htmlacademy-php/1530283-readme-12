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

/**
 * Функция получает подписки для заданного подписчика.
 * В случае успешного запроса функция возвращает массив данных о пользователях,
 * на которых подписан заданный подписчик, в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединеня с базой данных
 * @param  int  $user_id  - id пользователя
 * @param  int  $subscriber_id  - id подписчика
 *
 * @return null | array<int, array{
 *     id: int,
 *     avatar_url: string,
 *     login: string,
 *     created_at: string,
 *     posts_count: int,
 *     subscribers_count: int,
 *     is_observable: 0 | 1,
 *     is_user: bool
 * }>
 */
function get_subscriptions_by_subscriber(
    mysqli $db_connection,
    int $user_id,
    int $subscriber_id
) {
    $sql = "
        SELECT
            users.id AS id,
            users.avatar_url AS avatar_url,
            users.login AS login,
            users.created_at AS created_at,
            JSON_CONTAINS(JSON_ARRAYAGG(sub_subscriptions.subscriber_id), ?) AS is_observable,
            COUNT(DISTINCT posts.id) AS posts_count,
            COUNT(DISTINCT sub_subscriptions.subscriber_id) AS subscribers_count,
            (users.id = ?) AS is_user
        FROM subscriptions
        JOIN users ON subscriptions.observable_id = users.id
        LEFT JOIN posts 
            ON users.id = posts.author_id
        LEFT JOIN subscriptions sub_subscriptions
            ON sub_subscriptions.observable_id = users.id
        WHERE subscriptions.subscriber_id = ?
        GROUP BY users.id
    ";

    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        'sii',
        $user_id,
        $user_id,
        $subscriber_id
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    var_dump(mysqli_error($db_connection));
    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
