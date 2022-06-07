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