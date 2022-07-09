<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'utils/functions.php';

/**
 * Функция отправляет почтовое уведомление пользователю о новом подписчике.
 * Функция возвращает результат отправки уведомления в булевом формате.
 *
 * Ограничения:
 * Функция принимает сконфигурированный экзмепляр PHPMailer.
 *
 * @param  PHPMailer  $mail  - экземпляр PHPMailer
 * @param  array  $observable  - данные пользователя
 * @param  array  $subscriber  - данные подписчика
 *
 * @return bool - результат увеомления
 */
function notify_about_new_subscriber(
    PHPMailer $mail,
    array $observable,
    array $subscriber
): bool {
    try {
        $observable_login =
            isset($observable['login']) ? htmlspecialchars($observable['login'])
                : null;
        $observable_email =
            isset($observable['email']) ? htmlspecialchars($observable['email'])
                : null;
        $subscriber_id = $subscriber['id'] ?? null;
        $subscriber_login =
            isset($subscriber['login']) ? htmlspecialchars(
                $subscriber['login']
            ) : null;

        $mail->addAddress($observable_email, $observable_login);

        $origin = getOrigin();
        $subscriber_url = "$origin/profile.php?user-id=$subscriber_id";

        $mail->Subject = "У вас новый подписчик";

        $mail->Body =
            "Здравствуйте, $observable_login.
            На вас подписался новый пользователь $subscriber_login.
            Вот ссылка на его профиль:
            <a href=\"$subscriber_url\" target=\"_blank\">$subscriber_url</a>";

        $mail->AltBody = strip_tags(
            "Здравствуйте, $observable_login.
            На вас подписался новый пользователь $subscriber_login.
            Вот ссылка на его профиль: $subscriber_url"
        );

        $result = $mail->send();
        $mail->clearAddresses();

        return $result;
    } catch (Exception $error) {
        return false;
    }
}

/**
 * Функция уведомляет подписчика о создании автором новой публикации.
 * Функция возвращает результат отправки уведомления в булевом формате.
 *
 * Ограничения:
 * Функция принимает сконфигурированный экзмепляр PHPMailer.
 *
 * @param  PHPMailer  $mail  - экземпляр PHPMailer
 * @param  array  $subscriber  - данные подписчика
 * @param  array  $post  - данные публикации
 *
 * @return bool - результат увеомления
 */
function notify_about_new_post(
    PHPMailer $mail,
    array $subscriber,
    array $post
): bool {
    try {
        $subscriber_email =
            isset($subscriber['email']) ? htmlspecialchars($subscriber['email'])
                : null;
        $subscriber_login = isset($subscriber['login']) ? htmlspecialchars(
            $subscriber['login']
        ) : null;
        $post_id = $post['id'] ?? null;
        $post_title =
            isset($post['title']) ? htmlspecialchars($post['title']) : null;
        $post_author = $post['author'] ?? null;
        $post_author_id = $post_author['id'] ?? null;
        $post_author_login = isset($post_author['login'])
            ? htmlspecialchars($post_author['login']) : null;

        $mail->addAddress($subscriber_email, $subscriber_login);

        $origin = getOrigin();
        $post_author_url =
            "$origin/profile.php?user-id=$post_author_id#post-$post_id";

        $mail->Subject =
            strip_tags("Новая публикация от пользователя $post_author_login");

        $mail->Body =
            "Здравствуйте, $subscriber_login.
            Пользователь $post_author_login только что опубликовал
            новую запись „{$post_title}“. 
            Посмотрите её на странице пользователя:
            <a href=\"$post_author_url\" target=\"_blank\">$post_author_url</a>
        ";

        $mail->AltBody = strip_tags(
            "Здравствуйте, $subscriber_login.
            Пользователь $post_author_login только что опубликовал
            новую запись „{$post_title}“. 
            Посмотрите её на странице пользователя:
            <a href=\"$post_author_url\" target=\"_blank\">$post_author_url</a>
        "
        );

        $result = $mail->send();
        $mail->clearAddresses();

        return $result;
    } catch (Exception $error) {
        return false;
    }
}
