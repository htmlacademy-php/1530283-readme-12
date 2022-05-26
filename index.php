<?php

require_once 'utils/helpers.php';

session_start();

$user = $_SESSION['user'] ?? null;

if (!$user) {
    $layout_content = include_template('layouts/welcome.php', []);

    // todo: handle POST request

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

$page_content = include_template('feed.php', []);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/user.php', $layout_data);

print($layout_content);
