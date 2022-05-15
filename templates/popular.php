<?php
/**
 * Шаблон основного контента странцы 'Популярное'.
 * Отображает карточки публикаций с учетом выбранной сортировки и фильтрации.
 *
 * @var string $popular_filters_content - разметка секции сортировки и
 * фильтрации по типу контента
 * @var array $post_cards - массив с данными для карточек публикаций
 */
?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= $popular_filters_content ?>
    <div class="popular__posts">
        <?php
        foreach ($post_cards as $post_card): ?>
            <?= include_template(
                'partials/post-card/base.php',
                ['post_card' => $post_card]
            ) ?>
        <?php
        endforeach; ?>
    </div>
</div>
