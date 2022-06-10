<?php
/**
 * Шаблон секции информации о пользователе страницы профиля пользователя
 *
 * @var array $user - информация о пользователе
 * @var bool $is_own_profile - собственный профиль
 */

list(
    'id' => $id,
    'created_at' => $created_at,
    'login' => $login,
    'email' => $email,
    'avatar_url' => $avatar_url,
    'subscribers_count' => $subscribers_count,
    'posts_count' => $posts_count,
    'is_observable' => $is_observable
    ) = $user;
?>

<div class="profile__user-wrapper">
    <div class="profile__user user container">
        <div class="profile__user-info user__info">
            <div class="profile__avatar user__avatar">
                <img class="profile__picture user__picture"
                     src="/<?= $avatar_url ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя" width="100" height="100">
            </div>
            <div class="profile__name-wrapper user__name-wrapper">
                <span class="profile__name user__name"><?= htmlspecialchars(
                        $login
                    ) ?></span>
                <time class="profile__user-time user__time"
                      datetime="<?= format_iso_date_time(
                          $created_at
                      ) ?>"><?= format_relative_time($created_at) ?> на сайте
                </time>
            </div>
        </div>
        <div class="profile__rating user__rating">
            <p class="profile__rating-item user__rating-item user__rating-item--publications">
                <span class="user__rating-amount"><?= $posts_count ?></span>
                <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form(
                        $posts_count,
                        'публикация',
                        'публикации',
                        'публикаций'
                    ) ?></span>
            </p>
            <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                <span class="user__rating-amount"><?= $subscribers_count ?></span>
                <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form(
                        $subscribers_count,
                        'подписчик',
                        'подписчика',
                        'подписчиков'
                    ) ?></span>
            </p>
        </div>
        <?php
        if (!$is_own_profile): ?>
            <div class="profile__user-buttons user__buttons">
                <a class="profile__user-button user__button user__button--subscription button button--main"
                   href="subscribe.php?user-id=<?= $id ?>"><?= $is_observable
                        ? 'Отписаться' : 'Подписаться' ?>
                </a>
                <a class="profile__user-button user__button user__button--writing button button--green"
                   href="messages.php">Сообщение</a>
            </div>
        <?php
        endif; ?>
    </div>
</div>
