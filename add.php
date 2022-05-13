<?php

require_once 'helpers.php';
require_once 'init/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print($_POST['title']);
    exit();
}

$layout_data = [
    'title' => 'Популярное',
    'is_auth' => 1,
    'user_name' => 'Евгений',
    'page_modifier' => 'adding-post',
    'content' => '',
];

$content_type = 'photo';

$content_fields = include_template(
    "partials/add-post-form/$content_type-content-fields.php",
    []
);

$with_photo_file = $content_type === 'photo';

$page_content = include_template(
    'add-post-form.php',
    [
        'content_fields' => $content_fields,
        'with_photo_file' => $with_photo_file,
    ]
);

$layout_data['content'] = $page_content;

$layout_content = include_template('layout.php', $layout_data);

print($layout_content);
