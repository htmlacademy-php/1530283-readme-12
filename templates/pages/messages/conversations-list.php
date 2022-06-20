<?php
/**
 * Шаблон списка разговоров пользователя для страницы 'Сообщения'
 *
 * @var array $conversations -  массив с разговорами пользователя
 */

?>

<ul class="messages__contacts-list tabs__list">
    <?php
    foreach ($conversations as $conversation): ?>
        <?= include_template(
            'pages/messages/conversation-card.php',
            ['conversation' => $conversation]
        ) ?>
    <?php
    endforeach; ?>
</ul>
