<?php
/**
 * Шаблон списка сообщений текущего разговора для страницы 'Сообщения'
 *
 * @var array $messages - массив с сообщениями для текущего разговора
 */

?>

<ul class="messages__list tabs__content tabs__content--active">
    <?php
    foreach ($messages as $message): ?>
        <?= include_template(
            'pages/messages/messages-card.php',
            ['message' => $message]
        ) ?>
    <?php
    endforeach; ?>
</ul>
