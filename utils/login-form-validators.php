<?php

require_once 'utils/constants.php';

/**
 * Функция валидирует значение электронной почты формы авторизации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Корректность формата
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
function get_login_email_error(array $form_data)
{
    $email = $form_data['email'] ?? '';
    $length = mb_strlen($email);
    $error_title = 'Электронная почта';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_EMAIL_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_EMAIL_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_EMAIL_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный формат',
        ];
    }

    return null;
}

/**
 * Функция валидирует значение пароля формы авторизации и
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
function get_login_password_error(array $form_data)
{
    $password = $form_data['password'] ?? '';
    $length = strlen($password);
    $error_title = 'Пароль';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_PASSWORD_BYTES_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_PASSWORD_BYTES_LENGTH
                             . ' ' . 'байт',
        ];
    }

    return null;
}

/**
 * Функция возвращает ассоциативный массив ошибок валидации формы авторизации
 * Ключами массива являются значения полей формы, а значениями -
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

function get_login_form_data_errors(array $form_data): array
{
    $errors = [];

    foreach ($form_data as $field => $value) {
        $get_error = "get_login_${field}_error";

        if (is_callable($get_error)) {
            $error = $get_error($form_data);

            if ($error) {
                $errors[$field] = $error;
            }
        }
    }

    return $errors;
}
