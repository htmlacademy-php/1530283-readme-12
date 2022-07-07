<?php

require_once 'utils/functions.php';

/**
 * Шаблон карточки разговора для страницы 'Сообщения'
 *
 * @var array $conversation - данные разговора
 */

$url = $conversation['url'] ?? '';
$active = $conversation['active'] ?? false;
$unread_messages_count = $conversation['unread_messages_count'] ?? 0;
$interlocutor = $conversation['interlocutor'] ?? [];
$interlocutor_avatar_url = $interlocutor['avatar_url'] ?? AVATAR_PLACEHOLDER;
$interlocutor_login =
    isset($interlocutor['login']) ? htmlspecialchars($interlocutor['login'])
        : '';
$last_message = $conversation['last_message'] ?? null;
$is_last_message_own =
    $last_message && isset($last_message['is_own']) ? $last_message['is_own']
        : false;
$last_message_content =
    $last_message && isset($last_message['content']) ? htmlspecialchars(
        $last_message['content']
    ) : '';
$last_message_created_at = $last_message['created_at'] ?? null;
$last_message_iso_date_time =
    $last_message_created_at ? format_iso_date_time($last_message_created_at)
        : '';
$last_message_relative_time =
    $last_message_created_at ? format_relative_time($last_message_created_at)
        : '';
?>

<li class="messages__contacts-item">
    <a class="messages__contacts-tab tabs__item
     <?= $active ? 'tabs__item--active messages__contacts-tab--active'
        : '' ?>" <?= !$active ? "href='$url'" : '' ?>>
        <div class="messages__avatar-wrapper">
            <img class="messages__avatar"
                 src="/<?= $interlocutor_avatar_url ?>"
                 alt="Аватар пользователя" width="60" height="60">
            <?php
            if ($unread_messages_count): ?>
                <i class="messages__indicator"><?= $unread_messages_count ?></i>
            <?php
            endif; ?>
        </div>
        <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= $interlocutor_login ?>
                  </span>
            <?php
            if ($last_message): ?>
                <div class="messages__preview">
                    <p class="messages__preview-text">
                        <?= $is_last_message_own ? 'Вы: '
                            : '' ?><?= $last_message_content ?>
                    </p>
                    <time class="messages__preview-time"
                          datetime="<?= $last_message_iso_date_time ?>">
                        <?= $last_message_relative_time ?> назад
                    </time>
                </div>
            <?php
            endif; ?>
        </div>
    </a>
</li>
