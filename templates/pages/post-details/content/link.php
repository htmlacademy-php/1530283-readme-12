<?php

/**
 * Шаблон контента публикации ссылки для страницы просмотра публикации
 *
 * @var string $title - заголовок поста
 * @var string $string_content - строковый контент публикации
 */

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
                    <h3><?= strip_tags($title) ?></h3>
                </div>
            </div>
        </a>
    </div>
</div>
