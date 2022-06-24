<?php

require_once 'utils/functions.php';

/**
 * Шаблон карточки сообщения для страницы сообщения.
 *
 * @var array $message - данные сообщения
 */

list(
    'id' => $id,
    'author' => $author,
    'content' => $content,
    'created_at' => $created_at,
    'is_own' => $is_own
    ) = $message;

$user_profile_url = 'profile.php?user-id=' . $author['id'];
?>

<li id="message-id-<?= $id ?>"
        class="messages__item <?= $is_own ? 'messages__item--my' : '' ?>">
    <div class="messages__info-wrapper">
        <div class="messages__item-avatar">
            <a class="messages__author-link" href="<?= $user_profile_url ?>">
                <img class="messages__avatar"
                     src="/<?= $author['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя">
            </a>
        </div>
        <div class="messages__item-info">
            <a class="messages__author" href="<?= $user_profile_url ?>">
                <?= $author['login'] ?>
            </a>
            <time class="messages__time"
                  datetime="<?= format_iso_date_time($created_at) ?>">
                <?= format_relative_time($created_at) ?> назад
            </time>
        </div>
    </div>
    <p class="messages__text">
        <?= $content ?>
    </p>
</li>