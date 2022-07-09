<?php

require_once 'utils/functions.php';

/**
 * Шаблон карточки сообщения для страницы сообщения.
 *
 * @var array $message - данные сообщения
 */

$id = $message['id'] ?? null;
$author = $message['author'] ?? [];
$author_avatar_url = $author['avatar_url'] ?? AVATAR_PLACEHOLDER;
$author_login =
    isset($author['login']) ? htmlspecialchars($author['login']) : '';
$is_own = $message['is_own'] ?? false;
$content =
    isset($message['content']) ? htmlspecialchars($message['content']) : '';
$created_at = $message['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
$user_profile_url = 'profile.php?user-id=' . $author['id'];
?>

<li id="message-id-<?= $id ?>"
    class="messages__item <?= $is_own ? 'messages__item--my' : '' ?>">
    <div class="messages__info-wrapper">
        <div class="messages__item-avatar">
            <a class="messages__author-link" href="<?= $user_profile_url ?>">
                <img class="messages__avatar"
                     src="/<?= $author_avatar_url ?>"
                     alt="Аватар пользователя">
            </a>
        </div>
        <div class="messages__item-info">
            <a class="messages__author" href="<?= $user_profile_url ?>">
                <?= $author_login ?>
            </a>
            <time class="messages__time"
                  datetime="<?= $iso_date_time ?>">
                <?= $relative_time ?> назад
            </time>
        </div>
    </div>
    <p class="messages__text">
        <?= $content ?>
    </p>
</li>
