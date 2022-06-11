<?php
/**
 * Шаблон основного страницы профиля пользователя для таба 'Лайки'
 *
 * @var array $likes - массив с данными лайков к публикациям пользователя
 * @var bool $is_own_profile - собственный профиль
 */

?>

<section class="profile__likes tabs__content tabs__content--active">
    <h2 class="visually-hidden">Лайки</h2>
    <ul class="profile__likes-list">
        <?php
        foreach ($likes as $like): ?>
            <?= include_template(
                'pages/profile/like/base.php',
                ['like' => $like, 'is_own_profile' => $is_own_profile]
            ) ?>
        <?php
        endforeach; ?>
    </ul>
</section>
