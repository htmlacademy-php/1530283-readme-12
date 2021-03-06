<?php
/**
 * Шаблон карточки подписки для страницы профиля пользователя.
 * В карточке содержатся сведения о пользователе, имеющим подписку
 * на пользователя, которму принадлежит профиль.
 *
 * @var array $subscription - данные о подписке на пользователя
 */

$id = $subscription['id'] ?? null;
$is_observable = $subscription['is_observable'] ?? false;
$created_at = $subscription['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
$login =
    isset($subscription['login']) ? htmlspecialchars($subscription['login'])
        : '';
$posts_count = $subscription['posts_count'] ?? 0;
$subscribers_count = $subscription['subscribers_count'] ?? 0;
$avatar_url = $subscription['avatar_url'] ?? AVATAR_PLACEHOLDER;
$is_user = $subscription['is_user'] ?? false;
?>

<li class="post-mini post-mini--photo post user">
    <div class="post-mini__user-info user__info">
        <div class="post-mini__avatar user__avatar">
            <a class="user__avatar-link"
               href="profile.php?user-id=<?= $id ?>">
                <img class="post-mini__picture user__picture"
                     src="<?= $avatar_url ?>"
                     alt="Аватар пользователя"
                     width="60" height="60">
            </a>
        </div>
        <div class="post-mini__name-wrapper user__name-wrapper">
            <a class="post-mini__name user__name"
               href="profile.php?user-id=<?= $id ?>">
                <span><?= $login ?></span>
            </a>
            <time class="post-mini__time user__additional"
                  datetime="<?= $iso_date_time ?>"><?= $relative_time ?> на
                сайте
            </time>
        </div>
    </div>
    <div class="post-mini__rating user__rating">
        <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
            <span class="post-mini__rating-amount user__rating-amount"><?= $posts_count ?></span>
            <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form(
                    $posts_count,
                    'публикация',
                    'публикации',
                    'побликаций'
                ) ?></span>
        </p>
        <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
            <span class="post-mini__rating-amount user__rating-amount"><?= $subscribers_count ?></span>
            <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form(
                    $subscribers_count,
                    'подписчик',
                    'подписчика',
                    'подписчиков'
                ) ?></span>
        </p>
    </div>
    <div class="post-mini__user-buttons user__buttons">
        <?php
        if (!$is_user): ?>
            <a class="post-mini__user-button user__button user__button--subscription button
           button--<?= $is_observable ? 'quartz' : 'main' ?>"
               href="subscribe.php?user-id=<?= $id ?>"
               type="button"><?= $is_observable ? 'Отписаться'
                    : 'Подписаться' ?>
            </a>
        <?php
        endif;; ?>
    </div>
</li>
