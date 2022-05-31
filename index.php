<?php

require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/login-form-validators.php';
require_once 'models/user.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/db-connection.php';

/**
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

session_start();
$user = $_SESSION['user'] ?? null;

if (!$user) {
    $form_data = [];
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        list(
            'form_data' => $form_data,
            'errors' => $errors,
            ) = handle_login_form($db_connection);
    }

    $layout_content = include_template(
        'layouts/welcome.php',
        [
            'form_data' => $form_data,
            'errors' => $errors,
        ]
    );

    print($layout_content);

    exit();
}

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Моя лента',
    'user' => $user,
    'page_modifier' => 'feed',
    'basename' => $basename,
    'content' => '',
];

$current_content_filter = filter_input(
                              INPUT_GET,
                              CONTENT_FILTER_QUERY,
                              FILTER_SANITIZE_NUMBER_INT
                          ) ?? null;

$content_types = get_content_types($db_connection);

if (is_null($content_types)) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        ['content' => 'Не удалось загрузить страницу'],
        'user',
        $layout_data,
    );
    exit();
}

$is_content_filter_valid = is_null($current_content_filter)
                           || validate_content_filter(
                               $current_content_filter,
                               $content_types
                           );

$content_filters =
    get_content_filters($content_types, $basename, $current_content_filter);
$any_content_filter =
    get_any_content_filter($basename, is_null($current_content_filter));

$filters_content = include_template(
    'pages/feed/filters.php',
    [
        'content_filters' => $content_filters,
        'any_content_filter' => $any_content_filter,
    ]
);

$promo_content = include_template('common/promo.php', []);

// todo: тоже отптимизировать?
if (!$is_content_filter_valid) {
    http_response_code(BAD_REQUEST_STATUS);

    $filter_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Параметры фильтрации заданы некорректно',
            'link_description' => 'Сброс параметров',
            'link_url' => $basename,
        ]
    );

    $page_content = include_template(
        'pages/feed/page.php',
        [
            'filters_content' => $filters_content,
            'main_content' => $filter_error_message,
            'promo_content' => $promo_content,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);

    exit();
}

$post_card = get_feed_posts(
    $db_connection,
    [
        'content_type_id' => $current_content_filter
    ]
);

// todo: decorate feed page ?
// todo: handle error;
// todo: handle empty state;

$main_content = include_template(
    'pages/feed/main.php',
    [
        'post_cards' => $post_card,
    ]
);

$page_content = include_template(
    'pages/feed/page.php',
    [
        'main_content' => $main_content,
        'filters_content' => $filters_content,
        'promo_content' => $promo_content
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
