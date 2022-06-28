<?php

require_once 'utils/helpers.php';

/**
 * Функция рендерит страницу 'Сообщения' в зависимости от переданного массива
 * сообщений.
 *
 * Ограничения:
 * Функция не обрабатывает разметку секции разговоров и секции формы
 * добавления сообщений, т.е. принимает готовую разметку данных секций
 * для шаблона messages/page.php.
 *
 * @param  null | array $messages - массив сообщений
 * @param string $conversations_content - разметка секции разговоров
 * @param string $form_content - разметка секции формы добавления сообщения
 * @param  array  $layout_data - прочие данные шаблона страницы 'Сообщения'
 */
function render_messages_page(
    $messages,
    string $conversations_content,
    string $form_content,
    array $layout_data
) {
    $page_data = (function () use ($messages, $form_content) {
        if (is_null($messages)) {
            $error_content = include_template('common/message.php', [
                'content' => 'Не удалось загрузить сообщения',
            ]);

            return [
                'messages_content' => $error_content,
                'form_content' => '',
            ];
        }

        if (!count($messages)) {
            return [
                'messages_content' => '',
                'form_content' => $form_content,
            ];
        }

        $messages_content = include_template(
            'pages/messages/messages-list.php',
            ['messages' => $messages]
        );

        return [
            'messages_content' => $messages_content,
            'form_content' => $form_content,
        ];
    })();

    $page_data['conversations_content'] = $conversations_content;
    $page_content = include_template('pages/messages/page.php', $page_data);

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}
