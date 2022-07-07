<?php

require_once 'utils/functions.php';

/**
 * Функция получает список типов контента публикация из базы данных.
 * В случае успешного запроса функция возвращается массив
 * типов контента в виде ассоциативных массивов.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 *
 * @return null | array<int, array{
 *     id: int,
 *     type: string,
 *     name: string
 * }>
 */
function get_content_types(mysqli $db_connection)
{
    $sql = "
        SELECT
            content_types.id,
            content_types.type,
            content_types.name
        FROM content_types
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функиция возвращает данные типа контента из базы данных.
 * В случае успешного запроса функция возвращает данные в виде
 * ассоциативного массива.
 * В случае неуспешного запроса возвращается null.
 *
 * @param  mysqli  $db_connection  - ресурс соединения с базой данных
 * @param  int  $content_type_id  - id типа контента
 *
 * @return null | array{
 *     id: int,
 *     type: string,
 *     name: string
 * } - данные типа контента
 */
function get_content_type(mysqli $db_connection, int $content_type_id)
{
    $sql = "
        SELECT
            content_types.id,
            content_types.type,
            content_types.name
        FROM content_types
        WHERE id = ?
    ";

    $result =
        execute_select_query($db_connection, $sql, 'i', $content_type_id);

    if (!$result) {
        return null;
    }

    $content_type = mysqli_fetch_assoc($result);

    return isset($content_type['id']) ? $content_type : null;
}
