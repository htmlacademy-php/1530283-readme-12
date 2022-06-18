<?php
/**
 * Шаблон основного страницы профиля пользователя для таба 'Подписки'
 *  *
 * @var array $subscriptions - массив с данными подписок на пользователя
 */

?>

<section class="profile__subscriptions tabs__content tabs__content--active">
    <h2 class="visually-hidden">Подриски</h2>
    <ul class="profile__subscriptions-list">
        <?php
        foreach ($subscriptions as $subscription): ?>
            <?= include_template(
                'pages/profile/subscription.php',
                ['subscription' => $subscription]
            ) ?>
        <?php
        endforeach; ?>
    </ul>
</section>
