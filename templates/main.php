<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= include_template('partials/popular-filters.php') ?>
    <div class="popular__posts">
        <?php foreach ($post_cards as $post_card): ?>
            <?php $is_post_card_invalid =
                !(isset($post_card['title']) and
                    isset($post_card['type']) and
                    isset($post_card['content']) and
                    isset($post_card['user_name']) and
                    isset($post_card['avatar']));

                if ($is_post_card_invalid) {
                    continue;
                }
            ?>
            <?= include_template('partials/post-card.php', ['post_card' => $post_card]) ?>
        <?php endforeach; ?>
    </div>
</div>
