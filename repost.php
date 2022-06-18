<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'models/post.php';
require_once 'models/repost.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$post = null;
$post_id = filter_input(INPUT_GET, POST_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);

if ($post_id) {
    $post = get_basic_post_data($db_connection, $post_id);
}

if (!$post_id || !$post) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(['content' => 'Данная публикация не существует']);
    exit();
}

$is_own_post = $post['author_id'] === $user_session['id'];

if ($is_own_post) {
    http_response_code(BAD_REQUEST_STATUS);
    render_message_page(
        ['content' => 'Невозможно создать репост собственной публикации']
    );
    exit();
}

$repost_id = create_repost($db_connection, $user_session['id'], $post);

if (!$repost_id) {
    http_response_code(SERVER_ERROR_STATUS);
    render_message_page(['content' => 'Произошла внутренняя ошибка сервера']);
    exit();
}

header("Location: profile.php#post-$repost_id");
