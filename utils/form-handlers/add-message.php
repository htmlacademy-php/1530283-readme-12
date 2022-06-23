<?php

/**
 * Функция обрабабатыват данные формы добавления сообщения к разговору.
 * Возвращает ассоциативный массив с данными формы и ошибками валидации.
 *
 * @return array{
 *     form_data: array,
 *     errors: array,
 * } - данные формы и данные ошибок валидации
 */
function handle_add_message_form(): array
{
    $form_data = [];

    $form_data['content'] =
        trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING) ?? '');
    $form_data['conversation_id'] =
        filter_input(INPUT_POST, 'conversation-id', FILTER_SANITIZE_NUMBER_INT);

    $errors = get_message_form_data_errors($form_data);

    return [
        'form_data' => $form_data,
        'errors' => $errors,
    ];
}

/**
 * Функция возвращает ассоциативный массив ошибок валидации формы добавления
 * сообщения к разговору. Ключами массива являются значения полей формы,
 * а значениями - ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. В случае отсутствия ошибок возвращается пустой массив.
 *
 * @param  array  $form_data  - ассоциативный массив полей формы и их значений
 *
 * @return array<int, array{
 *   title: string,
 *   description: string
 * }> - массив ошибок валидации
 */
function get_message_form_data_errors(array $form_data): array
{
    $errors = [];

    foreach ($form_data as $field => $value) {
        $get_error = "get_message_${field}_error";

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
 * Функция валидирует значение контента формы добавления сообщения к разговору
 * и вовзвращает ассоциативный массив ошибки валидации, содержащий название и
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
function get_message_content_error(array $form_data)
{
    $content = $form_data['content'] ?? '';
    $length = mb_strlen($content);
    $error_title = 'Текст сообщения';

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
