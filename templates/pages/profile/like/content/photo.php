<?php
/**
 * Шаблон превью публицакиции-фото для карточки лайка
 * страницы профиля пользователя
 *
 * @var string $string_content - строковый контент публикации
 */

?>

<div class="post-mini__image-wrapper">
    <img class="post-mini__image"
         src="/<?= strip_tags($string_content) ?>" width="109"
         height="109" alt="Превью публикации">
</div>
<span class="visually-hidden">Фото</span>
