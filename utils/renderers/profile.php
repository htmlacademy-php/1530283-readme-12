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
 * @param  string  $tabs_content - разметка секции табов
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
