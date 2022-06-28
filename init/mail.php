<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'config/mail.php';
require_once 'vendor/autoload.php';

list(
    'host' => $host,
    'port' => $port,
    'username' => $username,
    'password' => $password,
    'username_alias' => $username_alias,
    'charset' => $charset
    ) = MAIL_CONFIG;

$mail = new PHPMailer();

try {
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->CharSet = $charset;
    $mail->setFrom($username, $username_alias);
    $mail->isHTML(true);
} catch (Exception $error) {}
