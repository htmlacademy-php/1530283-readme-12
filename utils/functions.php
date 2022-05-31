<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';

/**
 * Функция обрезает текст с учетом максимально заданнной длины, сохраняя целостность слов.
 * При обрезке текста после последнего слова добавляется многоточие.
 * Длина обрезанного текста рассчитывается без учета добавленного многоточия.
 * Ограничения: Длина первого слова исходного текста не должна превышать максимальную длину.
 *
 * @param  string  $text  Исходный текст
 * @param  int  $max_length  Максимальная длина текста
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
 * Функция генерирует ссылку для сортировки публикаций по заданному полю.
 * Поле публикации, по которму производится сортировка должно соотествовать
 * структуре публикаций возвращаемых функицей get_posts.
 * Смена направления сортировки производится ссылкой, соответсвующей
 * текущему активному значения поля, по которму производится сортировка.
 * Направление сортировки вычисляется на основе текущего значения в адресной
 * строке.
 *
 * @param  string  $basename  URL страницы без GET параметров
 * @param  string  $sort_type  - поле публикации, по которму производится
 * сортировка
 * @param  string  $current_sort_type  - текущее значение поля публикации,
 * по которму производится сортировка
 * @param  bool  $is_order_reversed  - обратная сортировка (по возрастанию)
 *
 * @return string - итоговый URL страницы для получения списка публикаций
 * с учетом заданной сортировки
 */
function get_sort_url(
    string $basename,
    string $sort_type,
    string $current_sort_type,
    bool $is_order_reversed
): string {
    $query_params = $_GET;

    $query_params[SORT_TYPE_QUERY] = $sort_type;

    if ($sort_type === $current_sort_type) {
        $query_params[SORT_ORDER_REVERSED] =
            $is_order_reversed ? 'false' : 'true';
    }

    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * Функция генерирует ссылку для фильтрации публикаций по типу контента.
 * Для генерирации ссылки, соотвествующей отсутствию фильтрации,
 * id типа контента не передается в функцию.
 *
 * @param  string  $basename  URL страницы без GET параметров
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
 * @param  array  $content_types  - список типов контента
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  int | null  $current_content_type  - id текущего типа контента
 *
 * @return array - массив фильтров публикаций по типу контента
 */
function get_content_filters(
    array $content_types,
    string $basename,
    int $current_content_type = null
): array {
    $content_filters = $content_types;

    array_walk(
        $content_filters,
        function (&$filter) use ($basename, $current_content_type) {
            $id = $filter['id'];

            $url = get_content_filter_url($basename, $id);
            $active = $id === $current_content_type;

            $filter['url'] = $url;
            $filter['active'] = $active;
        }
    );

    return $content_filters;
}

/**
 * Функция возвращает данные ссылки для снятия фильтрации по типу контента.
 *
 * @param  string  $basename - URL страницы без GET параметров
 * @param bool  $is_active - ссылка активна
 *
 * @return array - ассоциативный массив с данымми ссылки
 */
function get_any_content_filter(
    string $basename,
    bool $is_active
): array {
    return [
        'name' => 'Все',
        'type' => 'all',
        'url' => get_content_filter_url($basename),
        'active' => $is_active,
    ];
}

/**
 * Функция возвращает массив типов сортировки публикаций.
 * Тип сортировки представляет собой ассоциативный массив аналогичный
 * элементами в массиве SORT_TYPE_OPTIONS дополненный полями url и active.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  string  $current_sort_type  - текущее значение поля публикации,
 * по которму производится сортировка
 * @param  bool  $is_order_reversed  - обратная сортировка (по возрастанию)
 *
 * @return array Массив типов сортировки публикаций
 */
function get_sort_types(
    string $basename,
    string $current_sort_type,
    bool $is_order_reversed
): array {
    $sort_types = SORT_TYPE_OPTIONS;

    array_walk(
        $sort_types,
        function (&$sort_type) use (
            $basename,
            $current_sort_type,
            $is_order_reversed
        ) {
            $value = $sort_type['value'];

            $url = get_sort_url(
                $basename,
                $value,
                $current_sort_type,
                $is_order_reversed
            );
            $active = $value === $current_sort_type;

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
 * @param  string  $current_content_filter  - id типа контента
 * @param  array  $content_types  - список доступных типов контента
 *
 * @return bool Результат валидации
 */
function validate_content_filter(
    string $current_content_filter,
    array $content_types
): bool {
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
 * Функция генерирует случайное имя файла.
 *
 * @param  string  $extension  - расширение файла ('tmp' - по умолчанию)
 *
 * @return string имя файла с расширением
 */
function get_random_file_name(string $extension = 'tmp'): string
{
    return md5(rand()) . ".$extension";
}

/**
 * Функция сохраняет файл перданный по ссылке.
 * Имя файла генерируется случайными образом.
 * Функция возвращает путь к сохраненному файлу.
 * В случае ошибки сохранения функция возвращает false.
 *
 * Ограничения: путь к месту сохранения файла задается относительно
 * директории проекта.
 *
 * @param  string  $url  - ссылка на файл
 * @param  string  $destination  - путь к месту сохранения файла ('uploads' -
 * по умолчанию)
 *
 * @return string | false - путь к сохраненному файлу
 */
function download_file(string $url, string $destination = 'uploads')
{
    $filter_content = file_get_contents($url);

    if (!$filter_content) {
        return false;
    }

    $original_file_name = basename($url);
    $extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
    $file_name = get_random_file_name($extension);
    $file_path = "$destination/$file_name";

    $result = file_put_contents($file_path, $filter_content);

    return $result ? $file_path : false;
}

/**
 * Функция сохраняет файл загруженный через форму.
 * Имя файла генерируется случайными образом.
 * Функция возвращает путь к сохраненному файлу относительно директории проекто.
 * В случае ошибки сохранения функция возвращает false.
 *
 * Ограничения: путь к месту сохранения файла задается относительно
 * директории проекта.
 *
 * @param  array  $temp_file
 * @param  string  $destination  - путь к месту сохранения файла ('uploads' -
 * по умолчанию)
 *
 * @return string | false - путь к сохраненному файлу
 */
function save_file(array $temp_file, string $destination = 'uploads')
{
    $extension = pathinfo($temp_file['name'], PATHINFO_EXTENSION);
    $file_name = get_random_file_name($extension);
    $relative_path = "$destination/$file_name";
    $absolute_path = dirname(__DIR__) . "/$relative_path";

    $result = move_uploaded_file($temp_file['tmp_name'], $absolute_path);

    return $result ? $relative_path : false;
}

/**
 * Функция обрабабатыват данные формы аутентификации.
 * В случае успешной аутентификации происходит
 * перенаправление на контроллер index.php и досрочный выход из сценария.
 * В случае некорректных данных, либо ошибки аутентификации,
 * возвращает ассоциативный массив с данными формы и ошибками валидации.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return array - данные формы и данные ошибок валидации
 */
function handle_login_form(mysqli $db_connection)
{
    $form_data = [];

    $form_data['email'] = $_POST['email'] ?? '';
    $form_data['password'] = $_POST['password'] ?? '';

    $errors = get_login_form_data_errors($form_data);

    $user = !count($errors) ? get_user_by_email(
        $db_connection,
        $form_data['email']
    ) : null;

    $is_password_correct = $user
                           && password_verify(
                               $form_data['password'],
                               $user['password_hash']
                           );

    if (!count($errors) && (!$user || !$is_password_correct)) {
        $errors['email'] = [
            'title' => 'Электронная почта',
            'description' => 'Неверное значение',
        ];

        $errors['password'] = [
            'title' => 'Пароль',
            'description' => 'Неверное значение',
        ];
    }

    if (!count($errors)) {
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    }

    return [
        'form_data' => $form_data,
        'errors' => $errors
    ];
}
