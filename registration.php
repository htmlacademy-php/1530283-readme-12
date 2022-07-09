<?php

require_once 'init/common.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/form-handlers/registration.php';
require_once 'models/user.php';
require_once 'init/guest-session.php';
require_once 'init/db-connection.php';

/**
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$form_data = [];
$errors = [];

$layout_data = [
    'title' => 'Регистрация',
    'page_modifier' => 'registration',
    'basename' => $basename,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    list(
        'form_data' => $form_data,
        'errors' => $errors
        ) = handle_registration_form($db_connection);

    if (!count($errors)) {
        $form_data['password_hash'] =
            password_hash($form_data['password'], PASSWORD_BCRYPT);

        $created_user_id = create_user($db_connection, $form_data);

        if ($created_user_id) {
            header('Location: login.php');
            exit();
        }

        http_response_code(SERVER_ERROR_STATUS);
        render_message_page(
            ['content' => 'Не удалось завершить регистрацию'],
            'user',
            $layout_data,
        );
        exit();
    }
}

$invalid_block_content = count($errors) ? include_template(
    'common/form-invalid-block.php',
    ['errors' => $errors]
) : '';

$page_content = include_template(
    'pages/registration-form.php',
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
