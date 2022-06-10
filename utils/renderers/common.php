<?php

/**
 * Функция рендерит страницу со служебным сообщением в качестве основного
 * контента.
 *
 * Ограничения:
 * 1. Данные для служебного сообщения должны соответствовать
 * требованиям шаблона common/message.php.
 * 2. Данные для шаблона страницы должны соотвествовать заданному типу шаблона.
 *
 * @param  array  $message_data - данныя для шаблона сообщения
 * @param  string  $layout_type - типа шаблона страницы
 * @param  array  $layout_data - данные для шаблона страницы
 */
function render_message_page(
    array $message_data,
    string $layout_type = 'empty',
    array $layout_data = []
) {
    $page_content = include_template(
        'common/message.php',
        $message_data
    );

    $layout_data['content'] = $page_content;

    $layout_content =
        include_template("layouts/$layout_type.php", $layout_data);

    print($layout_content);
}
