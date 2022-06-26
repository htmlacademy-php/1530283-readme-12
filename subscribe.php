<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'init/mail.php';
require_once 'models/user.php';
require_once 'models/subscription.php';
require_once 'utils/notifiers.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 * @var PHPMailer $mail - экзмепляр PHPMailer
 */

$observable_user = null;
$observable_id =
    filter_input(INPUT_GET, USER_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);

if ($observable_id) {
    $observable_user =
        get_user($db_connection, $observable_id, $user_session['id']);
}

if (!$observable_id || !$observable_user) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(['content' => 'Данный пользователь не существует']);
    exit();
}

if (intval($observable_id) === $user_session['id']) {
    http_response_code(BAD_REQUEST_STATUS);
    render_message_page(
        ['content' => 'Нельзя оформить подписку на собственный профиль']
    );
    exit();
}

$is_subscription_toggled =
    toggle_subscription($db_connection, $user_session['id'], $observable_id);

if (!$is_subscription_toggled) {
    http_response_code(SERVER_ERROR_STATUS);
    render_message_page(['content' => 'Произошла внутренняя ошибка сервера']);
    exit();
}

if (!$observable_user['is_observable']) {
    var_dump(notify_about_new_subscriber($mail, $observable_user, $user_session));
    var_dump(notify_about_new_subscriber($mail, $observable_user, $user_session));
    var_dump(notify_about_new_subscriber($mail, $observable_user, $user_session));
    exit();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
