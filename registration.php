<?php

require_once 'utils/helpers.php';

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Регистрация',
    'page_modifier' => 'registration',
    'basename' => $basename
];

$page_content = include_template('registration-form.php', $layout_data);

$layout_data['content'] = $page_content;

$layout_content = include_template('layouts/guest.php', $layout_data);

print($layout_content);
