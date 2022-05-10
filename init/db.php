<?php
require_once 'config/db.php';

if (!isset($db) or !is_array($db)) {
    $db_connection = false;
    return;
}

list(
    'hostname' => $localhost,
    'username' => $username,
    'password' => $password,
    'database' => $database,
    'charset' => $charset
    ) = $db;

$db_connection = mysqli_connect($localhost, $username, $password, $database);

if ($db_connection) {
    mysqli_set_charset($db_connection, $charset);
}
