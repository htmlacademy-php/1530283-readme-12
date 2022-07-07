<?php

require_once 'init/common.php';

session_start();

$_SESSION = [];

header('Location: index.php');

exit();
