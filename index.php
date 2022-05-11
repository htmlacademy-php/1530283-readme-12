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

    return;
}

$current_content_type_id = $_GET[CONTENT_TYPE_QUERY] ? intval(
    $_GET[CONTENT_TYPE_QUERY]
) : null;

$content_types = get_content_types($db_connection);
$post_cards    = get_posts(
    $db_connection,
    [
        'sort'            => 'views_count',
        'content_type_id' => $current_content_type_id
    ]
);

if (is_null($content_types) or is_null($post_cards)) {
    $error_layout = include_template(
        'error.php',
        ['content' => 'Данные недоступны']
    );
    ob_end_clean();
    print($error_layout);

    return;
}

$basename = basename(__FILE__);

$filters = $content_types;

$empty_filter = [
    'name'   => 'Все',
    'icon'   => 'all',
    'url'    => get_filter_url($basename, CONTENT_TYPE_QUERY),
    'active' => is_filter_active(CONTENT_TYPE_QUERY),
];

array_walk(
    $filters,
    function (&$filter) use ($basename) {
        $id = $filter['id'];

        $url    = get_filter_url($basename, CONTENT_TYPE_QUERY, $id);
        $active = is_filter_active(CONTENT_TYPE_QUERY, $id);

        $filter['url']    = $url;
        $filter['active'] = $active;
    }
);

$popular_filters_content = include_template(
    'partials/popular-filters.php',
    [
        'filters'      => $filters,
        'empty_filter' => $empty_filter,
    ]
);

$page_content = include_template(
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
