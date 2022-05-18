<?php

require_once 'utils/constants.php';

/**
 * Функция валидирует значение электронной почты формы регистрации и
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
function get_registration_email_error(array $form_data)
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
 * Функция валидирует значение логина формы регистрации и
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
function get_registration_login_error(array $form_data)
{
    $login = $form_data['login'] ?? '';
    $length = mb_strlen($login);
    $error_title = 'Логин';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    if ($length > MAX_LOGIN_LENGTH) {
        return [
            'title' => $error_title,
            'description' => 'Длина поля не должна превышать '
                             . MAX_LOGIN_LENGTH
                             . ' ' . get_noun_plural_form(
                                 MAX_LOGIN_LENGTH,
                                 'символ',
                                 'символа',
                                 'символов'
                             ),
        ];
    }

    return null;
}

/**
 * Функция валидирует значение пароля формы регистрации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Совпадение с полем повтора пароля
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
function get_registration_password_error(array $form_data)
{
    $password = $form_data['password'] ?? '';
    $password_repeat = $form_data['password_repeat'] ?? '';
    $length = mb_strlen($password);
    $error_title = 'Пароль';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    // todo: check max length

    if ($password_repeat && $password_repeat !== $password) {
        return [
            'title' => $error_title,
            'description' => 'Значение не совпадает с повтором пароля',
        ];
    }

    return null;
}

/**
 * Функция валидирует значение повтора пароля формы регистрации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Ненулевая длина
 * 2. Максимальная длина
 * 3. Совпадение с полем пароля
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
function get_registration_password_repeat_error(array $form_data)
{
    $password = $form_data['password'] ?? '';
    $password_repeat = $form_data['password_repeat'] ?? '';
    $length = mb_strlen($password_repeat);
    $error_title = 'Повтор пароля';

    if (!$length) {
        return [
            'title' => $error_title,
            'description' => 'Поле обязательно к заполнению',
        ];
    }

    // todo: check max length

    if ($password && $password_repeat !== $password) {
        return [
            'title' => $error_title,
            'description' => 'Значение не совпадает с паролем',
        ];
    }

    return null;
}

/**
 * Функция валидирует загруженный файл аватара формы регистрации и
 * вовзвращает ассоциативный массив ошибки валидации, содержащий название и
 * описание ошибки. Если значение валидно, функция возвращает null.
 * Валидируемые критерии:
 * 1. Формат файла
 * 2. Максимальный размера файла
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
function get_registration_avatar_file_error(array $form_data)
{
    if (!$form_data['avatar_file']) {
        return null;
    }

    $error_title = 'Файл фото';
    $file = $form_data['avatar_file'];

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
 * Функция возвращает ассоциативный массив ошибок валидации формы регистрации
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

function get_registration_form_data_errors(array $form_data): array
{
    $errors = [];

    foreach ($form_data as $field => $value) {
        $get_error = "get_registration_${field}_error";

        if (is_callable($get_error)) {
            $error = $get_error($form_data);

            if ($error) {
                $errors[$field] = $error;
            }
        }
    }

    return $errors;
}
