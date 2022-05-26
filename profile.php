<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'init/db.php';

/**
 * @var mysqli | false | null $db_connection - ресурс соединения с базой данных
 */

session_start();
$user = $_SESSION['user'] ?? null;

check_db_connection($db_connection);


$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Профиль ползователя',
    'user' => $user,
    'page_modifier' => 'profile',
    'basename' => $basename,
    'content' => '',
];

$page_content = include_template('profile.php', []);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
