<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'init/db.php';

if (!isset($db_connection) or !$db_connection) {
    // todo: 500 Error template
    print '<h1>Error occurred</h1>';
    exit();
}

$sql = "
SELECT
    posts.id,
    posts.title,
    posts.string_content,
    posts.text_content,
    posts.created_at,
    posts.views_count,
    users.login AS author_login,
    users.avatar_url AS author_avatar,
    content_types.icon AS content_type
FROM posts
    JOIN users ON posts.author_id = users.id
    JOIN content_types ON posts.content_type_id = content_types.id
";

$result = mysqli_query($db_connection, $sql);

$post_cards = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
