<?php
const TEXT_SEPARATOR = ' ';

/**
* Функция обрезает текст с учетом максимально заданнной длины, сохраняя целостность слов.
* При обрезке текста после последнего слова добавляется многоточие.
* Длина обрезанного текста рассчитывается без учета добавленного многоточия.
* Ограничения: Длина первого слова исходного текста не должна превышать максимальную длину.
* @param string $text Исходный текст
* @param int $max_length Максимальная длина текста
* @return string Обрезанный текст
*/
function crop_text (string $text, int $max_length): string
{
    $words = explode(TEXT_SEPARATOR, $text);
    $words_count = count($words);

    $current_word_index = 0;
    $current_text_length = 0;

    while ($current_word_index < $words_count)  {
        $current_text_length += mb_strlen($words[$current_word_index]);

        if ($current_text_length > $max_length) {
            break;
        }

        $current_text_length++;
        $current_word_index++;
    }

    $is_cropped = $current_word_index < $words_count;

    if (!$is_cropped) {
        return $text;
    }

    $cropped_words = array_slice($words, 0, $current_word_index);

    return implode(TEXT_SEPARATOR, $cropped_words) . '...';
}

/**
* Функция шаблонизирует контент текстового поста
* @param string $text Контент текстового поста
* @param int $max_length Максимальная длина, показываемого текста. По умолчанию - 300 символов.
* @return string Шаблон контента текстового поста
*/
function decorate_post_text_content (string $text, int $max_length = 300): string
{
    $cropped_text = crop_text($text, $max_length);

    if ($text === $cropped_text) {
        return "<p>$text</p>";
    }

    return "
        <p>$cropped_text</p>
        <a class='post-text__more-link' href='#'>Читать далее</a>
    ";
}
