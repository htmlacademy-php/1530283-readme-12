<?php
/**
 * Шаблон карточки лайка к публикации для страницы профиля пользователя
 *
 * @var array $like - данные лайка к публикации
 * @var bool $is_own_profile - собственный профиль
 */

$author = $like['author'] ?? [];
$author_id = $author['id'] ?? null;
$author_login =
    isset($author['login']) ? htmlspecialchars($author['login']) : '';
$author_avatar_url = $author['avatar_url'] ?? AVATAR_PLACEHOLDER;
$additional_info =
    $is_own_profile ? 'Лайкнул вашу публикацию' : 'Лайкнул публикацию';
$post = $like['post'] ?? [];
$post_id = $post['id'] ?? null;
$post_content_type = $post['content_type'] ?? '';
$created_at = $like['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
?>
<li class="post-mini post-mini--photo post user">
    <div class="post-mini__user-info user__info">
        <div class="post-mini__avatar user__avatar">
            <a class="user__avatar-link"
               href="profile.php?user-id=<?= $author_id ?>">
                <img class="post-mini__picture user__picture"
                     src="/<?= $author_avatar_url ?>"
                     alt="Аватар пользователя" width="60" height="60">
            </a>
        </div>
        <div class="post-mini__name-wrapper user__name-wrapper">
            <a class="post-mini__name user__name"
               href="profile.php?user-id=<?= $author_id ?>">
                <span><?= $author_login ?></span>
            </a>
            <div class="post-mini__action">
                <span class="post-mini__activity user__additional"><?=
                    $additional_info ?></span>
                <time class="post-mini__time user__additional"
                      datetime="<?= $iso_date_time ?>"><?= $relative_time ?>
                    назад
                </time>
            </div>
        </div>
    </div>
    <div class="post-mini__preview">
        <a class="post-mini__link" href="post.php?post-id=<?= $post_id ?>"
           title="Перейти на публикацию">
            <?= include_template(
                "pages/profile/like/content/$post_content_type.php",
                $post
            ) ?>
        </a>
    </div>
</li>
