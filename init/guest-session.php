<?php

require_once 'init/db-connection.php';
require_once 'models/user.php';

/**
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

session_start();

$user_session = $_SESSION['user'] ?? null;
$is_user = $user_session && $user_session['id']
              && check_user($db_connection, $user_session['id']);

if ($is_user) {
    header('Location: index.php');

    exit();
}

$_SESSION = [];
