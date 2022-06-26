<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'init/mail.php';
require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/form-handlers/add-post.php';
require_once 'utils/renderers/common.php';
require_once 'utils/notifiers.php';
require_once 'models/content_type.php';
require_once 'models/post.php';
require_once 'models/subscription.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 * @var PHPMailer $mail - экзмепляр PHPMailer
 */

$form_data = [];
$errors = [];

$layout_data = [
    'title' => 'Добавить публикацию',
    'user' => $user_session,
    'page_modifier' => 'adding-post',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    list(
        'form_data' => $form_data,
        'errors' => $errors
        ) = handle_add_post_form($db_connection);

    if (!count($errors)) {
        $form_data['author_id'] = $user_session['id'];
        $created_post_id = create_post($db_connection, $form_data);

        if (!$created_post_id) {
            http_response_code(SERVER_ERROR_STATUS);
            render_message_page(
                ['content' => 'Не удалось создать публикацию'],
                'user',
                $layout_data,
            );
            exit();
        }

        $subscribers = get_subscribers($db_connection, $user_session['id']);

        if (is_array($subscribers)) {
            $created_post_data = [
                'id' => $created_post_id,
                'title' => $form_data['title'],
                'author' => $user_session,
            ];

            foreach ($subscribers as $subscriber) {
                var_dump(
                    notify_about_new_post(
                        $mail,
                        $subscriber,
                        $created_post_data
                    )
                );
                print '<br/>';
                var_dump(
                    notify_about_new_post(
                        $mail,
                        $subscriber,
                        $created_post_data
                    )
                );
                print '<br/>';
                var_dump(
                    notify_about_new_post(
                        $mail,
                        $subscriber,
                        $created_post_data
                    )
                );
                print '<br/>';
            }

            exit();
        }

        header("Location: post.php?post-id=$created_post_id");
        exit();
    }
}

$basename = basename(__FILE__);

$content_types = get_content_types($db_connection);

if (!$content_types) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data,
    );
    exit();
}

$current_content_id = filter_input(
    INPUT_GET,
    CONTENT_FILTER_QUERY,
    FILTER_SANITIZE_NUMBER_INT
);

$current_content_type = null;

if ($current_content_id) {
    $is_current_content_valid =
        validate_content_filter($current_content_id, $content_types);

    if (!$is_current_content_valid) {
        http_response_code(NOT_FOUND_STATUS);
        render_message_page(
            [
                'content' => 'Тип контента задан неверно',
                'link_description' => 'Перейти на страницу формы с типом по умолчанию',
                'link_url' => $basename,
            ],
            'user',
            $layout_data,
        );
        exit();
    }
} else {
    $default_content_type = $content_types[0];
    $current_content_id = $default_content_type['id'];
    $current_content_type = $default_content_type['type'];
}

if (!$current_content_type) {
    $content_index =
        array_search($current_content_id, array_column($content_types, 'id'));
    $current_content_type = $content_types[$content_index]['type'];
}

$form_data['content_type_id'] = $current_content_id;

$is_photo_content_type = $current_content_type === 'photo';

$content_tabs =
    get_content_filters($content_types, $basename, $current_content_id);

$content_fields_content = include_template(
    "pages/add-post-form/content-fields/$current_content_type.php",
    [
        'form_data' => $form_data,
        'errors' => $errors,
    ]
);

$content_tabs_content =
    include_template(
        'pages/add-post-form/content-tabs.php',
        [
            'content_tabs' => $content_tabs,
        ]
    );

$invalid_block_content = count($errors) ? include_template(
    'common/form-invalid-block.php',
    [
        'errors' => $errors
    ]
) : '';

$page_content = include_template(
    'pages/add-post-form/page.php',
    [
        'title' => ADD_POST_FORM_TITLE[$current_content_type],
        'form_data' => $form_data,
        'errors' => $errors,
        'invalid_block_content' => $invalid_block_content,
        'content_tabs' => $content_tabs_content,
        'content_fields' => $content_fields_content,
        'with_photo_file' => $is_photo_content_type
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

if (count($errors)) {
    http_response_code(BAD_REQUEST_STATUS);
}

print($layout_content);
