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
 * Функция возвращает ссылки для пагинации.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  int  $current_page  - номер текущей страницы
 * @param  bool  $is_next_page  - доступность следующей страницы
 *
 * @return array{
 *     prev: string | null,
 *     next: string | null
 * } - ссылки пагинации
 */
function get_pagination(
    string $basename,
    int $current_page,
    bool $is_next_page
): array {
    $prev_query_params = $_GET;
    $next_query_params = $_GET;

    $is_prev_page = $current_page > INITIAL_POSTS_PAGE;

    $prev_url = null;
    $next_url = null;

    if ($is_prev_page) {
        $prev_query_params[PAGE_QUERY] = $current_page - 1;
        $prev_query_string = http_build_query($prev_query_params);
        $prev_url = "/$basename?$prev_query_string";
    }

    if ($is_next_page) {
        $next_query_params[PAGE_QUERY] = $current_page + 1;
        $next_query_string = http_build_query($next_query_params);
        $next_url = "/$basename?$next_query_string";
    }

    return [
        'prev' => $prev_url,
        'next' => $next_url,
    ];
}

/**
 * Функция возвращает ссылку для показа показа полного списка комментариев.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 *
 * @return string - ссылка показа полного списка комментариев
 */
function get_expand_comments_url(string $basename): string
{
    $query_params = $_GET;
    $query_params[COMMENTS_EXPANDED] = 'true';
    $query_string = http_build_query($query_params);

    return "/$basename?$query_string#comments";
}

/**
 * Функция возвращает ссылку для показа показа полного списка комментариев.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 *
 * @return string - ссылка показа полного списка комментариев
 */
function get_open_comments_url(string $basename, int $post_id): string
{
    $query_params = $_GET;
    $query_params[COMMENTS_EXPANDED] = 'false';
    $query_params[COMMENTS_POST_ID_QUERY] = $post_id;
    $query_string = http_build_query($query_params);

    return "/$basename?$query_string#comments";
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
 * @param  string  $basename  - URL страницы без GET параметров
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
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  int | null  $content_type_id  - id типа контента публикации
 *
 * @return string - итоговый URL страницы для получения списка публикаций
 * с учетом фильтрации
 */
function get_content_filter_url(
    string $basename,
    int $content_type_id = null
): string {
    $query_params = $_GET;
    $query_params[CONTENT_FILTER_QUERY] = $content_type_id;
    $query_params[PAGE_QUERY] = null;
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
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  bool  $is_active  - ссылка активна
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
 * @return array - массив типов сортировки публикаций
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
 * Функция генерирует ссылку переключения на заданный таб страницы профиля
 * пользователя.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  string  $tab_value  - значение таба
 *
 * @return string - итоговый URL страницы для переключения на заданный таб
 */
function get_profile_tab_url(
    string $basename,
    string $tab_value
): string {
    $query_params = $_GET;
    $query_params[TAB_QUERY] = $tab_value;
    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * Функция возвращает массив с данными табов для страницы профиля пользователя.
 * Данные таба представляют собой ассоциативный массив аналогичный
 * элементам в массиве PROFILE_TABS дополненный полями url и active.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  string  $current_tab  - текущий таб
 *
 * @return array - массив c данными табов
 */
function get_profile_tabs(
    string $basename,
    string $current_tab
): array {
    $profile_tabs = PROFILE_TABS;

    array_walk(
        $profile_tabs,
        function (&$profile_tab) use (
            $basename,
            $current_tab
        ) {
            $value = $profile_tab['value'];

            $url = get_profile_tab_url($basename, $value);
            $active = $value === $current_tab;

            $profile_tab['url'] = $url;
            $profile_tab['active'] = $active;
        }
    );

    return $profile_tabs;
}

/**
 * Функция валидирует переданное значение таба страницы профиля пользователя.
 * Валидные значения табов перечислены в ключах value в массиве PROFILE_TABS.
 * Результат функции - true - если значение валидно, false - если не валидно.
 *
 * @param  string  $current_tab  - выбранный таб
 *
 * @return bool Результат валидации
 */
function validate_profile_tab(string $current_tab): bool
{
    $available_profile_tabs = array_map(
        function ($option) {
            return $option['value'];
        },
        PROFILE_TABS
    );

    return array_search(
               $current_tab,
               $available_profile_tabs
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
 * Функция возвращает название файла из переданного URL.
 *
 * @param  string  $url - URL
 *
 * @return string - название файла
 */
function parse_filename(string $url): string {
    $file_name = basename($url);

    if (strpos($file_name, HASH_CHAR) !== false) {
        $file_name = explode(HASH_CHAR, $file_name)[0];
    }


    if (strpos($file_name, QUESTION_CHAR) !== false) {
        $file_name = explode(QUESTION_CHAR, $file_name)[0];
    }

    return $file_name;
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

    $original_file_name = parse_filename($url);
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
 * Функция преобразует json-строку сформированную MySQL функцией JSON_ARRAYAGG
 * в массив.
 *
 * @param  string  $json  - массив в виде формате json
 *
 * @return array - преобразованный массив
 */
function decode_json_array_agg(string $json): array
{
    return array_filter(
        json_decode($json),
        function ($value) {
            return $value;
        }
    );
}

/**
 * Функция производит получение данных из базы данных с использованием
 * подготовленного выражения. Функция возвращается результат запроса типа
 * mysqli_result, в случае его успешного выполения, либо null - в случае
 * неуспешного выполнения.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $sql  - подготовленное SQL выражение
 * @param  string  $types  - строка, содержащая один или более символов,
 * каждый из которых задаёт тип значения привязываемой переменной
 * @param  mixed  ...$variables  - переменные, привязываемые к подготовленному
 * выражению
 *
 * Ограничения: Количество переменных и длина строки types должны в точности
 * соответствовать количеству параметров в запросе
 *
 * @return mysqli_result | null - результат выполения запроса
 */
function execute_select_query(
    mysqli $db_connection,
    string $sql,
    string $types,
    ...$variables
) {
    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        $types,
        ...$variables
    );

    if (!mysqli_stmt_execute($statement)) {
        return null;
    }

    return mysqli_stmt_get_result($statement) ?: null;
}

/**
 * Функция производит измененение данных а базы данных с использованием
 * подготовленного выражения. Функция возвращается результат запроса в булевом
 * формате.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  string  $sql  - подготовленное SQL выражение
 * @param  string  $types  - строка, содержащая один или более символов,
 * каждый из которых задаёт тип значения привязываемой переменной
 * @param  mixed  ...$variables  - переменные, привязываемые к подготовленному
 * выражению
 *
 * Ограничения: Количество переменных и длина строки types должны в точности
 * соответствовать количеству параметров в запросе
 *
 * @return bool - результат выполения запроса
 */
function execute_non_select_query(
    mysqli $db_connection,
    string $sql,
    string $types,
    ...$variables
): bool {
    $statement = mysqli_prepare($db_connection, $sql);
    mysqli_stmt_bind_param(
        $statement,
        $types,
        ...$variables
    );

    return mysqli_stmt_execute($statement);
}

/**
 * Функция генерирует ссылку для перехода на страницу разговора.
 *
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  int  $conversation_id  - id разговора
 *
 * @return string - итоговый URL страницы для переключения на заданный разговор
 */
function get_conversation_url(
    string $basename,
    int $conversation_id
): string {
    $query_params = $_GET;
    $query_params[CONVERSATION_ID_QUERY] = $conversation_id;
    $query_string = http_build_query($query_params);

    return "/$basename?$query_string";
}

/**
 * Функция возвращает массив с данными карточек разговоров для страницы
 * сообщений пользователя.
 * данными карточки разговора представляют собой ассоциативный массив
 * переданному массиву разговоров дополненный полями url и active.
 *
 * @param  array  $conversations  - массив с данными разговорав
 * @param  string  $basename  - URL страницы без GET параметров
 * @param  int  $current_conversation_id  - id разговора
 *
 * @return array - массив c данными карточек разговоров
 */
function get_conversation_cards(
    array $conversations,
    string $basename,
    int $current_conversation_id
): array {
    array_walk(
        $conversations,
        function (&$conversation) use (
            $basename,
            $current_conversation_id
        ) {
            $conversation_id = $conversation['id'];
            $url = get_conversation_url($basename, $conversation_id);
            $active = $conversation_id === $current_conversation_id;

            $conversation['url'] = $url;
            $conversation['active'] = $active;
        }
    );

    return $conversations;
}

/**
 * Функция возвращает источник (origin) текущего URL, состоящегго из
 * протокола, хоста и порта.
 *
 * @return string - источник URL
 */
function getOrigin(): string
{
    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'];
    $protocol = $_SERVER['REQUEST_SCHEME'];

    return "$protocol://$host:$port";
}

/**
 * Функция проверяет соотвествие типа файла изображения загруженного через
 * форму. Валидными типами фото являются следущие MIME-типы: image/jpg,
 * image/jpeg, image/png, image/gif.
 *
 * @param  array  $file - данные файла
 *
 * @return bool - соотвествие типа файла
 */
function check_photo_file_type(array $file): bool
{
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $file['tmp_name']);

    return
        array_search($file_type, ALLOWED_PHOTO_FILE_TYPES) !== false;
}
