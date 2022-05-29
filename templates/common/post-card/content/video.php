<?php

require_once 'utils/helpers.php';

/**
 * Общий шаблон контента карточки публикации видео
 *
 * @var string $id - id публикации
 * @var string $string_content - строковый контент публикации
 */

$string_content = isset($string_content) ? strip_tags($string_content) : '';
?>

<div class="post-video__block">
    <div class="post-video__preview">
        <img alt="youtube cover" width="760" height="396"
             src="<?= get_youtube_cover_url($string_content) ?>"/>
    </div>
    <a href="post.php?post_id=<?= $id ?>" class="post-video__play-big button">
        <svg class="post-video__play-big-icon" width="14" height="14">
            <use xlink:href="#icon-video-play-big"></use>
        </svg>
        <span class="visually-hidden">Запустить проигрыватель</span>
    </a>
</div>
