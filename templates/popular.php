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
