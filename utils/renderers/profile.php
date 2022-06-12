<?php

/**
 * Функция рендерит состояние некорректно заданного таба страницы профиля
 * пользователя.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секцию информации о пользователе и
 * секцию табов, т.е. принимает готовую разметку данных секцийи для шаблонов
 * profile/page.php
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона profile/page.php, кроме основного контента страницы.
 *
 * @param  string  $user_content  - разметка секции информации о пользователе
 * @param  string  $tabs_content  - разметка секции табов
 * @param  array  $layout_data  - данные для шаблона страницы профиля
 * пользователя
 */
function render_profile_tab_error(
    string $user_content,
    string $tabs_content,
    array $layout_data
) {
    $tab_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Выбранный таб не сущесвует',
            'link_description' => 'Сбросить выбранный таб',
            'link_url' => $layout_data['basename'],
        ]
    );

    $page_content = include_template(
        'pages/profile/page.php',
        [
            'user_content' => $user_content,
            'tabs_content' => $tabs_content,
            'main_content' => $tab_error_message,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}

/**
 * Функция возвращает разметку основного контента для таба 'Посты'
 * страницы профиля пользователя в зависимости от переданного массива
 * публикаций.
 *
 * @param  array | null  $posts  - массив публикаций
 *
 * @return string - разметка контента таба
 */
function get_profile_posts_tab_content($posts): string
{
    if (is_null($posts)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить публикации',
            ]
        );
    }

    if (!count($posts)) {
        return include_template(
            'common/message.php',
            ['title' => 'Публикации отсутствуют']
        );
    }

    return include_template(
        'pages/profile/main/posts.php',
        ['posts' => $posts]
    );
}

/**
 * Функция возвращает разметку основного контента для таба 'Лайки'
 * страницы профиля пользователя в зависимости от переданного массива
 * с данными лайков.
 *
 * @param  array | null  $likes  - массив с данными лайков
 * @param  bool  $is_own_profile  - собственный профиль
 *
 * @return string - разметка контента таба
 */
function get_profile_likes_tab_content($likes, bool $is_own_profile): string
{
    if (is_null($likes)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить лайки',
            ]
        );
    }

    if (!count($likes)) {
        return include_template(
            'common/message.php',
            ['title' => 'Лайки отсутствуют']
        );
    }

    return include_template(
        "pages/profile/main/likes.php",
        ['likes' => $likes, 'is_own_profile' => $is_own_profile]
    );
}

/**
 * Функция возвращает разметку основного контента для таба 'Подписки'
 * страницы профиля пользователя в зависимости от переданного массива
 * с данными подписок.
 *
 * @param  array | null  $subscriptions  - массив с данными подписок
 *
 * @return string - разметка контента таба
 */
function get_profile_subscriptions_tab_content($subscriptions): string
{
    if (is_null($subscriptions)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить подписки',
            ]
        );
    }

    if (!count($subscriptions)) {
        return include_template(
            'common/message.php',
            ['title' => 'Подписки отсутствуют']
        );
    }

    return include_template(
        "pages/profile/main/subscriptions.php",
        ['subscriptions' => $subscriptions]
    );
}
