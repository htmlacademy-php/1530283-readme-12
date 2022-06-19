<?php

/**
 * Шаблон карточки разговора для страницы 'Сообщения'
 *
 * @var array $conversation - данные разговора
 */

list(
    'url' => $url,
    'active' => $active,
    'interlocutor' => $interlocutor
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
        </div>
        <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= $interlocutor['login'] ?>
                  </span>
            <div class="messages__preview">
                <p class="messages__preview-text">
                    (Вы:) Озеро Байкал – огромное
                </p>
                <time class="messages__preview-time"
                      datetime="2019-05-01T14:40">
                    14:40
                </time>
            </div>
        </div>
    </a>
</li>
