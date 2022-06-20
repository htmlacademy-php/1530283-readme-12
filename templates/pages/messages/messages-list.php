<?php
/**
 * Шаблон списка сообщений текущего разговора для страницы 'Сообщения'
 *
 * @var array $messages - массив с сообщениями для текущего разговора
 */

var_dump($messages);

?>

<ul class="messages__list tabs__content tabs__content--active">
    <?php
    foreach ($messages as $message): ?>
        <?= include_template(
            'page/message/messages-card.php',
            ['message' => $message]
        ) ?>
    <?php
    endforeach; ?>
</ul>
