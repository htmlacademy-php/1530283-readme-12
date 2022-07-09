<?php

require_once 'init/common.php';
require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/renderers/popular.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$limit = filter_input(INPUT_GET, LIMIT_QUERY, FILTER_SANITIZE_NUMBER_INT) ??
         DEFAULT_POSTS_LIMIT;

$page = filter_input(INPUT_GET, PAGE_QUERY, FILTER_SANITIZE_NUMBER_INT) ??
        INITIAL_POSTS_PAGE;

$current_sort_type = filter_input(
                         INPUT_GET,
                         SORT_TYPE_QUERY,
                         FILTER_SANITIZE_STRING
                     ) ?? SORT_TYPE_OPTIONS[0]['value'];

$current_content_filter = filter_input(
                              INPUT_GET,
                              CONTENT_FILTER_QUERY,
                              FILTER_SANITIZE_NUMBER_INT
                          ) ?? null;

$is_sort_order_reversed =
    filter_input(INPUT_GET, SORT_ORDER_REVERSED, FILTER_VALIDATE_BOOLEAN) ??
    false;

$content_types = get_content_types($db_connection);

$layout_data = [
    'title' => 'Популярное',
    'user' => $user_session,
    'page_modifier' => 'popular',
    'basename' => $basename,
];

if (is_null($content_types)) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data
    );
    exit();
}

$is_sort_type_valid = validate_sort_type($current_sort_type);
$is_content_filter_valid = is_null($current_content_filter)
                           || validate_content_filter(
                               $current_content_filter,
                               $content_types
                           );

$sort_types =
    get_sort_types($basename, $current_sort_type, $is_sort_order_reversed);
$content_filters =
    get_content_filters($content_types, $basename, $current_content_filter);
$any_content_filter =
    get_any_content_filter($basename, is_null($current_content_filter));

$popular_filters_content = include_template(
    'pages/popular/filters.php',
    [
        'sort_types' => $sort_types,
        'is_sort_order_reversed' => $is_sort_order_reversed,
        'content_filters' => $content_filters,
        'any_content_filter' => $any_content_filter,
    ]
);

if (!$is_sort_type_valid or !$is_content_filter_valid) {
    http_response_code(BAD_REQUEST_STATUS);
    render_popular_filter_error($popular_filters_content, $layout_data);
    exit();
}

$post_cards_config = [
    'sort_type' => $current_sort_type,
    'is_order_reversed' => $is_sort_order_reversed,
    'content_type_id' => $current_content_filter,
    'limit' => $limit,
    'offset' => ($page - 1) * $limit,
];

$post_cards =
    get_popular_posts($db_connection, $user_session['id'], $post_cards_config);
$next_cards_config = $post_cards_config;

$next_cards_config['offset'] = $page * $limit;
$next_post_cards =
    get_popular_posts($db_connection, $user_session['id'], $next_cards_config);
$is_next_page = !is_null($next_post_cards) && count($next_post_cards);
$pagination = get_pagination($basename, $page, $is_next_page);

if (is_null($post_cards)) {
    http_response_code(SERVER_ERROR_STATUS);
}

render_popular_page(
    $popular_filters_content,
    [
        'post_cards' => $post_cards,
        'pagination' => $pagination,
    ],
    $layout_data
);
