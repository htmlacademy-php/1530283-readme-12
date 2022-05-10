<?php

if ( ! isset($title)) {
    $title = 'Ошибка';
}

if ( ! isset($content)) {
    $content = 'Что-то пошло не так...';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body class="page">

<header class="header" style="min-height:10vh"></header>

<section class="page__main"
         style="min-height:90vh;padding-top:10vh;text-align:center">
    <div class="container">
        <h1><?= $title ?></h1>
        <p><?= $content ?></p>
    </div>
</section>

</body>
</html>
