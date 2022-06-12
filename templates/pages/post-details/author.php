<?php

require_once 'utils/helpers.php';

/**
 * Шаблон секции автора публикации для страницы просмотра публикации.
 *
 * @var array $author - ассоциативный массив с данными автора публикации
 * @var bool $is_own_post - собственная публикация
 */

list(
    'id' => $id,
    'login' => $user_name,
    'avatar_url' => $avatar_url,
    'created_at' => $created_at,
    'subscribers_count' => $subscribers_count,
    'posts_count' => $posts_count,
    'is_observable' => $is_observable
    )
    = $author;

$user_name = strip_tags($user_name);

?>
<div class="post-details__user user">
    <div class="post-details__user-info user__info">
        <div class="post-details__avatar user__avatar">
            <a class="post-details__avatar-link user__avatar-link"
               href="profile.php?user-id=<?= $id ?>">
                <img class="post-details__picture user__picture"
                     src="/<?= $avatar_url ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя" width="60" height="60">
            </a>
        </div>
        <div class="post-details__name-wrapper user__name-wrapper">
            <a class="post-details__name user__name" href="#">
                <span><?= strip_tags($user_name) ?></span>
            </a>
            <time class="post-details__time user__time"
                  datetime="<?= format_iso_date_time(
                      $created_at
                  ) ?>"><?= format_relative_time($created_at) ?> на сайте
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
            <a class="user__button user__button--writing button button--green"
               href="messages.php">Сообщение</a>
        </div>
    <?php
    endif; ?>
</div>
