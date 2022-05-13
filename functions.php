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
    $words = explode(TEXT_SEPARATOR, $text);
    $words_count = count($words);

    $current_word_index = 0;
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

    if (!$is_cropped) {
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
    $date = date_create($date);
    $current_date = date_create();

    $interval = date_diff($current_date, $date);

    list(
        $days_total,
        $hours_remainder,
        $minutes_remainder
        )
        = explode(TEXT_SEPARATOR, date_interval_format($interval, '%a %h %i'));

    $days_total = (int)$days_total;
    $hours_remainder = (int)$hours_remainder;
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
        )
        = RELATIVE_TIME_UNITS[$unit];

    return "$amount "
           . get_noun_plural_form($amount, $one_unit, $two_units, $many_units);
}

/**
 * Функция проверяет установлено ли в адресной строке значение GET параметра.
 *
 * @param  string       $query_name   Название GET параметра
 * @param  string|null  $query_value  Значение GET параметра
 *
 * @return bool Результат проверки
 */
function is_query_active(string $query_name, string $query_value = null): bool
{
    $current_query_value = filter_input(
        INPUT_GET,
        $query_name,
        FILTER_SANITIZE_STRING
    );

    if (is_null($query_value)) {
        return is_null($current_query_value);
    }

    return $current_query_value === $query_value;
}

/**
 * Функция генерирует ссылку для сортировки публикаций по заданному полю.
 * Поле публикации, по которму производится сортировка должно соотествовать
 * структуре публикаций возвращаемых функицей get_posts.
 * Смена направления сортировки производится ссылкой, соответсвующей
 * текущему активному значения поля, по которму производится сортировка.
 * Направление сортировки вычисляется на основе текущего значения в адресной строке.
 *
 * @param  string  $basename   URL страницы без GET параметров
 * @param  string  $sort_type  Поле публикации, по которму производится сортировка
 *
 * @return string Итоговый URL страницы для получения списка публикаций с учетом заданной сортировки
 */
function get_sort_url(
    string $basename,
    string $sort_type
): string {
    $query_params = $_GET;
    $current_sort_order = filter_input(
        INPUT_GET,
        SORT_ORDER_REVERSED,
        FILTER_SANITIZE_STRING
    );

    $query_params[SORT_TYPE_QUERY] = $sort_type;

    if (is_query_active(SORT_TYPE_QUERY, $sort_type)) {
        $query_params[SORT_ORDER_REVERSED] = is_null($current_sort_order) ? ''
            : null;
    }

    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * Функция генерирует ссылку для фильтрации публикаций по типу контента.
 * Для генерирации ссылки, соотвествующей отсутствию фильтрации,
 * id типа контента не передается в функцию.
 *
 * @param  string      $basename         URL страницы без GET параметров
 * @param  int | null  $content_type_id  id типа контента публикации
 *
 * @return string Итоговый URL страницы для получения списка публикаций с учетом фильтрации
 */
function get_content_filter_url(
    string $basename,
    int $content_type_id = null
): string {
    $query_params = $_GET;
    $query_params[CONTENT_FILTER_QUERY] = $content_type_id;
    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * Функция возвращает массив фильтров публикаций по типу контента.
 * Фильтр представляет собой ассоциативный массив аналогичный типу контента,
 * дполненный полями url и active.
 *
 * @param  array   $content_types  список типов контента
 * @param  string  $basename       URL страницы без GET параметров
 *
 * @return array Массив фильтров публикаций по типу контента
 */
function get_content_filters(array $content_types, string $basename): array
{
    $content_filters = $content_types;

    array_walk(
        $content_filters,
        function (&$filter) use ($basename) {
            $id = $filter['id'];

            $url = get_content_filter_url($basename, $id);
            $active = is_query_active(CONTENT_FILTER_QUERY, $id);

            $filter['url'] = $url;
            $filter['active'] = $active;
        }
    );

    return $content_filters;
}

/**
 * Функция возвращает массив типов сортировки публикаций.
 * Тип сортировки представляет собой ассоциативный массив аналогичный
 * элементами в массиве SORT_TYPE_OPTIONS дополненный полями url и active.
 *
 * @param  string  $basename  URL страницы без GET параметров
 *
 * @return array Массив типов сортировки публикаций
 */
function get_sort_types(string $basename): array
{
    $sort_types = SORT_TYPE_OPTIONS;

    array_walk(
        $sort_types,
        function (&$sort_type) use ($basename) {
            $value = $sort_type['value'];

            $url = get_sort_url($basename, $value);
            $active = is_query_active(SORT_TYPE_QUERY, $value);


            $sort_type['url'] = $url;
            $sort_type['active'] = $active;
        }
    );

    return $sort_types;
}

/**
 * Функция валидирует переданное значение типа сортировки.
 * Валидные значения типов сортировки перечислены в ключах value в массиве
 * SORT_TYPE_OPTIONS.
 * Результат функции - true - если значение валидно, false - если не валидно.
 *
 * @param  string  $current_sort_type  - тип сортировки
 *
 * @return bool Результат валидации
 */
function validate_sort_type(string $current_sort_type): bool
{
    $available_sort_types = array_map(
        function ($option) {
            return $option['value'];
        },
        SORT_TYPE_OPTIONS
    );

    return array_search(
               $current_sort_type,
               $available_sort_types
           ) !== false;
}

/**
 * Функция валидирует переданное значение фильтра по типу контента.
 * Валидация осуществляется на основе переданного массива достпуных фильтров.
 * Результат функции - true - если значение валидно, false - если не валидно.
 *
 * Ограничения: Тип контента представляет собой ассоциативный массив,
 * содержащий ключ id.
 *
 * @param  string | null  $current_content_filter  - id типа контента
 * @param  array          $content_types           - список доступных типов контента
 *
 * @return bool Результат валидации
 */
function validate_content_filter(
    $current_content_filter,
    array $content_types
): bool {
    if (is_null($current_content_filter)) {
        return false;
    }

    $available_content_filters = array_map(
        function ($content_type) {
            return $content_type['id'];
        },
        $content_types
    );

    return array_search(
               $current_content_filter,
               $available_content_filters
           ) !== false;
}

/**
 * Функция валидирует значение заголовка формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_post_title_error(array $form_data)
{
    $title = $form_data['title'] ?? '';
    $length = mb_strlen($title);
    $error_title = 'Заголовок';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_TITLE_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать ' . MAX_TITLE_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_TITLE_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    return null;
}

/**
 * Функция валидирует значение заголовка формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Максимальная длина тега
 *
 * Ограничения:
 * 1. Функкция принимает теги в виде строки разделенной пробелами.
 * Не допускается наличие пробелов в начале и/или в конец строки, разделение
 * тегов множественными пробелами.
 * 2. Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_post_tags_error(array $form_data)
{
    $tags =
        $form_data['tags'] ? explode(TEXT_SEPARATOR, $form_data['tags']) : [];
    $error_title = 'Теги';

    foreach ($tags as $tag) {
        if (mb_strlen($tag) > MAX_TAG_LENGTH) {
            return [
                'title' => $error_title,
                'description' => "Один из тегов превышает допустимую длину "
                                 . MAX_TAG_LENGTH
                                 . ' ' . get_noun_plural_form(
                                     MAX_TAG_LENGTH,
                                     'символ',
                                     'символа',
                                     'символов'
                                 )
            ];
        }
    }

    return null;
}

/**
 * Функция валидирует значение фото ссылки формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Корректность URL
 * 4. Доступность фото по ссылке // todo: возможно это не здесь ?
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_photo_post_string_content_error(array $form_data)
{
    // todo: проверить наличие файла, в этом случае валидация игнорируется
    $string_content = $form_data['string_content'] ?? '';
    $length = mb_strlen($string_content);
    $error_title = 'Ссылка из интернета';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_STRING_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_STRING_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_TITLE_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    if (!filter_var($string_content, FILTER_VALIDATE_URL)) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный URL',
        ];
    }

    // todo: download file;

    return null;
}

/**
 * Функция валидирует значение видео ссылки формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Корректность URL
 * 4. Доступность видео по ссылке
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_video_post_string_content_error(array $form_data)
{
    $string_content = $form_data['string_content'] ?? '';
    $length = mb_strlen($string_content);
    $error_title = 'Ссылка youtube';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_STRING_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_STRING_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_STRING_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    if (!filter_var($string_content, FILTER_VALIDATE_URL)) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный URL',
        ];
    }

    if (check_youtube_url($string_content) !== true) {
        return [
            'title' => $error_title,
            'description' => 'Видео недоступно',
        ];
    }

    return null;
}

/**
 * Функция валидирует значение текста поста формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_text_post_text_content_error(array $form_data)
{
    $text_content = $form_data['text_content'] ?? '';
    $length = mb_strlen($text_content);
    $error_title = 'Текст поста';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_TEXT_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_TEXT_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_TEXT_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    return null;
}

/**
 * Функция валидирует значение автора цитаты формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_quote_post_string_content_error(array $form_data)
{
    $string_content = $form_data['string_content'] ?? '';
    $length = mb_strlen($string_content);
    $error_title = 'Автор цитаты';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_STRING_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_STRING_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_STRING_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    return null;
}

/**
 * Функция валидирует значение текста цитаты формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_quote_post_text_content_error(array $form_data)
{
    $text_content = $form_data['text_content'] ?? '';
    $length = mb_strlen($text_content);
    $error_title = 'Текст цитаты';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_TEXT_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_TEXT_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_TEXT_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    return null;
}

/**
 * Функция валидирует значение ссылки формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Корректность URL
 *
 * Ограничения:
 * Функция возвращает только первую ошибку валидации.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_link_post_string_content_error(array $form_data)
{
    $string_content = $form_data['string_content'] ?? '';
    $length = mb_strlen($string_content);
    $error_title = 'Ссылка';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_STRING_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_STRING_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_STRING_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    if (!filter_var($string_content, FILTER_VALIDATE_URL)) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный URL',
        ];
    }

    return null;
}

/**
 * Функция возвращает ассоциативный массив ошибок валидации формы создания
 * публикации. Ключами массива являются значения полей формы, а значениями -
 * ассоциативный массив ошибки валидации, содержащий название и описание ошибки.
 * В случае отсутствия ошибок возвращается пустой массив.
 *
 * Ограничения:
 * Допустимые значения типов контента - photo, link, text, quote, video.
 *
 * @param  array   $form_data     - ассоциативный массив полей формы и их значений
 * @param  string  $content_type  - тип контента публикации
 *
 * @return array<int, array{
 *   title: string,
 *   description: string
 * }> - массив ошибок валидации
 */
function get_post_form_data_errors(
    array $form_data,
    string $content_type
): array {
    $errors = [];

    foreach ($form_data as $field => $value) {
        $is_content_field = strpos($field, 'content') !== false;

        $get_error =
            $is_content_field ? "get_${content_type}_post_${field}_error"
                : "get_post_${field}_error";

        if (is_callable($get_error)) {
            $error = $get_error($form_data);

            if ($error) {
                $errors[$field] = $error;
            }
        }
    }

    return $errors;
}
