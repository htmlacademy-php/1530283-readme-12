<?php

require_once 'utils/helpers.php';

/**
 * Шаблон превью публицакиции-видео для карточки лайка
 * страницы профиля пользователя
 *
 * @var $string_content - строковый контент публикации
 */

$string_content = get_youtube_cover_url(strip_tags($string_content));
?>

<div class="post-mini__image-wrapper">
    <img class="post-mini__image"
         src="<?= $string_content ?>" width="109"
         height="109" alt="Превью публикации">
    <span class="post-mini__play-big">
        <svg class="post-mini__play-big-icon" width="12"
             height="13">
          <use xlink:href="#icon-video-play-big"></use>
        </svg>
    </span>
</div>
<span class="visually-hidden">Видео</span>
