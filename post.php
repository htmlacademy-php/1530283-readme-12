<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'models/user.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

$post = null;
$comments = null;
$author = null;

if ($post_id) {
    $post = get_post($db_connection, $post_id);
    $comments = get_comments($db_connection, $post_id);
    $hashtags = get_hashtags($db_connection, $post_id);
}

if (is_array($post) and isset($post['author_id'])) {
    $author = get_user($db_connection, $post['author_id']);
}

$layout_data = [
    'title' => 'Просмотр поста',
    'user' => $user_session,
    'page_modifier' => 'publication',
];

$is_page_error = is_null($post) || is_null($comments) || is_null($author);

if ($is_page_error) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data,
    );
    exit();
}

$author_content = include_template(
    'pages/post-details/author.php',
    ['author' => $author]
);

$content_type = $post['content_type'];

$post_details_content = include_template(
    "pages/post-details/content/$content_type.php",
    [
        'text_content' => $post['text_content'] ?? '',
        'string_content' => $post['string_content'] ?? '',
    ]
);

$page_content = include_template(
    'pages/post-details/page.php',
    [
        'post' => $post,
        'post_content' => $post_details_content,
        'author_content' => $author_content,
        'comments' => $comments,
    ]
);

$layout_data['title'] = $post['title'];
$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
