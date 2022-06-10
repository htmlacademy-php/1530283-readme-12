<?php
/**
 * Шаблон основного страницы профиля пользователя для таба 'Посты'
 *
 * @var array $user_posts - публикации пользователя
 */
?>

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
