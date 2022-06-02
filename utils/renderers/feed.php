<?php

require_once 'utils/helpers.php';

/**
 * Функция рендерит состояние некорректно заданной фильтрации страницы
 * 'Моя лента'.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и промо-секции,
 * т.е. принимает готовую разметку данных секций для шаблонов feed/page.php.
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона feed/page.php, кроме основного контента страницы.
 *
 * @param  string  $feed_filters_content  - разметка секции фильтрации и
 * сортировки
 * @param  string  $promo_content  - разметка промо-секции
 * @param  array  $layout_data  - данные для шаблона страницы 'Моя лента'
 */
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

/**
 * Функция рендерит страницу 'Моя лента' в зависимости от переданного массива
 * публикаций.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и промо-секции,
 * т.е. принимает готовую разметку данных секций для шаблонов feed/page.php.
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона feed/page.php, кроме основного контента страницы.
 *
 * @param  string  $feed_filters_content  - разметка секции фильтрации и
 * сортировки
 * @param  string  $promo_content  - разметка промо-секции
 * @param  array | null  $post_cards  - массив публикаций в виде ассоциативных
 * массивов
 * @param  array  $layout_data  - данные для шаблона страницы 'Моя лента'
 */
function render_feed_page(
    string $feed_filters_content,
    string $promo_content,
    $post_cards,
    array $layout_data
) {
    $page_content =
        (function () use ($post_cards, $feed_filters_content, $promo_content) {
            if (is_null($post_cards)) {
                $error_content = include_template(
                    'common/message.php',
                    [
                        'title' => 'Ошибка',
                        'content' => 'Не удалось загрузить публикации',
                    ]
                );

                return include_template(
                    'pages/feed/page.php',
                    [
                        'filters_content' => $feed_filters_content,
                        'main_content' => $error_content,
                        'promo_content' => $promo_content,
                    ]
                );
            }

            if (!count($post_cards)) {
                $empty_content = include_template(
                    'common/message.php',
                    ['title' => 'Ничего не найдено']
                );

                return include_template(
                    'pages/feed/page.php',
                    [
                        'filters_content' => $feed_filters_content,
                        'main_content' => $empty_content,
                        'promo_content' => $promo_content,
                    ]
                );
            }

            $main_content = include_template(
                'pages/feed/main.php',
                ['post_cards' => $post_cards]
            );

            return include_template(
                'pages/feed/page.php',
                [
                    'main_content' => $main_content,
                    'filters_content' => $feed_filters_content,
                    'promo_content' => $promo_content,
                ]
            );
        })();

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
