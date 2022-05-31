<?php

require_once 'utils/helpers.php';
require_once 'utils/decorators.php';

/**
 * Функция рендерит состояние некорректно заданной фильтрации или сортировки
 * страницы 'Популярное'.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и сортировки,
 * т.е. принимает готовую разметку данной секции для шаблонов popular/page.php
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона popular/page.php, кроме основного контента страницы.
 *
 * @param  string  $popular_filters_content - разметка секции фильтрации и
 * сортировки по типу контента
 * @param  array  $layout_data - данные для шаблона страницы 'Популярное'
 */
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

/**
 * Функция рендерит страницу 'Популярное' в зависимости от переданного массива
 * публикаций.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и сортировки,
 * т.е. принимает готовую разметку данной секции для шаблонов popular/page.php
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона popular/page.php, кроме основного контента страницы.
 *
 * @param  string  $popular_filters_content - разметка секции фильтрации и
 * сортировки по типу контента
 * @param  array  $post_cards - массив публикаций в виде ассоциативных
 * массивов
 * @param  array  $layout_data - прочие данные для шаблона страницы 'Популярное'
 */
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
