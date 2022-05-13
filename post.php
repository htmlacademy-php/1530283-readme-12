<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'models/user.php';
require_once 'models/hashtag.php';
require_once 'init/db.php';
require_once 'decorators/post_details.php';

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

$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

$post = null;
$comments = null;
$author = null;
$hashtags = null;

if ($post_id) {
    $post = get_post($db_connection, $post_id);
    $comments = get_comments($db_connection, $post_id);
    $hashtags = get_hashtags($db_connection, $post_id);
}

if (is_array($post) and isset($post['author_id'])) {
    $author = get_user($db_connection, $post['author_id']);
}

$layout_data = [
    'title' => 'Популярное',
    'is_auth' => 1,
    'user_name' => 'Евгений',
    'page_modifier' => 'publication',
    'content' => '',
];

$is_page_error = is_null($post) || is_null($comments) || is_null($author)
                 || is_null($hashtags);

if ($is_page_error) {
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

$author_content = include_template(
    'partials/post-details/author.php',
    ['author' => $author]
);

$page_content = include_template(
    'post-details.php',
    [
        'post' => $post,
        'post_content' => decorate_post_details_content($post),
        'hashtags' => $hashtags,
        'author_content' => $author_content,
        'comments' => $comments,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
