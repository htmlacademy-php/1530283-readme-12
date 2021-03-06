<?php

/**
 * Общий шаблон контента карточки публикации ссылки
 *
 * @var string $title - заголовок поста
 * @var string $string_content - строковый контент публикации
 */

$title = strip_tags($title);
$string_content = strip_tags($string_content);
?>

<div class="post__main">
    <div class="post-link__wrapper">
        <a class="post-link__external" href="<?= $string_content ?>"
           title="Перейти по ссылке">
            <div class="post-link__info-wrapper">
                <div class="post-link__icon-wrapper">
                    <img src="https://www.google.com/s2/favicons?domain=<?= $string_content ?>"
                         alt="Иконка">
                </div>
                <div class="post-link__info">
                    <h3><?= $title ?></h3>
                </div>
            </div>
        </a>
    </div>
</div>
