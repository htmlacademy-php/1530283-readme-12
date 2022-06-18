<?php
/**
 * Шаблон контента публикации цитаты для страницы просмотра публикации
 *
 * @var string $text_content - текстовый контент публикации
 * @var string $string_content - строковый контент публикации
 */

?>

<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p>
                <?= htmlspecialchars($text_content) ?>
            </p>
            <cite><?= htmlspecialchars($string_content) ?></cite>
        </blockquote>
    </div>
</div>
