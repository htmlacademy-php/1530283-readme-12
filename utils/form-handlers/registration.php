<?php

require_once 'utils/constants.php';
require_once 'utils/functions.php';

/**
 * Функция обрабабатыват данные формы регистрации.
 * Возвращает ассоциативный массив с данными формы и ошибками валидации.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return array{
 *     form_data: array,
 *     errors: array,
 * } - данные формы и данные ошибок валидации
 */
function handle_registration_form(mysqli $db_connection): array
{
    $with_file = isset($_FILES['photo-file'])
                 && $_FILES['photo-file']['error'] !== UPLOAD_ERR_NO_FILE;

    $form_data['email'] =
        filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $form_data['login'] =
        filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $form_data['password'] =
        filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $form_data['password_repeat'] =
        filter_input(INPUT_POST, 'password-repeat', FILTER_SANITIZE_STRING);
    $form_data['avatar_file'] =
        $with_file ? $_FILES['photo-file'] : null;
    $form_data['avatar_url'] = null;

    $errors = get_registration_form_data_errors($form_data);

    if (count($errors)) {
        $avatar_file_error = $errors['avatar_file'] ?? null;
        if ($with_file && !$avatar_file_error) {
            $errors['avatar_file'] = [
                'title' => 'Файл фото',
                'description' => 'Загрузите файл еще раз'
            ];
        }
    }

    $photo_url = '';

    if ($with_file && !count($errors)) {
        $photo_url = save_file($form_data['avatar_file']);

        if (!$photo_url) {
            $errors['avatar_file'] = [
                'title' => 'Файл фото',
                'description' => 'Не удалось загрузить файл'
            ];
        }
    }

    $email_error = $errors['email'] ?? null;
    if (!$email_error) {
        $is_email_busy = get_user_by_email($db_connection, $form_data['email']);

        if ($is_email_busy) {
            $errors['email'] = [
                'title' => 'Электронная почта',
                'description' => 'Пользователь с такой электронной почтой уже зарегистрирован'
            ];
        }
    }

    if (!count($errors) && $with_file) {
        $form_data['avatar_url'] = $photo_url;
    }

    return [
        'form_data' => $form_data,
        'errors' => $errors,
    ];
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
    $length = strlen($password_repeat);
    $error_title = 'Повтор пароля';

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
    $avatar_file = $form_data['avatar_file'] ?? null;
    if (!$avatar_file) {
        return null;
    }

    $error_title = 'Файл фото';

    $is_valid_type = check_photo_file_type($avatar_file);

    if (!$is_valid_type) {
        return [
            'title' => $error_title,
            'description' => 'Некорректный тип файла',
        ];
    }

    if ($avatar_file['size'] > MAX_PHOTO_FILE_SIZE) {
        return [
            'title' => $error_title,
            'description' => 'Превышен допустимый размер файла '
                             . convert_to_megabytes(MAX_PHOTO_FILE_SIZE)
                             . 'Мб',
        ];
    }

    return null;
}
