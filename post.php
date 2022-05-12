<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'init/db.php';
require_once 'decorators/post_details.php';

if ( ! isset($db_connection) or ! $db_connection) {
    http_response_code(SERVER_ERROR_STATUS);

    $error_layout = include_template(
        'empty_layout.php',
        ['content' => 'Произошла внутренняя ошибка сервера']
    );

    ob_end_clean();

    print($error_layout);

    return;
}

$post_id  = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
$post     = null;
$comments = null;

if ($post_id) {
    $post     = get_post($db_connection, $post_id);
    $comments = get_comments($db_connection, $post_id);
}

$layout_data = [
    'title'         => 'Популярное',
    'is_auth'       => 1,
    'user_name'     => 'Евгений',
    'page_modifier' => 'publication',
    'content'       => '',
];

if (is_null($post) or is_null($comments)) {
    http_response_code(NOT_FOUND_STATUS);

    $page_content = include_template(
        'partials/error.php',
        [
            'content' => 'Не удалось загрузить страницу'
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layout.php', $layout_data);

    print($layout_content);

    return;
}

$page_content = include_template(
    'post.php',
    [
        'post'     => $post,
        'content'  => decorate_post_details_content($post),
        'comments' => $comments
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
