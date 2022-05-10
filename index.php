<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'models/content_type.php';
require_once 'init/db.php';

if (!isset($db_connection) or !$db_connection) {
    $error_layout = include_template('error.php', ['content' => 'Данные недоступны']);
    ob_end_clean();
    print($error_layout);
    return;
}

$content_types = get_content_types($db_connection);
$post_cards = get_posts($db_connection, ['sort' => 'views_count']);

if (!$content_types or !$post_cards) {
    $error_layout = include_template('error.php', ['content' => 'Данные недоступны']);
    ob_end_clean();
    print($error_layout);
    return;
}

$popular_filters_content = include_template('partials/popular-filters.php', ['content_types' => $content_types]);

$page_content = include_template('main.php', [
    'popular_filters_content' => $popular_filters_content,
    'post_cards' => $post_cards,
]);

$layout_content = include_template('layout.php', [
    'title' => 'Популярное',
    'is_auth' => rand(0, 1),
    'user_name' => 'Евгений',
    'page_modifier' => 'popular',
    'content' => $page_content,
]);

print($layout_content);
