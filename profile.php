<?php

require_once 'init/db-connection.php';
require_once 'init/user-session.php';
require_once 'utils/helpers.php';
require_once 'utils/renderers/profile.php';
require_once 'utils/form-handlers/add-comment.php';
require_once 'models/like.php';
require_once 'models/user.php';
require_once 'models/post.php';
require_once 'models/comment.php';
require_once 'models/subscription.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Профиль ползователя',
    'user' => $user_session,
    'page_modifier' => 'profile',
    'basename' => $basename,
];

$user_id = filter_input(INPUT_GET, USER_ID_QUERY, FILTER_SANITIZE_NUMBER_INT) ??
           $user_session['id'];
$current_tab = filter_input(INPUT_GET, TAB_QUERY, FILTER_SANITIZE_STRING) ??
               PROFILE_TABS[0]['value'];
$is_own_profile = intval($user_id) === $user_session['id'];

$user = get_user($db_connection, $user_id, $user_session['id']);

if (!$user) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data
    );
    exit();
}

$user_content = include_template(
    'pages/profile/user.php',
    [
        'user' => $user,
        'is_own_profile' => $is_own_profile
    ]
);

$tabs = get_profile_tabs($basename, $current_tab);
$tabs_content = include_template(
    'pages/profile/tabs.php',
    ['tabs' => $tabs]
);

$is_tab_valid = validate_profile_tab($current_tab);

if (!$is_tab_valid) {
    http_response_code(BAD_REQUEST_STATUS);
    render_profile_tab_error($user_content, $tabs_content, $layout_data);
    exit();
}

$main_content = '';

switch ($current_tab) {
    case PROFILE_LIKES_TAB['value']:
        $likes = get_likes($db_connection, $user_id);

        if (is_null($likes)) {
            http_response_code(SERVER_ERROR_STATUS);
        }

        $main_content = get_profile_likes_tab_content($likes, $is_own_profile);
        break;

    case PROFILE_SUBSCRIPTIONS_TAB['value']:
        $subscriptions =
            get_subscriptions_by_subscriber(
                $db_connection,
                $user_session['id'],
                $user_id
            );

        if (is_null($subscriptions)) {
            http_response_code(SERVER_ERROR_STATUS);
        }

        $main_content = get_profile_subscriptions_tab_content($subscriptions);
        break;

    default:
        $comments_post_id = filter_input(
            INPUT_GET,
            COMMENTS_POST_ID_QUERY,
            FILTER_SANITIZE_NUMBER_INT
        );

        $form_data = [];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            list(
                'form_data' => $form_data,
                'errors' => $errors
                ) = handle_add_comment_form($db_connection);

            if (!count($errors)) {
                $form_data['author_id'] = $user_session['id'];
                $created_comment_id =
                    create_comment($db_connection, $form_data);

                if ($created_comment_id) {
                    $query_param = $_GET;
                    $query_param[USER_ID_QUERY] = $user_id;
                    $query_param[COMMENTS_POST_ID_QUERY] = $comments_post_id;
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

        $is_comments_expanded =
            filter_input(
                INPUT_GET,
                COMMENTS_EXPANDED,
                FILTER_VALIDATE_BOOLEAN
            )
            ??
            false;

        $comments = [];
        $comments_form_data = [];
        $comments_list_data = [];

        if ($comments_post_id) {
            $comments_limit =
                $is_comments_expanded ? null : DEFAULT_COMMENTS_LIMIT;
            $comments = get_comments(
                $db_connection,
                $comments_post_id,
                $comments_limit
            );

            $form_data['post_id'] = $comments_post_id;
            $form_data['post_author_id'] = $user_id;

            $comments_form_data = [
                'user' => $user_session,
                'form_data' => $form_data,
                'errors' => $errors
            ];

            $comments_list_data = [
                'comments' => $comments,
            ];
        }

        $user_posts =
            get_posts_by_author($db_connection, $user_session['id'], $user_id);

        if (is_array($user_posts)) {
            $comments_data = [
                'basename' => $basename,
                'post_id' => $comments_post_id,
                'form_data' => $comments_form_data,
                'list_data' => $comments_list_data,
                'is_expanded' => $is_comments_expanded
            ];
            $user_posts = add_comments_contents($user_posts, $comments_data);
        }

        if (is_null($user_posts) || ($comments_post_id && is_null($comments))) {
            http_response_code(SERVER_ERROR_STATUS);
        }

        $main_content = get_profile_posts_tab_content($user_posts);
}

$page_content = include_template(
    'pages/profile/page.php',
    [
        'user_content' => $user_content,
        'tabs_content' => $tabs_content,
        'main_content' => $main_content,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
