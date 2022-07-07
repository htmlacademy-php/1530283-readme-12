<?php

require_once 'init/common.php';
require_once 'utils/constants.php';
require_once 'utils/helpers.php';
require_once 'utils/functions.php';
require_once 'utils/renderers/search.php';
require_once 'models/post.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$query = filter_input(INPUT_GET, SEARCH_QUERY, FILTER_SANITIZE_STRING);

$layout_data = [
    'title' => "Вы искали: $query",
    'user' => $user_session,
    'page_modifier' => 'search-results',
    'query' => $query,
    'basename' => $basename
];

$query_content = include_template(
    'pages/search/query.php',
    ['query' => $query]
);

$is_hashtag_mode = $query[0] === '#';

if ($is_hashtag_mode) {
    $query = substr($query, 1);
}

$post_cards = $is_hashtag_mode ? get_posts_by_hashtag(
    $db_connection,
    $user_session['id'],
    $query
)
    : get_posts_by_query($db_connection, $user_session['id'], $query);

if (is_null($post_cards)) {
    http_response_code(SERVER_ERROR_STATUS);
}

render_search_page($query_content, $post_cards, $layout_data);
