<?php
/**
 * Шаблон секции информации о пользователе страницы профиля пользователя
 *
 * @var array $user - информация о пользователе
 * @var bool $is_own_profile - собственный профиль
 */

$id = $user['id'] ?? null;
$login = isset($user['login']) ? htmlspecialchars($user['login']) : '';
$avatar_url = $user['avatar_url'] ?? AVATAR_PLACEHOLDER;
$created_at = $user['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
$posts_count = $user['posts_count'] ?? 0;
$subscribers_count = $user['subscribers_count'] ?? 0;
$is_observable = $user['is_observable'] ?? false;
?>

<div class="profile__user-wrapper">
    <div class="profile__user user container">
        <div class="profile__user-info user__info">
            <div class="profile__avatar user__avatar">
                <img class="profile__picture user__picture"
                     src="/<?= $avatar_url ?>"
                     alt="Аватар пользователя" width="100" height="100">
            </div>
            <div class="profile__name-wrapper user__name-wrapper">
                <span class="profile__name user__name"><?= $login ?></span>
                <time class="profile__user-time user__time"
                      datetime="<?= $iso_date_time ?>"><?= $relative_time ?> на
                    сайте
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
                <a class="profile__user-button user__button user__button--subscription button
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
</div>
