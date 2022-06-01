<?php

require_once 'utils/decorators.php';

/**
 * Функция рендерит страницу 'Результаты поиска' в зависимости от переданного
 * массива публикаций.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку разметка блока строки запроса,
 * т.е. принимает готовую разметку данной секции для шаблонов search/page.php
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона search/page.php, кроме основного контента страницы.
 *
 * @param  string  $query_content - разметка блока строки запроса
 * @param  array | null  $post_cards - массив публикаций в виде ассоциативных
 * массивов
 * @param  array  $layout_data - прочие данные для шаблона страницы 'Популярное'
 */
function render_search_page(
    string $query_content,
    $post_cards,
    array $layout_data
) {
    $page_content =
        decorate_search_page_content($query_content, $post_cards);

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
