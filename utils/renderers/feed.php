<?php

require_once 'utils/helpers.php';
require_once 'utils/decorators.php';

// todo: add phpDoc
function render_feed_filter_error(
    string $feed_filters_content,
    string $promo_content,
    array $layout_data
) {
    $filter_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Параметры фильтрации заданы некорректно',
            'link_description' => 'Сброс параметров',
            'link_url' => $layout_data['basename'],
        ]
    );

    $page_content = include_template(
        'pages/feed/page.php',
        [
            'filters_content' => $feed_filters_content,
            'main_content' => $filter_error_message,
            'promo_content' => $promo_content,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}

// todo: add phpDoc
function render_feed_page(
    string $popular_filters_content,
    string $promo_content,
    $post_cards,
    array $layout_data
) {
    $page_content =
        decorate_feed_page_content(
            $popular_filters_content,
            $promo_content,
            $post_cards
        );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
