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
        list(
            'email' => $observable_email,
            'login' => $observable_login
            ) = $observable;

        list(
            'id' => $subscriber_id,
            'login' => $subscriber_login
            ) = $subscriber;

        $observable_login = htmlspecialchars($observable_login);
        $observable_email = htmlspecialchars($observable_email);
        $subscriber_login = htmlspecialchars($subscriber_login);

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
        list(
            'email' => $subscriber_email,
            'login' => $subscriber_login
            ) = $subscriber;

        list(
            'id' => $post_id,
            'title' => $post_title,
            'author' => $post_author
            ) = $post;

        list(
            'id' => $post_author_id,
            'login' => $post_author_login
            ) = $post_author;

        $subscriber_email = htmlspecialchars($subscriber_email);
        $subscriber_login = htmlspecialchars($subscriber_login);
        $post_author_login = htmlspecialchars($post_author_login);
        $post_title = htmlspecialchars($post_title);

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
