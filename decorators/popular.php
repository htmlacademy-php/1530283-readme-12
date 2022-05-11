<?php

function decorate_popular_page(
    string $popular_filters_content,
    $post_cards
): string {
    if (is_null($post_cards)) {
        return include_template(
            'popular_empty.php',
            [
                'popular_filters_content' => $popular_filters_content,
                'title'                   => 'Ошибка',
                'content'                 => 'Не удалось загрузить публикации',
            ]
        );
    }

    if ( ! count($post_cards)) {
        return include_template(
            'popular_empty.php',
            [
                'popular_filters_content' => $popular_filters_content,
                'title'                   => 'Ничего не найдено',
            ]
        );
    }

    return include_template(
        'popular.php',
        [
            'popular_filters_content' => $popular_filters_content,
            'post_cards'              => $post_cards,
        ]
    );
}
