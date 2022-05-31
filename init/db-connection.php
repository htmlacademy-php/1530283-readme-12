<?php

require_once 'config/db.php';
require_once 'utils/helpers.php';
require_once 'utils/renderers/common.php';

list(
    'hostname' => $localhost,
    'username' => $username,
    'password' => $password,
    'database' => $database,
    'charset' => $charset
    ) = DB_CONFIG;

$db_connection = mysqli_connect($localhost, $username, $password, $database);

if (!$db_connection || mysqli_error($db_connection)) {
    http_response_code(SERVER_ERROR_STATUS);
    ob_end_clean();
    render_message_page(
        ['content' => 'Произошла внутренняя ошибка сервера'],
        'empty'
    );
    exit();
}

mysqli_set_charset($db_connection, $charset);
mysqli_options($db_connection, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
