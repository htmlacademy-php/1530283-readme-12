<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'models/post.php';
require_once 'init/db.php';

if (!isset($db_connection) or !$db_connection) {
    $error_layout = include_template('error.php', ['content' => 'Данные недоступны']);
    ob_end_clean();
    print($error_layout);
    return;
}

$post_cards = get_posts($db_connection, ['sort' => 'views_count']);

if (!$post_cards) {
    $error_layout = include_template('error.php', ['content' => 'Данные недоступны']);
    ob_end_clean();
    print($error_layout);
    return;
}

$page_content = include_template('main.php', [
    'post_cards' => $post_cards
]);

$layout_content = include_template('layout.php', [
    'title' => 'Популярное',
    'is_auth' => rand(0, 1),
    'user_name' => 'Евгений',
    'content' => $page_content,
    'page_modifier' => 'popular',
]);

print($layout_content);
