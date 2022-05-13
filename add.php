<?php

require_once 'constants.php';
require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/content_type.php';
require_once 'models/post.php';
require_once 'init/db.php';

if (!isset($db_connection) or !$db_connection) {
    http_response_code(SERVER_ERROR_STATUS);

    $error_layout = include_template(
        'empty-layout.php',
        ['content' => 'Произошла внутренняя ошибка сервера']
    );

    ob_end_clean();

    print($error_layout);

    return;
}

$content_types = get_content_types($db_connection);

$current_content_filter = filter_input(
    INPUT_GET,
    CONTENT_FILTER_QUERY,
    FILTER_SANITIZE_NUMBER_INT
);

$is_content_filter_valid = $content_types
                           && validate_content_filter(
                               $current_content_filter,
                               $content_types
                           );

if (is_null($content_types) || !$is_content_filter_valid) {
    http_response_code(NOT_FOUND_STATUS);

    $page_content = include_template(
        'partials/error.php',
        ['content' => 'Не удалось загрузить страницу']
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layout.php', $layout_data);

    print($layout_content);

    return;
}

// todo: create util function ?
$content_type = $content_types[array_search(
    $current_content_filter,
    array_map(
        function ($content_type) {
            return $content_type['id'];
        },
        $content_types
    )
)]['icon'];

$basename = basename(__FILE__);
$form_data = [];
$errors = [];
$invalid = false;
$db_error = false;

$layout_data = [
    'title' => 'Добавить публикацию',
    'is_auth' => 1,
    'user_name' => 'Евгений',
    'page_modifier' => 'adding-post',
    'content' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // todo: create parser ?
    $form_data['title'] = $_POST['title'] ?? '';
    $form_data['text_content'] = $_POST['text-content'] ?? '';
    $form_data['string_content'] = $_POST['string-content'] ?? '';
    $form_data['tags'] =
        $_POST['tags'] ? trim(
            preg_replace('/\s+/', TEXT_SEPARATOR, strtolower($_POST['tags']))
        ) : '';

    $errors = get_post_form_data_errors($form_data, $content_type);
    $invalid = boolval(count($errors));

    if (!$invalid) {
        $form_data['author_id'] = 1;
        $form_data['content_type_id'] = $current_content_filter;

        $created_post_id = create_post($db_connection, $form_data);

        if ($created_post_id) {
            header("Location: post.php?post_id=$created_post_id");
            return;
        } else {
            http_response_code(SERVER_ERROR_STATUS);

            $page_content = include_template(
                'partials/error.php',
                ['content' => 'Не удалось создать публикацию']
            );

            $layout_data['content'] = $page_content;

            $layout_content = include_template('layout.php', $layout_data);

            print($layout_content);

            return;
        }
    }
}

$content_filters = get_content_filters($content_types, $basename);

$content_fields_content = include_template(
    "partials/add-post-form/$content_type-content-fields.php",
    [
        'form_data' => $form_data,
        'errors' => $errors,
    ]
);

$content_filters_content =
    include_template(
        'partials/add-post-form/content-filters.php',
        [
            'content_filters' => $content_filters,
        ]
    );

$with_photo_file = $content_type === 'photo';

$page_content = include_template(
    'add-post-form.php',
    [
        'title' => ADD_POST_FORM_TITLE[$content_type],
        'form_data' => $form_data,
        'errors' => $errors,
        'invalid' => $invalid,
        'content_filters' => $content_filters_content,
        'content_fields' => $content_fields_content,
        'with_photo_file' => $with_photo_file,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
