<?php

// todo: add phpDoc
function render_message_page(
    array $message_data,
    string $layout_type,
    array $layout_data = []
) {
    $page_content = include_template(
        'common/message.php',
        $message_data
    );

    $layout_data['content'] = $page_content;

    $layout_content =
        include_template("layouts/$layout_type.php", $layout_data);

    print($layout_content);
}
