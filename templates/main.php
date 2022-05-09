<?php
if (!isset($post_cards)) {
    throw new Exception('Post cards variable is not defined');
}

if (!is_array($post_cards)) {
    throw new Exception('Post cards variable is not array');
}
?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= include_template('partials/popular-filters.php') ?>
    <div class="popular__posts">
        <?php foreach ($post_cards as $post_card): ?>
            <?= include_template('partials/post-card.php', ['post_card' => $post_card]) ?>
        <?php endforeach; ?>
    </div>
</div>
