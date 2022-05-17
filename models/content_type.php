<?php

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
 *     icon: string,
 *     name: string
 * }>
 */
function get_content_types(mysqli $db_connection)
{
    $sql = "
        SELECT
            content_types.id,
            content_types.icon,
            content_types.name
        FROM content_types
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
