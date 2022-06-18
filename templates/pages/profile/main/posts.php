<?php
/**
 * Шаблон основного страницы профиля пользователя для таба 'Посты'
 *
 * @var array $posts - публикации пользователя
 */
?>

<section
    class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php
    foreach ($posts as $post): ?>
        <?= include_template(
            'pages/profile/post-card/base.php',
            [
                'card_modifier' => 'profile',
                'post_card' => $post
            ]
        ) ?>
    <?php
    endforeach; ?>
</section>
