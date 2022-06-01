<?php
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
    $page_content = (function () use ($query_content, $post_cards) {
        if (is_null($post_cards) || !count($post_cards)) {
            $empty_content = include_template(
                'pages/search/empty.php',
                ['back_url' => $_SERVER['HTTP_REFERER']]
            );

            return include_template(
                'pages/search/page.php',
                [
                    'query_content' => $query_content,
                    'main_content' => $empty_content,
                ]
            );
        }

        $main_content = include_template(
            'pages/search/main.php',
            ['post_cards' => $post_cards]
        );

        return include_template(
            'pages/search/page.php',
            [
                'query_content' => $query_content,
                'main_content' => $main_content,
            ]
        );
    })();

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
