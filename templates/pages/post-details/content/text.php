<?php
/**
 * Шаблон контента публикации текста для страницы просмотра публикации
 *
 * @var string $text_content - текстовый контент публикации
 */

?>

<div class="post-details__image-wrapper post-text">
    <div class="post__main">
        <p>
            <?= htmlspecialchars($text_content) ?>
        </p>
    </div>
</div>
