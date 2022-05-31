<?php

require_once 'utils/decorators.php';

// todo: add phpDoc
function render_popular_filter_error(
    string $popular_filters_content,
    array $layout_data
) {
    $filter_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Параметры фильтрации или сортировки заданы некорректно',
            'link_description' => 'Сброс параметров',
            'link_url' => $layout_data['basename'],
        ]
    );

    $page_content = include_template(
        'pages/popular/page.php',
        [
            'filters_content' => $popular_filters_content,
            'main_content' => $filter_error_message,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}

// todo: add phpDoc
function render_popular_page(
    string $popular_filters_content,
    array $post_cards,
    array $layout_data
) {
    $page_content =
        decorate_popular_page_content($popular_filters_content, $post_cards);

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
