<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/decorators.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

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
    'content' => '',
];

if (is_null($content_types)) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        $layout_data,
        'user'
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
$any_content_filter = [
    'name' => 'Все',
    'type' => 'all',
    'url' => get_content_filter_url($basename),
    'active' => is_null($current_content_filter),
];

$popular_filters_content = include_template(
    'pages/popular/filters.php',
    [
        'sort_types' => $sort_types,
        'is_sort_order_reversed' => $is_sort_order_reversed,
        'content_filters' => $content_filters,
        'any_content_filter' => $any_content_filter,
    ]
);

// todo: тоже отптимизировать?
if (!$is_sort_type_valid or !$is_content_filter_valid) {
    http_response_code(BAD_REQUEST_STATUS);

    $filter_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Параметры фильтрации или сортировки заданы некорректно',
            'link_description' => 'Сброс параметров',
            'link_url' => $basename,
        ]
    );

    $page_content = include_template(
        'pages/popular/page.php',
        [
            'filters_content' => $popular_filters_content,
            'main_content' => $filter_error_message,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);

    exit();
}

$post_cards = get_popular_posts(
    $db_connection,
    [
        'sort_type' => $current_sort_type,
        'is_order_reversed' => $is_sort_order_reversed,
        'content_type_id' => $current_content_filter
    ]
);

if (is_null($post_cards)) {
    http_response_code(NOT_FOUND_STATUS);
}

$page_content = decorate_popular_page($popular_filters_content, $post_cards);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
