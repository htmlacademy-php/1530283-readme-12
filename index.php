<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

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
