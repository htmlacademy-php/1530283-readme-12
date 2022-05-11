<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'init/db.php';

if ( ! isset($db_connection) or ! $db_connection) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    return;
}

$post_id  = $_GET['post_id'] ? intval($_GET['post_id']) : null;
$post     = null;
$comments = null;

if ($post_id) {
    $post     = get_post($db_connection, $post_id);
    $comments = get_comments($db_connection, $post_id);
}

if (is_null($post) or is_null($comments)) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    return;
}

list(
    'text_content' => $text_content,
    'string_content' => $string_content,
    'content_type' => $content_type,
    )
    = $post;

$post_content_decorators = [
    'quote' => function () use ($text_content, $string_content) {
        return include_template(
            'partials/post-details/quote-content.php',
            [
                'text_content'   => $text_content,
                'string_content' => $string_content,
            ]
        );
    },
    'text'  => function () use ($text_content, $string_content) {
        return include_template(
            'partials/post-details/text-content.php',
            [
                'text_content' => $text_content,
            ]
        );
    },
    'photo' => function () use ($string_content) {
        return include_template(
            'partials/post-details/photo-content.php',
            [
                'string_content' => $string_content,
            ]
        );
    },
    'link'  => function () use ($string_content) {
        return include_template(
            'partials/post-details/link-content.php',
            [
                'string_content' => $string_content,
            ]
        );
    },
];

$page_content = include_template(
    'post.php',
    [
        'post'     => $post,
        'content'  => $post_content_decorators[$content_type](),
        'comments' => $comments
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'title'         => $post['title'],
        'is_auth'       => rand(0, 1),
        'user_name'     => 'Евгений',
        'page_modifier' => 'publication',
        'content'       => $page_content,
    ]
);

print($layout_content);
