<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

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

$page_content = include_template('pages/profile.php', []);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
