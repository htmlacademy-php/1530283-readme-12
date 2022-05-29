<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/login-form-validators.php';
require_once 'models/user.php';
require_once 'init/db.php';

/**
 * @var mysqli | false | null $db_connection - ресурс соединения с базой данных
 */

session_start();
$user = $_SESSION['user'] ?? null;

check_db_connection($db_connection);

if (!$user) {
    $form_data = [];
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        list(
            'form_data' => $form_data,
            'errors' => $errors,
            ) = handle_login_form($db_connection);
    }

    $layout_content = include_template(
        'layouts/welcome.php',
        [

            'form_data' => $form_data,
            'errors' => $errors,
        ]
    );

    print($layout_content);

    exit();
}

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Моя лента',
    'user' => $user,
    'page_modifier' => 'feed',
    'basename' => $basename,
    'content' => '',
];

$page_content = include_template('pages/feed.php', []);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
