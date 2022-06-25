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
 * @param  PHPMailer  $mail - экземпляр PHPMailer
 * @param  array  $observable - данные пользователя
 * @param  array  $subscriber - данные подписчика
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

        $mail->addAddress($observable_email, $observable_login);

        $origin = getOrigin();
        $subscriber_url = "$origin/profile.php?user-id=$subscriber_id";

        $mail->Subject = "У вас новый подписчик";
        $mail->Body = "Здравствуйте, $observable_login.
            На вас подписался новый пользователь $subscriber_login.
            Вот ссылка на его профиль:
            <a href=\"$subscriber_url\" target=\"_blank\">$subscriber_url</a>";

        $mail->AltBody = "Здравствуйте, $observable_login.
            На вас подписался новый пользователь $subscriber_login.
            Вот ссылка на его профиль: $subscriber_url";

        return $mail->send();
    } catch (Exception $error) {
        return false;
    }
}
