<?php

/**
 * Функция обрабабатыват данные формы создания комментария к публикации.
 * Возвращает ассоциативный массив с данными формы и ошибками валидации.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return array{
 *     form_data: array,
 *     errors: array,
 * } - данные формы и данные ошибок валидации
 */
function handle_add_comment_form(mysqli $db_connection): array
{
    $form_data = [];

    $form_data['content'] =
        trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING) ?? '');
    $form_data['post_id'] =
        filter_input(INPUT_POST, 'post-id', FILTER_SANITIZE_NUMBER_INT);
    $form_data['post_author_id'] =
        filter_input(INPUT_POST, 'post-author-id', FILTER_SANITIZE_NUMBER_INT);

    $errors = get_comment_form_data_errors($form_data);

    if (!check_post($db_connection, $form_data['post_id'])) {
        $errors['post_id'] = [
            'title' => 'ID публикации',
            'description' => 'Публикации с таким ID не существует',
        ];
    }

    if (!check_user($db_connection, $form_data['post_author_id'])) {
        $errors['author_id'] = [
            'title' => 'ID автора публикации',
            'description' => 'Пользователя с таким ID не существует',
        ];
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
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return array<int, array{
 *   title: string,
 *   description: string
 * }> - массив ошибок валидации
 */
function get_comment_form_data_errors(array $form_data): array
{
    $errors = [];

    foreach ($form_data as $field => $value) {
        $get_error = "get_post_${field}_error";

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
 * Функция валидирует значение контента формы создания комментария и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Минимальная длина
 * 3. Максимальная длина
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
function get_post_content_error(array $form_data)
{
    $content = $form_data['content'] ?? '';
    $length = mb_strlen($content);
    $error_title = 'Текст комментария';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length < MIN_TEXT_CONTENT_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна быть меньше '
                             . MIN_TEXT_CONTENT_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MIN_TEXT_CONTENT_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
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
