<?php

require_once 'utils/helpers.php';

/**
 * Шаблон контента публикации видео для страницы просмотра публикации
 *
 * @var string $string_content - строковый контент публикации
 */

$string_content = get_youtube_iframe_url(strip_tags($string_content));
?>

<div class="post-details__image-wrapper post-photo__image-wrapper">
    <iframe width="760" height="400"
            src="<?= $string_content ?>"
            frameborder="0"></iframe>
</div>
