<?php
/**
 * Функция принимает ресурс соединения с базой данный
 * и возвращает массив с типами контента.
 * @param mysqli $db_connection - ресурс соединения с базой данных
 * @return false | array<int, array{
 *     id: int,
 *     icon: string,
 *     name: string
 * }>
 */
function get_content_types(mysqli $db_connection) {
    $sql = "
        SELECT
            content_types.id,
            content_types.icon,
            content_types.name
        FROM content_types
    ";

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
