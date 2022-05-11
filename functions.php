<?php

require_once 'constants.php';

/**
 * Функция обрезает текст с учетом максимально заданнной длины, сохраняя целостность слов.
 * При обрезке текста после последнего слова добавляется многоточие.
 * Длина обрезанного текста рассчитывается без учета добавленного многоточия.
 * Ограничения: Длина первого слова исходного текста не должна превышать максимальную длину.
 *
 * @param  string  $text        Исходный текст
 * @param  int     $max_length  Максимальная длина текста
 *
 * @return string Обрезанный текст
 */
function crop_text(string $text, int $max_length): string
{
    $words       = explode(TEXT_SEPARATOR, $text);
    $words_count = count($words);

    $current_word_index  = 0;
    $current_text_length = 0;

    while ($current_word_index < $words_count) {
        $current_text_length += mb_strlen($words[$current_word_index]);

        if ($current_text_length > $max_length) {
            break;
        }

        $current_text_length++;
        $current_word_index++;
    }

    $is_cropped = $current_word_index < $words_count;

    if ( ! $is_cropped) {
        return $text;
    }

    $cropped_words = array_slice($words, 0, $current_word_index);

    return implode(TEXT_SEPARATOR, $cropped_words) . '...';
}

/**
 * Функция преобразует строку даты из произвольного формата в формат стандарта ISO 8601.
 * Ограничения: произвольный формат даты должен поддерживаться стандартной функцией strtotime.
 *
 * @param  string  $date  Строка даты в произвольном формате
 *
 * @return string Строка даты в формате стандарта ISO 8601
 */
function format_iso_date_time(string $date): string
{
    return date('c', strtotime($date));
}

/** Форматирует преобразует строку даты из произвольного формата в дату в относительном формате, удобном для пользователя:
 * - если до текущего времени прошло меньше 60 минут, то формат будет вида «% минут назад»;
 * - если до текущего времени прошло не меньше 60 минут, но меньше 24 часов, то формат будет вида «% часов назад»;
 * - если до текущего времени прошло не меньше 24 часов, но меньше 7 дней, то формат будет вида «% дней назад»;
 * - если до текущего времени прошло не меньше 7 дней, но меньше 5 недель, то формат будет вида «% недель назад»;
 * - если до текущего времени прошло больше 5 недель, то формат будет вида «% месяцев назад».
 * Ограничения: произвольный формат даты должен поддерживаться стандартной функцией date_create.
 *
 * @param  string  $date  Строка даты в произвольном формате
 *
 * @return string Строка даты в относительном формате, удобном для пользователя
 */
function format_relative_time(string $date): string
{
    $date         = date_create($date);
    $current_date = date_create();

    $interval = date_diff($current_date, $date);

    list(
        $days_total,
        $hours_remainder,
        $minutes_remainder
        )
        = explode(TEXT_SEPARATOR, date_interval_format($interval, '%a %h %i'));

    $days_total        = (int)$days_total;
    $hours_remainder   = (int)$hours_remainder;
    $minutes_remainder = (int)$minutes_remainder;

    $weeks_total = (int)floor($days_total / DAYS_IN_WEEK);

    list(
        'unit' => $unit,
        'amount' => $amount,
        )
        = (function () use (
        $weeks_total,
        $days_total,
        $hours_remainder,
        $minutes_remainder
    ) {
        if ($weeks_total >= 5) {
            $months_total = floor($days_total / DAYS_IN_MONTH);

            return [
                'unit'   => 'month',
                'amount' => $months_total,
            ];
        }

        if ($weeks_total >= 1) {
            return [
                'unit'   => 'week',
                'amount' => $weeks_total,
            ];
        }

        if ($days_total >= 1) {
            return [
                'unit'   => 'day',
                'amount' => $days_total,
            ];
        }

        if ($hours_remainder >= 1) {
            return [
                'unit'   => 'hour',
                'amount' => $hours_remainder,
            ];
        }

        return [
            'unit'   => 'minute',
            'amount' => $minutes_remainder,
        ];
    })();

    list(
        'one' => $one_unit,
        'two' => $two_units,
        'many' => $many_units,
        )
        = RELATIVE_TIME_UNITS[$unit];

    return "$amount "
           . get_noun_plural_form($amount, $one_unit, $two_units, $many_units)
           . RELATIVE_TIME_POSTFIX;
}

/**
 * @param  string       $query_name
 * @param  string|null  $query_value
 *
 * @return bool
 */
function is_query_active(string $query_name, string $query_value = null): bool
{
    $current_filter_id = $_GET[$query_name];

    if (is_null($query_value)) {
        return is_null($current_filter_id);
    }

    return $current_filter_id === $query_value;
}

/**
 * @param  string  $basename
 * @param  string  $sort_type
 *
 * @return string
 */
function get_sort_url(
    string $basename,
    string $sort_type
): string {
    $query_params       = $_GET;
    $current_sort_order = $query_params[SORT_ORDER_REVERSED];

    $query_params[SORT_TYPE_QUERY] = $sort_type;

    if (is_query_active(SORT_TYPE_QUERY, $sort_type)) {
        $query_params[SORT_ORDER_REVERSED] = is_null($current_sort_order) ? ''
            : null;
    }

    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * @param  string      $basename
 * @param  int | null  $filter_id
 *
 * @return string
 */
function get_filter_url(
    string $basename,
    int $filter_id = null
): string {
    $query_params                     = $_GET;
    $query_params[CONTENT_TYPE_QUERY] = $filter_id;
    $query_string                     = http_build_query($query_params);

    return "/$basename?$query_string";
}
