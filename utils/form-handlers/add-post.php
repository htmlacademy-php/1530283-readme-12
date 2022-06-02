<?php

require_once 'utils/constants.php';
require_once 'utils/helpers.php';

/**
 * Функция обрабабатыват данные формы создания публикации.
 * Возвращает ассоциативный массив с данными формы и ошибками валидации.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return array{
 *     form_data: array,
 *     errors: array,
 * } - данные формы и данные ошибок валидации
 */
function handle_add_post_form(mysqli $db_connection): array {
    $form_data= [];

    $with_file = isset($_FILES['photo-file'])
                 && $_FILES['photo-file']['error'] !== UPLOAD_ERR_NO_FILE;

    $form_data['content_type_id'] = $_POST['content-type-id'] ?? '';
    $form_data['title'] = $_POST['title'] ?? '';
    $form_data['text_content'] = $_POST['text-content'] ?? '';
    $form_data['string_content'] =
        !$with_file ? $_POST['string-content'] ?? '' : '';
    $form_data['tags'] =
        $_POST['tags'] ? trim(
            preg_replace('/\s+/', TEXT_SEPARATOR, mb_strtolower($_POST['tags']))
        ) : '';
    $form_data['photo_file'] =
        $with_file ? $_FILES['photo-file'] : null;

    $content_type_data = $form_data['content_type_id'] ? get_content_type(
        $db_connection,
        $form_data['content_type_id']
    ) : null;
    $content_type = $content_type_data && $content_type_data['type']
        ? $content_type_data['type'] : null;

    $errors = $content_type
        ? get_post_form_data_errors($form_data, $content_type)
        : [
            [
                'title' => 'Тип контента',
                'description' => 'Некорректный тип'
            ]
        ];

    if (count($errors)) {
        if ($with_file && !$errors['photo_file']) {
            $errors['photo_file'] = [
                'title' => 'Файл фото',
                'description' => 'Загрузите файл еще раз'
            ];
        }
    }

    $is_photo_content_type = $content_type === 'photo';
    $photo_url = '';

    if (!count($errors) && $is_photo_content_type) {
        $photo_url =
            $with_file
                ? save_file($form_data['photo_file'])
                : download_file($form_data['string_content']);

        if (!$photo_url) {
            if ($with_file) {
                $errors['photo_file'] = [
                    'title' => 'Файл фото',
                    'description' => 'Не удалось загрузить файл'
                ];
            } else {
                $errors['string_content'] = [
                    'title' => 'Ссылка из интернета',
                    'description' => 'Не удалось загрузить файл по ссылке'
                ];
            }
        }
    }

    if (!count($errors)) {
        if ($is_photo_content_type) {
            $form_data['string_content'] = $photo_url;
        }
    }

    return [
        'form_data' => $form_data,
        'errors' => $errors,
    ];
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
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
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
            'description' => 'Длина поля не должна превышать '
                             . MAX_TITLE_LENGTH
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
 * Функция валидирует загруженный файл фото для публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Корректный формат файла
 * 2. Максимальный размер файла
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return null | array{
 *     title: string,
 *     description: string,
 * } - Ошибка валидации (при наличии)
 */
function get_post_photo_file_error(array $form_data)
{
    if (!$form_data['photo_file']) {
        return null;
    }

    $error_title = 'Файл фото';
    $file = $form_data['photo_file'];

    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $file['tmp_name']);
    $is_valid_type =
        array_search($file_type, ALLOWED_PHOTO_FILE_TYPES) !== false;

    if (!$is_valid_type) {
        return [
            'title' => $error_title,
            'description' => 'Некорретный тип файла',
        ];
    }

    if ($file['size'] > MAX_PHOTO_FILE_SIZE) {
        return [
            'title' => $error_title,
            'description' => 'Превышен допустимый размер файла '
                             . convert_to_megabytes(MAX_PHOTO_FILE_SIZE)
                             . 'Мб',
        ];
    }

    return null;
}

/**
 * Функция валидирует значение фото ссылки формы создания публикации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Корректность URL
 * 3. Доступность ссылки
 * 4. Корректный формат файла по ссылке
 * 5. Максимальный размера файла по ссылке
 *
 * Ограничения:
 * 1. Функция возвращает только первую ошибку валидации.
 * 2. В случае передачи загрузки через форму файла фото,
 * валидация ссылки не проводится.
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
    if ($form_data['photo_file']) {
        return null;
    }

    $string_content = $form_data['string_content'] ?? '';
    $length = mb_strlen($string_content);
    $error_title = 'Ссылка из интернета';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Введите ссылку, либо загрузите файл',
        ];
    }

    if (!filter_var($string_content, FILTER_VALIDATE_URL)) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный URL',
        ];
    }

    if (!check_url($string_content)) {
        return [
            'title' => $error_title,
            'description' => 'Ссылка недоступна',
        ];
    }

    if (!check_photo_url($string_content)) {
        return [
            'title' => $error_title,
            'description' => 'Некорретный тип файла',
        ];
    }

    $file_size = get_url_size($string_content);

    if ($file_size > MAX_PHOTO_FILE_SIZE) {
        return [
            'title' => $error_title,
            'description' => 'Превышен допустимый размер файла '
                             . convert_to_megabytes(MAX_PHOTO_FILE_SIZE)
                             . 'Мб',
        ];
    }

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

    if (!check_youtube_url($string_content)) {
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
