<?php

require_once 'utils/helpers.php';

/**
 * Шаблон секции автора публикации для страницы просмотра публикации.
 *
 * @var array $author - ассоциативный массив с данными автора публикации
 * @var bool $is_own_post - собственная публикация
 */

$id = $author['id'] ?? null;
$avatar_url = $author['avatar_url'] ?? AVATAR_PLACEHOLDER;
$user_name = isset($author['login']) ? htmlspecialchars($author['login']) : '';
$posts_count = $author['posts_count'] ?? 0;
$subscribers_count = $author['subscribers_count'] ?? 0;
$is_observable = $author['is_observable'] ?? false;
$created_at = $author['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
?>
<div class="post-details__user user">
    <div class="post-details__user-info user__info">
        <div class="post-details__avatar user__avatar">
            <a class="post-details__avatar-link user__avatar-link"
               href="profile.php?user-id=<?= $id ?>">
                <img class="post-details__picture user__picture"
                     src="/<?= $avatar_url ?>"
                     alt="Аватар пользователя" width="60" height="60">
            </a>
        </div>
        <div class="post-details__name-wrapper user__name-wrapper">
            <a class="post-details__name user__name"
               href="profile.php?user-id=<?= $id ?>">
                <span><?= $user_name ?></span>
            </a>
            <time class="post-details__time user__time"
                  datetime="<?= $iso_date_time ?>"><?= $relative_time ?> на
                сайте
            </time>
        </div>
    </div>
    <div class="post-details__rating user__rating">
        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
            <span class="post-details__rating-amount user__rating-amount"><?= $subscribers_count ?></span>
            <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(
                    $subscribers_count,
                    'подписчик',
                    'подписчика',
                    'подписчиков'
                ) ?></span>
        </p>
        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
            <span class="post-details__rating-amount user__rating-amount"><?= $posts_count ?></span>
            <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(
                    $posts_count,
                    'публикация',
                    'публикации',
                    'побликаций'
                ) ?></span>
        </p>
    </div>
    <?php
    if (!$is_own_post): ?>
        <div class="post-details__user-buttons user__buttons">
            <a class="user__button user__button--subscription button
               button--<?= $is_observable ? 'quartz' : 'main' ?>"
               href="subscribe.php?user-id=<?= $id ?>"><?= $is_observable
                    ? 'Отписаться' : 'Подписаться' ?>
            </a>
            <?php
            if ($is_observable): ?>
                <a class="user__button user__button--writing button button--green"
                   href="messages.php?user-id=<?= $id ?>">Сообщение</a>
            <?php
            endif; ?>
        </div>
    <?php
    endif; ?>
</div>
