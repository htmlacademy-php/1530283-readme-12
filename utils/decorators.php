<?php

/**
 * Функция возвращает разметку контента страницы с карточками
 * популярных публикаций сгенерированных из переданного массива публикаций.
 * В случае пустого массива публикаций вместо карточек будет выведно сообщение
 * об отсутствии публикаций.
 * В случае отсутсвия массива публикаций будет выведено сообщение об ошибке
 * загрузки публикаций.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секции фильтрации и сортировки,
 * т.е. принимает готовую разметку данной секции для шаблонов popular/page.php
 * и popular/page-empty.php.
 * 2. Структура ассоциативного массива публикации должна соответствовать
 * требованиям шаблона popular/main.php.
 *
 * @param  string  $popular_filters_content  Разметка секции фильтрации и сортировки
 * @param  array | null  $post_cards  Массив публикаций в виде ассоциативных массивов
 *
 * @return string Разметка контента страницы с карточками популярных публикаций
 */
function decorate_popular_page_content(
    string $popular_filters_content,
    $post_cards
): string {
    if (is_null($post_cards)) {
        $error_content = include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить публикации'
            ]
        );

        return include_template(
            'pages/popular/page.php',
            [
                'filters_content' => $popular_filters_content,
                'main_content' => $error_content,
            ]
        );
    }

    if (!count($post_cards)) {
        $empty_content = include_template(
            'common/message.php',
            ['title' => 'Ничего не найдено']
        );

        return include_template(
            'pages/popular/page.php',
            [
                'filters_content' => $popular_filters_content,
                'main_content' => $empty_content,
            ]
        );
    }

    $main_content = include_template(
        'pages/popular/main.php',
        ['post_cards' => $post_cards]
    );

    return include_template(
        'pages/popular/page.php',
        [
            'filters_content' => $popular_filters_content,
            'main_content' => $main_content,
        ]
    );
}

/**
 * Функция возвращает разметку контента карточки публикации
 * в зависимости от типа контента публикации.
 *
 * Ограничения:
 * 1. Ассоциативный массив публикации должен содержать поле с типом контента
 * content_type. Допустимые значения - text, quote, link, photo, video.
 * 2. Ассоциативный массив публикации должен содержать поля содержащие текстовый
 * и/или строковый контент (text_content и/или string_content) в зависимости
 * от типа контента.
 *
 * @param  array  $post_card  - данные публикации в виде ассоциативного массива
 * @param  string | null  $page  - название страницы (опционально)
 *
 * @return string Разметка контента карточки публикации
 */
function decorate_post_card_content(
    array $post_card,
    string $page = null
): string {
    $id = $post_card['id'];
    $content_type = $post_card['content_type'];
    $text_content = $post_card['text_content'] ?? '';
    $string_content = $post_card['string_content'] ?? '';

    $template_path = $page ? "pages/$page/post-card/content/$content_type.php"
        : "common/post-card/content/$content_type.php";

    return include_template(
        $template_path,
        [
            'id' => $id,
            'text_content' => $text_content,
            'string_content' => $string_content,
        ]
    );
}


