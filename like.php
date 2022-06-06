<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'models/post.php';
require_once 'models/like.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$is_post_id_valid = false;
$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

if ($post_id) {
    $is_post_id_valid = check_post($db_connection, $post_id);
}

if (!$post_id || !$is_post_id_valid) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(['content' => 'Данная публикация не существует']);
    exit();
}


$is_like_toggled = toggle_like($db_connection, $user_session['id'], $post_id);


if (!$is_like_toggled) {
    http_response_code(SERVER_ERROR_STATUS);
    render_message_page(['content' => 'Произошла внутренняя ошибка сервера']);
    exit();
}

header('Location: ' . $_SERVER['HTTP_REFERER'] . "#post-$post_id");
