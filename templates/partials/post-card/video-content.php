<?php

require_once 'helpers.php';

/**
 * Шаблон контента карточки публикации видео для страницы 'Популярное'
 *
 * @var string $id - id публикации
 * @var string $string_content - строковый контент публикации
 */

$string_content = isset($string_content) ? strip_tags($string_content) : '';
?>

<div class="post-video__block">
    <div class="post-video__preview">
        <?= embed_youtube_cover($string_content) ?>
    </div>
    <a href="post.php?post_id=<?= $id ?>" class="post-video__play-big button">
        <svg class="post-video__play-big-icon" width="14" height="14">
            <use xlink:href="#icon-video-play-big"></use>
        </svg>
        <span class="visually-hidden">Запустить проигрыватель</span>
    </a>
</div>
