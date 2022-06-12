<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'models/user.php';
require_once 'models/subscription.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$is_user_id_valid = false;
$user_id = filter_input(INPUT_GET, USER_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);

if ($user_id) {
    $is_user_id_valid = check_user($db_connection, $user_id);
}

if (!$user_id || !$is_user_id_valid) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(['content' => 'Данный пользователь не существует']);
    exit();
}

if (intval($user_id) === $user_session['id']) {
    http_response_code(BAD_REQUEST_STATUS);
    render_message_page(['content' => 'Нельзя оформить подписку на собственный профиль']);
    exit();
}

$is_subscription_toggled =
    toggle_subscription($db_connection, $user_session['id'], $user_id);

if (!$is_subscription_toggled) {
    http_response_code(SERVER_ERROR_STATUS);
    render_message_page(['content' => 'Произошла внутренняя ошибка сервера']);
    exit();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
