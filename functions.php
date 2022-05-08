<?php
require_once 'constants.php';

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
 * @param string $string_content Текстовый контент поста
 * @param string $text_content Строковый контент поста
 * @param int $max_length Максимальная длина, показываемого текста. По умолчанию - 300 символов.
 * @return string Шаблон контента текстового поста
 */
function decorate_post_text_content (string $text_content, string $string_content, int $max_length = 300): string
{
    $text_content = htmlspecialchars($text_content);
    $cropped_text_content = crop_text($text_content, $max_length);

    if ($text_content === $cropped_text_content) {
        return "<p>$text_content</p>";
    }

    return "
        <p>$cropped_text_content</p>
        <a class='post-text__more-link' href='#'>Читать далее</a>
    ";
}

/**
 * Функция шаблонизирует контент поста цитаты
 * @param string $string_content Текстовый контент поста
 * @param string $text_content Строковый контент поста
 * @return string Шаблон контента поста цитаты
 */
function decorate_post_quote_content (string $text_content, string $string_content): string
{
    $text_content = htmlspecialchars($text_content);
    $string_content = htmlspecialchars($string_content);

    return "
        <blockquote>
            <p>$text_content</p>
            <cite>$string_content</cite>
        </blockquote>
    ";
}

/**
 * Функция шаблонизирует контент поста изображения
 * @param string $string_content Текстовый контент поста
 * @param string $text_content Строковый контент поста
 * @return string Шаблон контента поста изображения
 */
function decorate_post_photo_content (string $text_content, string $string_content): string
{
    return "
        <div class=\"post-photo__image-wrapper\">
            <img src=\"img/$string_content\" alt=\"Фото от пользователя\" width=\"360\" height=\"240\">
        </div>
    ";
}

/**
 * Функция шаблонизирует контент поста ссылки
 * @param string $string_content Текстовый контент поста
 * @param string $text_content Строковый контент поста
 * @return string Шаблон контента поста ссылки
 */
function decorate_post_link_content (string $text_content, string $string_content): string
{
    $string_content = strip_tags($string_content);

    return "
        <div class=\"post-link__wrapper\">
            <a class=\"post-link__external\" href=\"http://$string_content\" title=\"Перейти по ссылке\">
                <div class=\"post-link__info-wrapper\">
                    <div class=\"post-link__icon-wrapper\">
                        <img src=\"https://www.google.com/s2/favicons?domain=vitadental.ru\" alt=\"Иконка\">
                    </div>
                    <div class=\"post-link__info\">
                        <h3>Описание ссылки</h3>
                    </div>
                </div>
                <span>$string_content</span>
            </a>
        </div>
    ";
}

/**
 * Функция преобразует строку даты из произвольного формата в формат стандарта ISO 8601.
 * Ограничения: произвольный формат даты должен поддерживаться стандартной функцией strtotime.
 * @param string $date Строка даты в произвольном формате
 * @return string Строка даты в формате стандарта ISO 8601
 */
function format_iso_date_time (string $date): string {
    return date('c', strtotime($date));
}

/** Форматирует преобразует строку даты из произвольного формата в дату в относительном формате, удобном для пользователя:
 * - если до текущего времени прошло меньше 60 минут, то формат будет вида «% минут назад»;
 * - если до текущего времени прошло не меньше 60 минут, но меньше 24 часов, то формат будет вида «% часов назад»;
 * - если до текущего времени прошло не меньше 24 часов, но меньше 7 дней, то формат будет вида «% дней назад»;
 * - если до текущего времени прошло не меньше 7 дней, но меньше 5 недель, то формат будет вида «% недель назад»;
 * - если до текущего времени прошло больше 5 недель, то формат будет вида «% месяцев назад».
 * Ограничения: произвольный формат даты должен поддерживаться стандартной функцией date_create.
 * @param string $date Строка даты в произвольном формате
 * @return string Строка даты в относительном формате, удобном для пользователя
 */
function format_relative_time(string $date): string {
    $date = date_create($date);
    $current_date = date_create();

    $interval = date_diff($current_date, $date);

    list(
        $days_total,
        $hours_remainder,
        $minutes_remainder
        ) = explode(TEXT_SEPARATOR, date_interval_format($interval, '%a %h %i'));

    $days_total = (int) $days_total;
    $hours_remainder = (int) $hours_remainder;
    $minutes_remainder = (int) $minutes_remainder;

    $weeks_total = (int) floor($days_total / DAYS_IN_WEEK);

    list(
        'unit' => $unit,
        'amount' => $amount,
        ) = (function () use ($weeks_total, $days_total, $hours_remainder, $minutes_remainder) {
        if ($weeks_total >= 5) {
            $months_total = floor($days_total / DAYS_IN_MONTH);

            return [
                'unit' => 'month',
                'amount' => $months_total,
            ];
        }

        if ($weeks_total >= 1) {
            return [
                'unit' => 'week',
                'amount' => $weeks_total,
            ];
        }

        if ($days_total >= 1) {
            return [
                'unit' => 'day',
                'amount' => $days_total,
            ];
        }

        if ($hours_remainder >= 1) {
            return [
                'unit' => 'hour',
                'amount' => $hours_remainder,
            ];
        }

        return [
            'unit' => 'minute',
            'amount' => $minutes_remainder,
        ];
    })();

    list(
        'one' => $one_unit,
        'two' => $two_units,
        'many' => $many_units,
        ) = RELATIVE_TIME_UNITS[$unit];

    return "$amount "
        . get_noun_plural_form($amount, $one_unit, $two_units, $many_units)
        . RELATIVE_TIME_POSTFIX;
}
