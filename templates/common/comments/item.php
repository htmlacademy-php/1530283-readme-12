<?php
/**
 * Шаблон комментария в списке для секции комментариев к публикации.
 *
 * @var array $comment - ассоциативный массив комментария
 */

list(
    'content' => $content,
    'created_at' => $created_at,
    'author' => $author
    ) = $comment;
?>

<li class="comments__item user">
    <div class="comments__avatar">
        <a class="user__avatar-link"
           href="profile.php?user-id=<?= $author['id'] ?>">
            <img class="comments__picture"
                 src="/<?= $author['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                 alt="Аватар пользователя">
        </a>
    </div>
    <div class="comments__info">
        <div class="comments__name-wrapper">
            <a class="comments__user-name"
               href="profile.php?user-id=<?= $author['id'] ?>">
                <span><?= htmlspecialchars($author['login']) ?></span>
            </a>
            <time class="comments__time"
                  datetime="<?= format_iso_date_time(
                      $created_at
                  ) ?>"><?= format_relative_time(
                    $created_at
                ) ?> назад
            </time>
        </div>
        <p class="comments__text">
            <?= htmlspecialchars($content) ?>
        </p>
    </div>
</li>
