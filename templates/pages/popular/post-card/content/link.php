<?php

/**
 * Шаблон контента карточки публикации ссылки для страницы 'Популярное'
 *
 * @var string $string_content - строковый контент публикации
 */

$string_content = isset($string_content) ? strip_tags($string_content) : '';
?>

<div class="post-link__wrapper">
    <a class="post-link__external" href="http://<?= $string_content ?>"
       title="Перейти по ссылке">
        <div class="post-link__info-wrapper">
            <div class="post-link__icon-wrapper">
                <img src="https://www.google.com/s2/favicons?domain=<?= $string_content; ?>"
                     alt="Иконка">
            </div>
            <div class="post-link__info">
                <h3>Описание ссылки</h3>
            </div>
        </div>
        <span><?= $string_content ?></span>
    </a>
</div>