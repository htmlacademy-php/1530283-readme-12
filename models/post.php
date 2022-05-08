<?php
/**
 * Функция принимает ресурс соединения с базой данный
 * и ассоциативныый массив с параметрами запроса
 * и возвращает массив с публикациями.
 * Параметры запроса позволяют задавать фильтрацию и сортировку публикаций.
 * @param mysqli $db_connection - ресурс соединения с базой данных
 * @param array[
 *     'sort' => 'views_count' | null,
 *     'filter' => 'text' | 'link' | 'quote' | 'video' | 'photo' | null] $config - параметры запроса
 * @return false | array<int, array{
 *     id: int,
 *     title: string,
 *     string_content: string,
 *     text_content: string,
 *     created_at: string,
 *     views_count: int,
 *     author_login: string,
 *     author_avatar: string,
 *     content_type: string,
 * }>
 */
function get_posts(mysqli $db_connection, $config = []) {
    $sort = $config['sort'];

    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.string_content,
            posts.text_content,
            posts.created_at,
            posts.views_count,
            users.login AS author_login,
            users.avatar_url AS author_avatar,
            content_types.icon AS content_type
        FROM posts
            JOIN users ON posts.author_id = users.id
            JOIN content_types ON posts.content_type_id = content_types.id
    ";

    if ($sort === 'views_count') {
        $sql .= " ORDER BY posts.views_count";
    }

    $result = mysqli_query($db_connection, $sql);

    if (!$result) {
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

