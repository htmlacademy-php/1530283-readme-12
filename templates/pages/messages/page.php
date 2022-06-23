<?php
/**
 * Шаблон страницы 'Сообщения'
 *
 * @var array $user - данные пользователя
 * @var array $conversations_content -  разметка секции с разговорами
 * @var string $messages_content - разметка секции со списком сообщений
 * текущего разговора
 * @var string $form_content - разметка формы добавления сообщения к
 * текущему разговору
 */

?>

<h1 class="visually-hidden">Личные сообщения</h1>
<section class="messages tabs">
    <h2 class="visually-hidden">Сообщения</h2>
    <div class="messages__contacts">
        <?= $conversations_content ?>
    </div>
    <div class="messages__chat">
        <div class="messages__chat-wrapper">
            <?= $messages_content ?>
        </div>
        <div class="comments">
            <?= $form_content ?>
        </div>
    </div>
</section>
