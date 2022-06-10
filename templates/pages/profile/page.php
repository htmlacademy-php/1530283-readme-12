<?php

/**
 * Шаблон страницы профиля пользователя
 *
 * @var string $user_content - разметка секции с информацией о пользователей
 * @var string $tabs_content - разметка секции табов
 * @var array $main_content - разметка основного контента для выбранного таба
 */

?>

<h1 class="visually-hidden">Профиль</h1>
<div class="profile profile--default">
    <?= $user_content ?>
    <div class="profile__tabs-wrapper tabs">
        <div class="container">
            <?= $tabs_content ?>
            <div class="profile__tab-content">
                <?= $main_content ?>
            </div>
        </div>
    </div>
</div>
