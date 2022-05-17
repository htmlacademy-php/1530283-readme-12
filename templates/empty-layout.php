<?php

/**
 * Шаблон страницы служебнго сообщения
 *
 * @var string $title - заголовок страницы ('Ошибка' - по умолчанию)
 * @var string $content - текст сообщения страницы ('Что-то пошло не так...'
 * - по умолчанию)
 */

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
    <style>
        .header {
            min-height: 10vh;
        }

        .page__main {
            flex-grow: 1;
        }

        .page__main--empty {
            text-align: center;
        }
    </style>
</head>
<body class="page">

<header class="header"></header>

<section class="page__main page__main--empty">
    <div class="container">
        <h1><?= $title ?></h1>
        <p><?= $content ?></p>
    </div>
</section>

</body>
</html>
