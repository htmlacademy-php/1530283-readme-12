<?php

/**
 * Шаблон контента публикации фото для страницы просмотра публикации
 *
 * @var string $string_content - строковый контент публикации
 */

$string_content = strip_tags($string_content);
?>

<div class="post-details__image-wrapper post-photo__image-wrapper">
    <img src="/<?= $string_content ?>" alt="Фото от пользователя" width="760"
         height="507">
</div>
