<?php

require_once 'utils/helpers.php';

/**
 * Шаблон контента публикации видео для страницы просмотра публикации
 *
 * @var string $string_content - строковый контент публикации
 */

$string_content = strip_tags($string_content);
?>

<div class="post-details__image-wrapper post-photo__image-wrapper">
    <?= embed_youtube_video($string_content); ?>
</div>
