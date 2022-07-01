<?php

require_once 'init/db-connection.php';
require_once 'models/message.php';
require_once 'models/user.php';

/**
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

session_start();

$user_session = $_SESSION['user'];
$is_no_user = !$user_session || !$user_session['id']
              || !check_user($db_connection, $user_session['id']);

if ($is_no_user) {
    header('Location: index.php');

    exit();
}

$user_session['unread_messages_count'] =
    get_unread_messages_count($db_connection, $user_session['id']);
