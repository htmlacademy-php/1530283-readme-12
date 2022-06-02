<?php

/**
 * Шаблон контента публикации текста для страницы просмотра публикации
 *
 * @var string $text_content - текстовый контент публикации
 */

$text_content = htmlspecialchars($text_content);
?>

<div class="post-details__image-wrapper post-text">
    <div class="post__main">
        <p>
            <?= $text_content ?>
        </p>
    </div>
</div>
