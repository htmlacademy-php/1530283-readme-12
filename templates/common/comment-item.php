<?php
/**
 * Шаблон комментария для списка секции комментариев.
 *
 * @var array $comment - ассоциативный массив комментария
 */

list(
    'content' => $content,
    'created_at' => $created_at,
    'author_id' => $author_id,
    'author_login' => $author_login,
    'author_avatar' => $author_avatar
    )
    = $comment;
?>
<li class="comments__item user">
    <div class="comments__avatar">
        <a class="user__avatar-link"
           href="profile.php?user_id=<?= $author_id ?>">
            <img class="comments__picture"
                 src="/<?= $author_avatar ?? AVATAR_PLACEHOLDER ?>"
                 alt="Аватар пользователя">
        </a>
    </div>
    <div class="comments__info">
        <div class="comments__name-wrapper">
            <a class="comments__user-name"
               href="profile.php?user_id=<?= $author_id ?>">
                <span><?= $author_login ?></span>
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
            <?= $content ?>
        </p>
    </div>
</li>
