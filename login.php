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
    $form_data['email'] = $_POST['email'] ?? '';
    $form_data['password'] = $_POST['password'] ?? '';

    $errors = get_login_form_data_errors($form_data);

    $user = !count($errors) ? get_user_by_email(
        $db_connection,
        $form_data['email']
    ) : null;

    $is_password_correct = $user
                           && password_verify(
                               $form_data['password'],
                               $user['password_hash']
                           );

    if (!$user || !$is_password_correct) {
        $errors['verification'] = [
            'title' => 'Верификация',
            'description' => 'Неверное значение электронной почты или пароля',
        ];
    }

    if (!count($errors)) {
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    }
}

$page_content = include_template(
    'login-form.php',
    [
        'form_data' => $form_data,
        'errors' => $errors,
        'invalid' => !!count($errors),
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/guest.php', $layout_data);

print($layout_content);
