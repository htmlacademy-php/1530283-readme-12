<?php

require_once 'init/user-session.php';
require_once 'init/db-connection.php';
require_once 'models/like.php';

/**
 * @var array $user_session - сессия пользователя
 * @var mysqli $db_connection - ресурс соединения с базой данных
 */

$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

toggle_like($db_connection, $user_session['id'], $post_id);

header('Location: ' . $_SERVER['HTTP_REFERER']. "#post-$post_id");
