<?php

/**
 * Функция рендерит состояние некорректно заданного таба страницы профиля
 * пользователя.
 *
 * Ограничения:
 * 1. Функция не обрабатывает разметку секцию информации о пользователе и
 * секцию табов, т.е. принимает готовую разметку данных секцийи для шаблонов
 * profile/page.php
 * 2. Данные для шаблона страницы должны содержать все необходимеы данные для
 * шаблона profile/page.php, кроме основного контента страницы.
 *
 * @param  string  $user_content  - разметка секции информации о пользователе
 * @param  string  $tabs_content  - разметка секции табов
 * @param  array  $layout_data  - данные для шаблона страницы профиля
 * пользователя
 */
function render_profile_tab_error(
    string $user_content,
    string $tabs_content,
    array $layout_data
) {
    $tab_error_message = include_template(
        'common/message.php',
        [
            'title' => 'Ошибка',
            'content' => 'Выбранный таб не сущесвует',
            'link_description' => 'Сбросить выбранный таб',
            'link_url' => $layout_data['basename'],
        ]
    );

    $page_content = include_template(
        'pages/profile/page.php',
        [
            'user_content' => $user_content,
            'tabs_content' => $tabs_content,
            'main_content' => $tab_error_message,
        ]
    );

    $layout_data['content'] = $page_content;

    $layout_content = include_template('layouts/user.php', $layout_data);

    print($layout_content);
}

/**
 * Функция возвращает разметку основного контента для таба 'Посты'
 * страницы профиля пользователя в зависимости от переданного массива
 * публикаций.
 *
 * @param  array | null  $posts  - массив публикаций
 *
 * @return string - разметка контента таба
 */
function get_profile_posts_tab_content($posts): string
{
    if (is_null($posts)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить публикации',
            ]
        );
    }

    if (!count($posts)) {
        return include_template(
            'common/message.php',
            ['title' => 'Публикации отсутствуют']
        );
    }

    return include_template(
        'pages/profile/main/posts.php',
        ['posts' => $posts]
    );
}

/**
 * Функция возвращает разметку основного контента для таба 'Лайки'
 * страницы профиля пользователя в зависимости от переданного массива
 * с данными лайков.
 *
 * @param  array | null  $likes  - массив с данными лайков
 * @param  bool  $is_own_profile  - собственный профиль
 *
 * @return string - разметка контента таба
 */
function get_profile_likes_tab_content($likes, bool $is_own_profile): string
{
    if (is_null($likes)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить лайки',
            ]
        );
    }

    if (!count($likes)) {
        return include_template(
            'common/message.php',
            ['title' => 'Лайки отсутствуют']
        );
    }

    return include_template(
        "pages/profile/main/likes.php",
        ['likes' => $likes, 'is_own_profile' => $is_own_profile]
    );
}

/**
 * Функция возвращает разметку основного контента для таба 'Подписки'
 * страницы профиля пользователя в зависимости от переданного массива
 * с данными подписок.
 *
 * @param  array | null  $subscriptions  - массив с данными подписок
 *
 * @return string - разметка контента таба
 */
function get_profile_subscriptions_tab_content($subscriptions): string
{
    if (is_null($subscriptions)) {
        return include_template(
            'common/message.php',
            [
                'title' => 'Ошибка',
                'content' => 'Не удалось загрузить подписки',
            ]
        );
    }

    if (!count($subscriptions)) {
        return include_template(
            'common/message.php',
            ['title' => 'Подписки отсутствуют']
        );
    }

    return include_template(
        "pages/profile/main/subscriptions.php",
        ['subscriptions' => $subscriptions]
    );
}

/**
 * Функция добавляет к публикациям в массиве контент секции комментариев.
 * Секция комментариев имеет два режима - открытый и закрытый.
 * По умолчанию (в закрытом режиме) в секции комментариев располагается кнопка
 * показа комменатриев (перехода в открытый режим).
 * В открытом режиме показывается сокращенный список комментариев (не более 2
 * комментариев), ссылка показа полного списка (при количестве комментариев
 * более 2) и форма добавления комментариея.
 *
 * Ограничения:
 * Нажатие на кнопку показа комментариев к публикации приводит к закрытию
 * аналогичных секций к другим комментариям на странице, т.е. на странице
 * не может находиться более одной публикации с открытой секцией комментариев.
 *
 * @param  array  $posts - массив публикаций
 * @param  array  $comments_data - данные для отображения комментариев
 *
 * @return array - массив публикаций с контентом секции комментариев
 */
function add_comments_contents(array $posts, array $comments_data): array
{
    array_walk(
        $posts,
        function (&$post) use (
            $comments_data
        ) {
            list(
                'post_id' => $post_id,
                'form_data' => $form_data,
                'list_data' => $list_data,
                'basename' => $basename,
                'is_expanded' => $is_expanded
                ) = $comments_data;

            if ($post['id'] === intval($post_id)) {
                $post['comments_form_content'] = include_template(
                    'common/comments/form.php',
                    $form_data
                );

                if (is_null($list_data['comments'])) {
                    $post['comments_list_content'] =
                        include_template(
                            'common/message.php',
                            [
                                'title' => 'Ошибка',
                                'content' => 'Не удалось загрузить комментарии',
                                'comments' => true
                            ]
                        );

                    return;
                }

                if (!count($list_data['comments'])) {
                    $post['comments_list_content'] = include_template(
                        'common/comments/list.php',
                        $list_data
                    );

                    return;
                }

                $comments_count = $post['comments_count'];
                $is_expansion_required =
                    count($list_data['comments']) < $comments_count;
                $expand_comments_url = !$is_expanded && $is_expansion_required
                    ? get_expand_comments_url($basename) : null;

                $list_data['comments_count'] = $comments_count;
                $list_data['expand_comments_url'] = $expand_comments_url;

                $post['comments_list_content'] = include_template(
                    'common/comments/list.php',
                    $list_data
                );

                return;
            }

            $open_comments_url = get_open_comments_url($basename, $post['id']);
            $post['comments_list_content'] = include_template(
                'common/comments/show-button.php',
                ['url' => $open_comments_url]
            );
        }
    );

    return $posts;
}
