<?php

require_once 'utils/helpers.php';

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Авторизация',
    'page_modifier' => 'login',
    'basename' => $basename
];

$page_content = include_template('login-form.php', $layout_data);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/guest.php', $layout_data);

print($layout_content);
