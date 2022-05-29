<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';

$user = check_user();

$query = filter_input(INPUT_GET, SEARCH_QUERY, FILTER_SANITIZE_STRING);

$layout_data = [
    'title' => "Вы искали: $query",
    'user' => $user,
    'page_modifier' => 'search-results',
    'query' => $query,
];

$query_content = include_template('partials/search/query.php', [
    'query' => $query,
]);

$page_content = include_template('search.php', [
    'query_content' => $query_content,
]);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
