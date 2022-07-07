<?php

require_once 'init/common.php';
require_once 'utils/constants.php';
require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'utils/helpers.php';
require_once 'models/conversation.php';
require_once 'models/message.php';
require_once 'models/user.php';
require_once 'utils/form-handlers/add-message.php';
require_once 'utils/renderers/messages.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$basename = basename(__FILE__);

$layout_data = [
    'title' => 'Сообщения',
    'user' => $user_session,
    'page_modifier' => 'messages',
    'basename' => $basename,
];

$form_data = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_author_id = $user_session['id'];
    list(
        'form_data' => $form_data,
        'errors' => $errors
        ) = handle_add_message_form();

    if (!count($errors)) {
        $created_message_id =
            create_message($db_connection, $message_author_id, $form_data);

        if (!$created_message_id) {
            http_response_code(SERVER_ERROR_STATUS);
            render_message_page(
                ['content' => 'Произошла внутренняя ошибка сервера'],
                'user',
                $layout_data
            );
            exit();
        }

        $form_data = [];
    }
}

$interlocutor_id =
    filter_input(INPUT_GET, USER_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);
$current_conversation_id =
    filter_input(INPUT_GET, CONVERSATION_ID_QUERY, FILTER_SANITIZE_NUMBER_INT);

if ($interlocutor_id) {
    if (!check_user($db_connection, $interlocutor_id)) {
        http_response_code(BAD_REQUEST_STATUS);
        render_message_page(
            ['content' => 'Данные пользователь не существует'],
            'user',
            $layout_data
        );
        exit();
    };

    $current_conversation_id = create_conversation(
        $db_connection,
        $user_session['id'],
        $interlocutor_id
    );

    if (!$current_conversation_id) {
        http_response_code(SERVER_ERROR_STATUS);
        render_message_page(
            ['content' => 'Произошла внутренняя ошибка сервера'],
            'user',
            $layout_data
        );
        exit();
    }

    header("Location: messages.php?conversation-id=$current_conversation_id");
    exit();
}

$conversations = get_conversations(
    $db_connection,
    $user_session['id'],
    $current_conversation_id
);

if (is_null($conversations)) {
    http_response_code(SERVER_ERROR_STATUS);
    render_message_page(
        ['content' => 'Произошла внутренняя ошибка сервера'],
        'user',
        $layout_data
    );
    exit();
}

if (!count($conversations)) {
    http_response_code(NOT_FOUND_STATUS);
    render_message_page(
        [
            'title' => 'У Вас пока нет сообщений',
            'content' => 'Вы можете подписаться на любого автора и начать с ним разговор'
        ],
        'user',
        $layout_data
    );
    exit();
}

if (!$current_conversation_id) {
    $current_conversation_id = $conversations[0]['id'];
}

$conversation_cards =
    get_conversation_cards($conversations, $basename, $current_conversation_id);

$conversations_content = include_template(
    'pages/messages/conversations-list.php',
    ['conversations' => $conversation_cards]
);

$messages =
    get_messages($db_connection, $user_session['id'], $current_conversation_id);

$form_data['conversation_id'] = $current_conversation_id;

$form_content = include_template(
    'pages/messages/message-form.php',
    [
        'user' => $user_session,
        'form_data' => $form_data,
        'errors' => $errors,
    ]
);

if (is_null($messages)) {
    http_response_code(SERVER_ERROR_STATUS);
} else {
    read_conversation_messages(
        $db_connection,
        $user_session['id'],
        $current_conversation_id
    );
}

render_messages_page(
    $messages,
    $conversations_content,
    $form_content,
    $layout_data
);
