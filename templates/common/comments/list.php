<?php
/**
 * Шаблон списка комментариев для секции комментариев к публикации
 *
 * @var array $comments - массив с комментариями
 * @var int | null $comments_count $comments_count - полное число комментариев
 * @var string | null $expand_comments_url - URL для показа полного списка
 * комментариев
 */

$with_expand_button =
    !is_null($comments_count) && !is_null($expand_comments_url);
?>

<div class="comments__list-wrapper">
    <ul id="comments" class="comments__list">
        <?php
        foreach ($comments as $comment): ?>
            <?= include_template(
                'common/comments/item.php',
                ['comment' => $comment]
            ) ?>
        <?php
        endforeach; ?>
    </ul>
    <?php
    if ($with_expand_button): ?>
        <a class="comments__more-link" href="<?= $expand_comments_url ?>">
            <span>Показать все комментарии</span>
            <sup class="comments__amount"><?= $comments_count ?></sup>
        </a>
    <?php
    endif; ?>
</div>
