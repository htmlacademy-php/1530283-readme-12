<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/registration-form-validators.php';
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
    $with_file = isset($_FILES['photo-file'])
                 && $_FILES['photo-file']['error'] !== UPLOAD_ERR_NO_FILE;

    $form_data['email'] = $_POST['email'] ?? '';
    $form_data['login'] = $_POST['login'] ?? '';
    $form_data['password'] = $_POST['password'] ?? '';
    $form_data['password_repeat'] = $_POST['password-repeat'] ?? '';
    $form_data['avatar_file'] =
        $with_file ? $_FILES['photo-file'] : null;

    $errors = get_registration_form_data_errors($form_data);

    if (count($errors)) {
        if ($with_file && !$errors['avatar_file']) {
            $errors['avatar_file'] = [
                'title' => 'Файл фото',
                'description' => 'Загрузите файл еще раз'
            ];
        }
    }

    $photo_url =
        !count($errors) && $with_file ? save_file($form_data['avatar_file'])
            : '';

    if ($with_file && !$photo_url) {
        $errors['avatar_file'] = [
            'title' => 'Файл фото',
            'description' => 'Не удалось загрузить файл'
        ];
    }

    $is_email_busy = get_user_by_email($db_connection, $form_data['email']);

    if ($is_email_busy) {
        $errors['email'] = [
            'title' => 'Электронная почта',
            'description' => 'Пользователь с такой электронной почто уже зарегистрирован'
        ];
    }

    if (!count($errors)) {
        if ($with_file) {
            $form_data['avatar_url'] = $photo_url;
        }

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
    [
        'errors' => $errors
    ]
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

print($layout_content);
