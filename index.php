<?php

require_once 'constants.php';
require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/db.php';
require_once 'decorators/popular.php';

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

$basename = basename(__FILE__);

$current_sort_type = filter_input(
    INPUT_GET,
    SORT_TYPE_QUERY,
    FILTER_SANITIZE_STRING
);

if (!$current_sort_type) {
    $url = get_sort_url(
        $basename,
        SORT_TYPE_OPTIONS[0]['value']
    );

    header("Location: $url");

    return;
}

$current_content_filter = filter_input(
    INPUT_GET,
    CONTENT_FILTER_QUERY,
    FILTER_SANITIZE_NUMBER_INT
);

$is_sort_order_reversed = isset($_GET[SORT_ORDER_REVERSED]);

$content_types = get_content_types($db_connection);

$layout_data = [
    'title' => 'Популярное',
    'is_auth' => 1,
    'user_name' => 'Евгений',
    'page_modifier' => 'popular',
    'content' => '',
];

if (is_null($content_types)) {
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

$available_sort_types = array_map(
    function ($option) {
        return $option['value'];
    },
    SORT_TYPE_OPTIONS
);

$available_content_filters = array_map(
    function ($content_type) {
        return $content_type['id'];
    },
    $content_types
);

$is_sort_type_valid = array_search(
                          $current_sort_type,
                          $available_sort_types
                      ) !== false;

$is_content_filter_valid = is_null($current_content_filter)
                           || array_search(
                                  $current_content_filter,
                                  $available_content_filters
                              ) !== false;

$is_page_filters_invalid = !$is_sort_type_valid or !$is_content_filter_valid;

$sort_types = SORT_TYPE_OPTIONS;

array_walk(
    $sort_types,
    function (&$sort_type) use ($basename) {
        $value = $sort_type['value'];

        $url = get_sort_url($basename, $value);
        $active = is_query_active(SORT_TYPE_QUERY, $value);


        $sort_type['url'] = $url;
        $sort_type['active'] = $active;
    }
);

$content_filters = $content_types;

$any_content_filter = [
    'name' => 'Все',
    'icon' => 'all',
    'url' => get_content_filter_url($basename),
    'active' => is_query_active(CONTENT_FILTER_QUERY),
];

array_walk(
    $content_filters,
    function (&$filter) use ($basename) {
        $id = $filter['id'];

        $url = get_content_filter_url($basename, $id);
        $active = is_query_active(CONTENT_FILTER_QUERY, $id);

        $filter['url'] = $url;
        $filter['active'] = $active;
    }
);

$popular_filters_content = include_template(
    'partials/popular-filters.php',
    [
        'sort_types' => $sort_types,
        'is_sort_order_reversed' => $is_sort_order_reversed,
        'content_filters' => $content_filters,
        'any_content_filter' => $any_content_filter,
    ]
);

if (!$is_sort_type_valid or !$is_content_filter_valid) {
    http_response_code(BAD_REQUEST_STATUS);

    $page_content = include_template(
        'popular-empty.php',
        [
            'popular_filters_content' => $popular_filters_content,
            'title' => 'Ошибка',
            'content' => 'Параметры фильтрации или сортировки заданы некорректно',
            'link_description' => 'Сброс параметров',
            'link_url' => $basename,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layout.php', $layout_data);

    print($layout_content);

    exit();
}

$post_cards = get_posts(
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

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
