<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'models/post.php';
require_once 'init/db.php';

$user = check_user();

$query = filter_input(INPUT_GET, SEARCH_QUERY, FILTER_SANITIZE_STRING);

$layout_data = [
    'title' => "Вы искали: $query",
    'user' => $user,
    'page_modifier' => 'search-results',
    'query' => $query,
];

$query_content = include_template('pages/search/query.php', [
    'query' => $query,
]);

// todo: temp posts
$posts = get_posts($db_connection);

$page_content = include_template('pages/search/page.php', [
    'query_content' => $query_content,
    'post_cards' => $posts
]);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
