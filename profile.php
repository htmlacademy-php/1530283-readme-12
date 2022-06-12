<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/renderers/profile.php';
require_once 'models/like.php';
require_once 'models/user.php';
require_once 'models/post.php';
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
            get_observable_users($db_connection, $user_id, $user_session['id']);

        if (is_null($subscriptions)) {
            http_response_code(SERVER_ERROR_STATUS);
        }

        $main_content = get_profile_subscriptions_tab_content($subscriptions);
        break;

    default:
        $user_posts =
            get_posts_by_author($db_connection, $user_session['id'], $user_id);

        if (is_null($user_posts)) {
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
