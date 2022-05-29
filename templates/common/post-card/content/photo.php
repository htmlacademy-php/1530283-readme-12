<?php

/**
 * Общий шаблон контента карточки публикации фото
 *
 * @var string $string_content - строковый контент публикации
 */

$string_content = isset($string_content) ? strip_tags($string_content) : '';
?>

<div class="post-photo__image-wrapper">
    <img src="/<?= $string_content ?>" alt="Фото от пользователя" width="760"
         height="396">
</div>
