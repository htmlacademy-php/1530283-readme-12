<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/post-form-validators.php';
require_once 'models/content_type.php';
require_once 'models/post.php';
require_once 'init/db.php';

/**
 * @var mysqli | false | null $db_connection - ресурс соединения с базой данных
 */

check_db_connection($db_connection);

$content_types = get_content_types($db_connection);

if (!$content_types) {
    http_response_code(NOT_FOUND_STATUS);

    $page_content = include_template(
        'partials/error.php',
        ['content' => 'Не удалось загрузить страницу']
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layout.php', $layout_data);

    print($layout_content);

    exit();
}

$current_content_id = filter_input(
    INPUT_GET,
    CONTENT_FILTER_QUERY,
    FILTER_SANITIZE_NUMBER_INT
);
$current_content_type = null;

if ($current_content_id) {
    $current_content_type_data = get_content_type($db_connection, $current_content_id);
    $current_content_type = $current_content_type_data ? $current_content_type_data['type'] : null;
}

$basename = basename(__FILE__);

if (!$current_content_type) {
    $default_content_type_id = $content_types[0]['id'];
    $redirect_url = "$basename?content_type_id=$default_content_type_id";

    header("Location: $redirect_url");

    exit();
}

$is_photo_content_type = $current_content_type === 'photo';

$form_data = [
    'author_id' => 1,
    'content_type_id' => $current_content_id,
];
$errors = [];

$layout_data = [
    'title' => 'Добавить публикацию',
    'is_auth' => 1,
    'user_name' => 'Евгений',
    'page_modifier' => 'adding-post',
    'content' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $with_file = isset($_FILES['photo-file'])
                 && $_FILES['photo-file']['error'] !== UPLOAD_ERR_NO_FILE;

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

    $errors = get_post_form_data_errors($form_data, $current_content_type);

    if (count($errors)) {
        if ($with_file && !$errors['photo_file']) {
            $errors['photo_file'] = [
                'title' => 'Файл фото',
                'description' => 'Загрузите файл еще раз'
            ];
        }
    }

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
                $errors['photo_file'] = [
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

        $page_content = include_template(
            'partials/error.php',
            ['content' => 'Не удалось создать публикацию']
        );

        $layout_data['content'] = $page_content;

        $layout_content = include_template('layout.php', $layout_data);

        print($layout_content);

        exit();
    }
}

$content_tabs = get_content_filters($content_types, $basename);

$content_fields_content = include_template(
    "partials/add-post-form/$current_content_type-content-fields.php",
    [
        'form_data' => $form_data,
        'errors' => $errors,
    ]
);

$content_tabs_content =
    include_template(
        'partials/add-post-form/content-tabs.php',
        [
            'content_tabs' => $content_tabs,
        ]
    );

$page_content = include_template(
    'add-post-form.php',
    [
        'title' => ADD_POST_FORM_TITLE[$current_content_type],
        'form_data' => $form_data,
        'errors' => $errors,
        'invalid' => boolval(count($errors)),
        'content_tabs' => $content_tabs_content,
        'content_fields' => $content_fields_content,
        'with_photo_file' => $is_photo_content_type,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
