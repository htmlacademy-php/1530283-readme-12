<?php

require_once 'utils/constants.php';

/**
 * Функция обрабабатыват данные формы аутентификации.
 * Возвращает ассоциативный массив с данными формы, ошибками валидации
 * и данными пользователя (в случе успешной аутентификации).
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return array{
 *     form_data: array,
 *     errors: array,
 *     user: array | null
 * } - данные формы, данные ошибок валидации, данные пользователя
 */
function handle_login_form(mysqli $db_connection): array
{
    $form_data = [];

    $form_data['email'] =
        filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $form_data['password'] =
        filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $errors = get_login_form_data_errors($form_data);

    $user = !count($errors) ? get_user_by_email(
        $db_connection,
        $form_data['email']
    ) : null;

    $is_password_correct = isset($user['password_hash'])
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

    if ($user) {
        unset($user['password_hash']);
    }

    return [
        'form_data' => $form_data,
        'errors' => $errors,
        'user' => $user
    ];
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
