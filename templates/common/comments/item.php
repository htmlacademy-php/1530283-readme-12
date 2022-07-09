<?php
/**
 * Шаблон комментария в списке для секции комментариев к публикации.
 *
 * @var array $comment - ассоциативный массив комментария
 */

$content =
    isset($comment['content']) ? htmlspecialchars($comment['content']) : '';
$created_at = $comment['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
$author = $comment['author'] ?? [];
$author_id = $author['id'] ?? null;
$author_login =
    isset($author['login']) ? htmlspecialchars($author['login']) : '';
$author_avatar_url = $author['avatar_url'] ?? AVATAR_PLACEHOLDER;
?>

<li class="comments__item user">
    <div class="comments__avatar">
        <a class="user__avatar-link"
           href="profile.php?user-id=<?= $author_id ?>">
            <img class="comments__picture"
                 src="/<?= $author_avatar_url ?>"
                 alt="Аватар пользователя">
        </a>
    </div>
    <div class="comments__info">
        <div class="comments__name-wrapper">
            <a class="comments__user-name"
               href="profile.php?user-id=<?= $author_id ?>">
                <span><?= $author_login ?></span>
            </a>
            <time class="comments__time"
                  datetime="<?= $iso_date_time ?>"><?= $relative_time ?> назад
            </time>
        </div>
        <p class="comments__text">
            <?= $content ?>
        </p>
    </div>
</li>
