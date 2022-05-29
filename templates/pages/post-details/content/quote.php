<?php

/**
 * Шаблон контента публикации цитаты для страницы просмотра публикации
 *
 * @var string $text_content - текстовый контент публикации
 * @var string $string_content - строковый контент публикации
 */

$text_content   = htmlspecialchars($text_content);
$string_content = htmlspecialchars($string_content);
?>

<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p>
                <?= $text_content ?>
            </p>
            <cite><?= $string_content ?></cite>
        </blockquote>
    </div>
</div>
