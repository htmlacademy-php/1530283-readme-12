<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/form-handlers/login.php';
require_once 'models/user.php';
require_once 'init/guest-session.php';
require_once 'init/db-connection.php';

/**
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Авторизация',
    'page_modifier' => 'login',
    'basename' => $basename
];

$form_data = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    list(
        'form_data' => $form_data,
        'errors' => $errors,
        'user' => $user
        ) = handle_login_form($db_connection);

    if (!count($errors)) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    }
}

$invalid_block_content = count($errors) ? include_template(
    'common/form-invalid-block.php',
    ['errors' => $errors]
) : '';

$page_content = include_template(
    'pages/login-form.php',
    [
        'form_data' => $form_data,
        'errors' => $errors,
        'invalid_block_content' => $invalid_block_content,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/guest.php', $layout_data);

if (count($errors)) {
    http_response_code(BAD_REQUEST_STATUS);
}

print($layout_content);
