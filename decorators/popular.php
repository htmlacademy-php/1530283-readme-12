<?php

/**
 * Функция возвращает разметку контента страницы с карточками
 * популярных публикаций сгенерированных из переданного массива публикаций.
 * В случае пустого массива публикаций вместо карточек будет выведно сообщение
 * об отсутствии публикаций.
 * В случае отсутсвия массива публикаций будет выведено сообщение об ошибке
 * загрузки публикаций.
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и сортировки,
 * т.е. принимает готовую разметку данной секции для шаблонов popular.php
 * и popular-empty.php.
 * 2. Структура ассоциативного массива публикации должна соответствовать
 * требованиям шаблона popular.php.
 *
 * @param  string        $popular_filters_content  Разметка секции фильтрации и сортировки
 * @param  array | null  $post_cards               Массив публикаций в виде ассоциативных массивов
 *
 * @return string Разметка контента страницы с карточками популярных публикаций
 */
function decorate_popular_page(
    string $popular_filters_content,
    array $post_cards
): string {
    if (is_null($post_cards)) {
        return include_template(
            'popular-empty.php',
            [
                'popular_filters_content' => $popular_filters_content,
                'title'                   => 'Ошибка',
                'content'                 => 'Не удалось загрузить публикации',
            ]
        );
    }

    if ( ! count($post_cards)) {
        return include_template(
            'popular-empty.php',
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
