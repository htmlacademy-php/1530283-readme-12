<?php
/**
 * Шаблон оснвного контента страницы 'Моя лента'.
 * Отображает карточки публикаций
 *
 * @var array $post_cards - массив с данными для карточек публикаций
 */

?>

<div class="feed__main-wrapper">
    <div class="feed__wrapper">
        <?php
        foreach ($post_cards as $post_card): ?>
            <?= include_template(
                'common/post-card/base.php',
                [
                    'post_card' => $post_card,
                    'card_modifier' => 'feed'
                ]
            ) ?>
        <?php
        endforeach; ?>
    </div>
</div>
