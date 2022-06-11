<?php
/**
 * Шаблон карточки лайка к публикации для страницы профиля пользователя
 *
 * @var array $like - данные лайка к публикации
 * @var bool $is_own_profile - собственный профиль
 */

list(
    'created_at' => $created_at,
    'author' => $author,
    'post' => $post,
    ) = $like;

$additional_info =
    $is_own_profile ? 'Лайкнул вашу публикацию' : 'Лайкнул публикацию';
?>
<li class="post-mini post-mini--photo post user">
    <div class="post-mini__user-info user__info">
        <div class="post-mini__avatar user__avatar">
            <a class="user__avatar-link"
               href="profile.php?user-id=<?= $author['id'] ?>">
                <img class="post-mini__picture user__picture"
                     src="/<?= $author['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя" width="60" height="60">
            </a>
        </div>
        <div class="post-mini__name-wrapper user__name-wrapper">
            <a class="post-mini__name user__name"
               href="profile.php?user-id=<?= $author['id'] ?>">
                <span><?= htmlspecialchars($author['login']) ?></span>
            </a>
            <div class="post-mini__action">
                <span class="post-mini__activity user__additional"><?=
                    $additional_info ?></span>
                <time class="post-mini__time user__additional"
                      datetime="<?= format_iso_date_time(
                          $created_at
                      ) ?>"><?= format_relative_time($created_at) ?> назад
                </time>
            </div>
        </div>
    </div>
    <div class="post-mini__preview">
        <a class="post-mini__link" href="post.php?post-id=<?= $post['id'] ?>"
           title="Перейти на публикацию">
            <?= include_template(
                'pages/profile/like/content/' . $post['content_type'] . '.php',
                $post
            ) ?>
        </a>
    </div>
</li>
