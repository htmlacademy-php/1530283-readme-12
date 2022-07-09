<?php

require_once 'init/common.php';
require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/form-handlers/add-comment.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'models/user.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$post_id = filter_input(INPUT_GET, POST_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);
$is_comments_expanded =
    filter_input(INPUT_GET, COMMENTS_EXPANDED, FILTER_VALIDATE_BOOLEAN) ??
    false;

$basename = basename(__FILE__);

$post = null;
$comments = null;
$author = null;

$form_data = [];
$errors = [];

$layout_data = [
    'title' => 'Просмотр поста',
    'user' => $user_session,
    'page_modifier' => 'publication',
    'basename' => $basename
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    list(
        'form_data' => $form_data,
        'errors' => $errors
        ) = handle_add_comment_form($db_connection);

    if (!count($errors)) {
        $form_data['author_id'] = $user_session['id'];
        $created_comment_id = create_comment($db_connection, $form_data);

        if ($created_comment_id) {
            $query_param = $_GET;
            $query_param[USER_ID_QUERY] = $form_data['post_author_id'];
            $query_param[COMMENTS_POST_ID_QUERY] = $post_id;
            $query_string = http_build_query($query_param);

            header("Location: profile.php?$query_string#comments");
            exit();
        }

        http_response_code(SERVER_ERROR_STATUS);
        render_message_page(
            ['content' => 'Не удалось создать комментарий'],
            'user',
            $layout_data,
        );
        exit();
    }
}

$comments_limit = $is_comments_expanded ? null : DEFAULT_COMMENTS_LIMIT;

if ($post_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        increase_views_count($db_connection, $post_id);
    }
    $post = get_post($db_connection, $user_session['id'], $post_id);
    $comments = get_comments($db_connection, $post_id, $comments_limit);
    $hashtags = get_hashtags($db_connection, $post_id);
}

if (is_array($post) and isset($post['author_id'])) {
    $author = get_user($db_connection, $post['author_id'], $user_session['id']);
}

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

$is_own_post = $author['id'] === $user_session['id'];

$author_content = include_template(
    'pages/post-details/author.php',
    [
        'author' => $author,
        'is_own_post' => $is_own_post,
    ]
);

$content_type = $post['content_type'];

$post_details_content = include_template(
    "pages/post-details/content/$content_type.php",
    [
        'title' => $post['title'],
        'text_content' => $post['text_content'] ?? '',
        'string_content' => $post['string_content'] ?? '',
    ]
);

$form_data['post_id'] = $post['id'];
$form_data['post_author_id'] = $post['author_id'];

$comments_count = $post['comments_count'];
$is_comments_expansion_required = count($comments) < $comments_count;
$expand_comments_url =
    !$is_comments_expanded && $is_comments_expansion_required
        ? get_expand_comments_url($basename) : null;


$comments_list_content = include_template(
    'common/comments/list.php',
    [
        'comments' => $comments,
        'comments_count' => $comments_count,
        'expand_comments_url' => $expand_comments_url,
    ]
);

$comments_form_content = include_template(
    'common/comments/form.php',
    [
        'user' => $user_session,
        'form_data' => $form_data,
        'errors' => $errors,
    ]
);

$page_content = include_template(
    'pages/post-details/page.php',
    [
        'post' => $post,
        'post_content' => $post_details_content,
        'author_content' => $author_content,
        'comments_list_content' => $comments_list_content,
        'comments_form_content' => $comments_form_content,
    ]
);

$layout_data['title'] = $post['title'];
$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

if (count($errors)) {
    http_response_code(BAD_REQUEST_STATUS);
}

print($layout_content);
