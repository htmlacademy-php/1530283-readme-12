<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'models/user.php';
require_once 'models/hashtag.php';
require_once 'init/db.php';

/**
 * @var mysqli | false | null $db_connection - ресурс соединения с базой данных
 */

check_db_connection($db_connection);

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
    'title' => 'Просмотр поста',
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

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);

    return;
}

$author_content = include_template(
    'partials/post-details/author.php',
    ['author' => $author]
);

$content_type = $post['content_type'];

$post_details_content = include_template(
    "partials/post-details/$content_type-content.php",
    [
        'text_content' => $post['text_content'] ?? '',
        'string_content' => $post['string_content'] ?? '',
    ]
);

$page_content = include_template(
    'post-details.php',
    [
        'post' => $post,
        'post_content' => $post_details_content,
        'hashtags' => $hashtags,
        'author_content' => $author_content,
        'comments' => $comments,
    ]
);

$layout_data['title'] = $post['title'];
$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
