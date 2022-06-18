<?php

/**
 * Шаблон контента карточки публикации фото для страницы 'Популярное'
 *
 * @var string $string_content - строковый контент публикации
 */

?>

<div class="post-photo__image-wrapper">
    <img src="/<?= strip_tags($string_content) ?>" alt="Фото от пользователя"
         width="360"
         height="240">
</div>
