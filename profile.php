<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'models/user.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Профиль ползователя',
    'user' => $user_session,
    'page_modifier' => 'profile',
    'basename' => $basename,
    'content' => '',
];

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT) ??
           $user_session['id'];
$is_own_profile = intval($user_id) === $user_session['id'];

$user = get_user($db_connection, $user_id);

if (!$user) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data
    );
    exit();
}

$user_content = include_template(
    'pages/profile/user.php',
    [
        'user' => $user,
        'is_own_profile' => $is_own_profile
    ]
);

$page_content = include_template(
    'pages/profile/page.php',
    [
        'user_content' => $user_content,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
