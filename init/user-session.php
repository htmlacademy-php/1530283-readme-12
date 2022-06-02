<?php

session_start();

$user_session = $_SESSION['user'];

if (!$user_session) {
    header('Location: index.php');

    exit();
}
