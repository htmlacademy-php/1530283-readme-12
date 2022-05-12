<?php

require_once 'constants.php';

/**
 * Функция возвращает разметку тектового контента полного представления публикации
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле текстового
 * контента text_content.
 *
 * @param  array  $post  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка текстового контента полного представления публикации
 */
function decorate_post_details_quote_content(array $post): string
{
    $text_content   = $post['text_content'];
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/quote-content.php',
        [
            'text_content'   => $text_content,
            'string_content' => $string_content,
        ]
    );
}

/**
 * Функция возвращает разметку контента полного представления публикации цитаты
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поля текстового
 * контента text_content и строкового контента string_content.
 *
 * @param  array  $post  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка контента полного представления публикации цитаты
 */
function decorate_post_details_text_content(array $post): string
{
    $text_content = $post['text_content'];

    return include_template(
        'partials/post-details/text-content.php',
        [
            'text_content' => $text_content,
        ]
    );
}

/**
 * Функция возвращает разметку фото контента полного представления публикации
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле строкового
 * контента string_content.
 *
 * @param  array  $post  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка текстового фото полного представления публикации
 */
function decorate_post_details_photo_content(array $post): string
{
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/photo-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

/**
 * Функция возвращает разметку ссылочного контента полного представления публикации
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле строкового
 * контента string_content.
 *
 * @param  array  $post  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка ссылочного фото полного представления публикации
 */
function decorate_post_details_link_content(array $post): string
{
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/link-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

/**
 * Функция возвращает разметку контента полного представления публикации
 * в зависимости от типа контента публикации.
 *
 * Ограничения:
 * 1. Ассоциативный массив публикации должен содержать поле с типом контента
 * content_type. Допустимые значения - text, quote, link, photo.
 * 2. Ассоциативный массив публикации должен содержать поля содержащие текстовый
 * и/или строковый контент (text_content и/или string_content) в зависимости
 * от типа контента.
 *
 * @param  array  $post  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка контента полного представления публикации
 */
function decorate_post_details_content(array $post): string
{
    $content_type = $post['content_type'];

    $decorate = POST_DETAILS_CONTENT_DECORATORS[$content_type];

    return $decorate($post);
}
