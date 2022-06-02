<?php
/**
 * Шаблон основного контента страницы 'Популярное'.
 * Отображает секцию с карточками публикаций.
 *
 * @var array $post_cards - массив с данными для карточек публикаций
 */

?>

<div class="popular__posts">
    <?php
    foreach ($post_cards as $post_card): ?>
        <?= include_template(
            'pages/popular/post-card/base.php',
            ['post_card' => $post_card]
        ) ?>
    <?php
    endforeach; ?>
</div>
