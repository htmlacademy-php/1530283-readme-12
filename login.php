<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/login-form-validators.php';
require_once 'models/user.php';
require_once 'init/db.php';

/**
 * @var mysqli | false | null $db_connection - ресурс соединения с базой данных
 */

check_guest();

check_db_connection($db_connection);

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
    ) = handle_login_form($db_connection);
}

$invalid_block_content = count($errors) ? include_template(
    'common/form-invalid-block.php',
    [
        'errors' => $errors
    ]
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

print($layout_content);
