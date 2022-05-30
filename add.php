<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/post-form-validators.php';
require_once 'models/content_type.php';
require_once 'models/post.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$form_data = [
    'author_id' => $user_session['id'],
];
$errors = [];

$layout_data = [
    'title' => 'Добавить публикацию',
    'user' => $user_session,
    'page_modifier' => 'adding-post',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $with_file = isset($_FILES['photo-file'])
                 && $_FILES['photo-file']['error'] !== UPLOAD_ERR_NO_FILE;

    $form_data['content_type_id'] = $_POST['content-type-id'] ?? '';
    $form_data['title'] = $_POST['title'] ?? '';
    $form_data['text_content'] = $_POST['text-content'] ?? '';
    $form_data['string_content'] =
        !$with_file ? $_POST['string-content'] ?? '' : '';
    $form_data['tags'] =
        $_POST['tags'] ? trim(
            preg_replace('/\s+/', TEXT_SEPARATOR, mb_strtolower($_POST['tags']))
        ) : '';
    $form_data['photo_file'] =
        $with_file ? $_FILES['photo-file'] : null;

    $content_type_data = $form_data['content_type_id'] ? get_content_type(
        $db_connection,
        $form_data['content_type_id']
    ) : null;
    $content_type = $content_type_data && $content_type_data['type']
        ? $content_type_data['type'] : null;

    $errors = $content_type
        ? get_post_form_data_errors($form_data, $content_type)
        : [
            [
                'title' => 'Тип контента',
                'description' => 'Некорректный тип'
            ]
        ];

    if (count($errors)) {
        if ($with_file && !$errors['photo_file']) {
            $errors['photo_file'] = [
                'title' => 'Файл фото',
                'description' => 'Загрузите файл еще раз'
            ];
        }
    }

    $is_photo_content_type = $content_type === 'photo';
    $photo_url = '';

    if (!count($errors) && $is_photo_content_type) {
        $photo_url =
            $with_file
                ? save_file($form_data['photo_file'])
                : download_file($form_data['string_content']);

        if (!$photo_url) {
            if ($with_file) {
                $errors['photo_file'] = [
                    'title' => 'Файл фото',
                    'description' => 'Не удалось загрузить файл'
                ];
            } else {
                $errors['string_content'] = [
                    'title' => 'Ссылка из интернета',
                    'description' => 'Не удалось загрузить файл по ссылке'
                ];
            }
        }
    }

    if (!count($errors)) {
        if ($is_photo_content_type) {
            $form_data['string_content'] = $photo_url;
        }

        $created_post_id = create_post($db_connection, $form_data);

        if ($created_post_id) {
            header("Location: post.php?post_id=$created_post_id");

            exit();
        }

        http_response_code(SERVER_ERROR_STATUS);
        render_message_page(
            ['content' => 'Не удалось создать публикацию'],
            $layout_data,
            'user'
        );
        exit();
    }
}

$basename = basename(__FILE__);

$content_types = get_content_types($db_connection);

if (!$content_types) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        $layout_data,
        'user'
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
            $layout_data,
            'user'
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

print($layout_content);
