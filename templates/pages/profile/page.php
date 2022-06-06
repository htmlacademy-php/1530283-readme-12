<?php

/**
 * Шаблон страницы профиля пользователя
 *
 * @var string $user_content - разметка секции с информацией о пользователей
 * @var string $tabs_content - разметка секции табов
 * @var array $user_posts - публикации пользователя
 */

?>

<h1 class="visually-hidden">Профиль</h1>
<div class="profile profile--default">
    <?= $user_content ?>
    <div class="profile__tabs-wrapper tabs">
        <div class="container">
            <?= $tabs_content ?>
            <div class="profile__tab-content">
                <section
                        class="profile__posts tabs__content tabs__content--active">
                    <h2 class="visually-hidden">Публикации</h2>
                    <?php
                    foreach ($user_posts as $user_post): ?>
                        <?= include_template(
                            'common/post-card/base.php',
                            [
                                'card_modifier' => 'profile',
                                'post_card' => $user_post
                            ]
                        ) ?>
                    <?php
                    endforeach; ?>
                </section>
            </div>
        </div>
    </div>
</div>
