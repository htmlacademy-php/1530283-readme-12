<?php

require_once 'constants.php';

/**
 * Функция возвращает разметку текстового контента карточки публикации.
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле
 * текстового контента text_content.
 *
 * @param  array  $post_card  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка текстового контента карточки публикации
 */
function decorate_post_card_text_content(array $post_card): string
{
    $text_content = $post_card['text_content'];

    return include_template(
        'partials/post-card/text-content.php',
        [
            'text_content' => $text_content,
        ]
    );
}

/**
 * Функция возвращает разметку контента карточки публикации цитаты.
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле
 * текстового контента text_content и строкового контента string_content.
 *
 * @param  array  $post_card  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка текстового контента карточки публикации цитаты
 */
function decorate_post_card_quote_content(array $post_card): string
{
    $text_content   = $post_card['text_content'];
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/quote-content.php',
        [
            'text_content'   => $text_content,
            'string_content' => $string_content,
        ]
    );
}


/**
 * Функция возвращает разметку фото контента карточки публикации.
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле
 * строкового контента string_content.
 *
 * @param  array  $post_card  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка фото контента карточки публикации
 */
function decorate_post_card_photo_content(array $post_card): string
{
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/photo-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

/**
 * Функция возвращает разметку ссылочного контента карточки публикации.
 *
 * Ограничения: Ассоциативный массив публикации должен содержать поле
 * строкового контента string_content.
 *
 * @param  array  $post_card  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка ссылочного контента карточки публикации
 */
function decorate_post_card_link_content(array $post_card): string
{
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/link-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

/**
 * Функция возвращает разметку контента карточки публикации
 * в зависимости от типа контента публикации.
 *
 * Ограничения:
 * 1. Ассоциативный массив публикации должен содержать поле с типом контента
 * content_type. Допустимые значения - text, quote, link, photo.
 * 2. Ассоциативный массив публикации должен содержать поля содержащие текстовый
 * и/или строковый контент (text_content и/или string_content) в зависимости
 * от типа контента.
 *
 * @param  array  $post_card  Данные публикации в виде ассоциативного массива
 *
 * @return string Разметка контента карточки публикации
 */
function decorate_post_card_content(array $post_card): string
{
    $content_type = $post_card['content_type'];

    $decorate = "decorate_post_card_${content_type}_content";

    return $decorate($post_card);
}
