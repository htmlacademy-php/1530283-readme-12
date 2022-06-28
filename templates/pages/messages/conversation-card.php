<?php

require_once 'utils/functions.php';

/**
 * Шаблон карточки разговора для страницы 'Сообщения'
 *
 * @var array $conversation - данные разговора
 */

list(
    'url' => $url,
    'active' => $active,
    'interlocutor' => $interlocutor,
    'unread_messages_count' => $unread_messages_count,
    'last_message' => $last_message
    ) = $conversation;
?>

<li class="messages__contacts-item">
    <a class="messages__contacts-tab tabs__item
     <?= $active ? 'tabs__item--active messages__contacts-tab--active'
        : '' ?>" <?= !$active ? "href='$url'" : '' ?>>
        <div class="messages__avatar-wrapper">
            <img class="messages__avatar"
                 src="/<?= $interlocutor['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                 alt="Аватар пользователя" width="60" height="60">
            <?php
            if ($unread_messages_count): ?>
                <i class="messages__indicator"><?= $unread_messages_count ?></i>
            <?php
            endif; ?>
        </div>
        <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= htmlspecialchars($interlocutor['login']) ?>
                  </span>
            <?php
            if ($last_message): ?>
                <div class="messages__preview">
                    <p class="messages__preview-text">
                        <?= $last_message['is_own'] ? 'Вы: '
                            : '' ?><?= htmlspecialchars(
                            $last_message['content']
                        ) ?>
                    </p>
                    <time class="messages__preview-time"
                          datetime="<?= format_iso_date_time(
                              $last_message['created_at']
                          ) ?>">
                        <?= format_relative_time($last_message['created_at']) ?>
                        назад
                    </time>
                </div>
            <?php
            endif; ?>
        </div>
    </a>
</li>
