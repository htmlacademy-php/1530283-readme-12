<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/db.php';

if ( ! isset($db_connection) or ! $db_connection) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    // todo: page error
    return;
}

$basename = basename(__FILE__);

if ( ! isset($_GET[SORT_TYPE_QUERY])) {
    $url = get_sort_url(
        $basename,
        SORT_TYPE_OPTIONS[0]['value']
    );

    header("Location: $url");

    return;
}

$current_content_type_id = filter_input(
    INPUT_GET,
    CONTENT_TYPE_QUERY,
    FILTER_SANITIZE_NUMBER_INT
);
$current_sort_type       = filter_input(
    INPUT_GET,
    SORT_TYPE_QUERY,
    FILTER_SANITIZE_STRING
);
$is_sort_order_reversed  = isset($_GET[SORT_ORDER_REVERSED]);

$content_types = get_content_types($db_connection);

if (is_null($content_types)) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    // todo: page error
    return;
}

$available_sort_types = array_map(
    function ($option) {
        return $option['value'];
    },
    SORT_TYPE_OPTIONS
);

$available_filters = array_map(
    function ($content_type) {
        return $content_type['id'];
    },
    $content_types
);

$is_sort_types_valid = array_search($current_sort_type, $available_sort_types)
                       !== false;
$is_filter_valid     = is_null($current_content_type_id)
                       || array_search(
                              $current_content_type_id,
                              $available_filters
                          ) !== false;

if ( ! $is_sort_types_valid or ! $is_filter_valid) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Ошибка фильтров']
    );
    ob_end_clean();
    print($error_layout);

    // todo: filters error
    return;
}

$post_cards = get_posts(
    $db_connection,
    [
        'sort_type'         => $current_sort_type,
        'is_order_reversed' => $is_sort_order_reversed,
        'content_type_id'   => $current_content_type_id
    ]
);

if (is_null($post_cards)) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    // todo: post cards data error
    return;
}

$sort_types = SORT_TYPE_OPTIONS;

array_walk(
    $sort_types,
    function (&$sort_type) use ($basename) {
        $value = $sort_type['value'];

        $url    = get_sort_url($basename, $value);
        $active = is_query_active(SORT_TYPE_QUERY, $value);


        $sort_type['url']    = $url;
        $sort_type['active'] = $active;
    }
);

$filters = $content_types;

$empty_filter = [
    'name'   => 'Все',
    'icon'   => 'all',
    'url'    => get_filter_url($basename),
    'active' => is_query_active(CONTENT_TYPE_QUERY),
];

array_walk(
    $filters,
    function (&$filter) use ($basename) {
        $id = $filter['id'];

        $url    = get_filter_url($basename, $id);
        $active = is_query_active(CONTENT_TYPE_QUERY, $id);

        $filter['url']    = $url;
        $filter['active'] = $active;
    }
);

$popular_filters_content = include_template(
    'partials/popular-filters.php',
    [
        'sort_types'             => $sort_types,
        'is_sort_order_reversed' => $is_sort_order_reversed,
        'filters'                => $filters,
        'empty_filter'           => $empty_filter,
    ]
);

$is_empty = ! count($post_cards);

$page_content = $is_empty
    ? include_template(
        'popular_empty.php',
        [
            'popular_filters_content' => $popular_filters_content,
        ]
    )
    :
    include_template(
        'popular.php',
        [
            'popular_filters_content' => $popular_filters_content,
            'post_cards'              => $post_cards,
        ]
    );

$layout_content = include_template(
    'layout.php',
    [
        'title'         => 'Популярное',
        'is_auth'       => 1,
        'user_name'     => 'Евгений',
        'page_modifier' => 'popular',
        'content'       => $page_content,
    ]
);

print($layout_content);
