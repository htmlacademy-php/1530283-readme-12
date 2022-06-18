<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'utils/helpers.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Сообщения',
    'user' => $user_session,
    'page_modifier' => 'messages',
    'basename' => $basename,
];

$page_content = include_template('pages/messages/page.php', []);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
