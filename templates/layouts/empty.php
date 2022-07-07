<?php
/**
 * Шаблон пустой страницы
 *
 * @var string | null $title - заголовок страницы
 * @var string | null $content - разметка контента страницы
 */

$title = $title ?? 'Ошибка';
$content = $content ?? "
    <h1>$title</h1>
    <p>Что-то пошло не так...</p>
";
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
    <?= $content ?>
</section>

</body>
</html>
