<?php
/**
 * Шаблон основного контента страницы 'Результаты поиска'
 * Отображает карточки найденных публикаций
 *
 * @var array $post_cards - массив с данными для карточек публикаций
 */

?>

<div class="search__results-wrapper">
    <div class="container">
        <div class="search__content">
            <?php
            foreach ($post_cards as $post_card): ?>
                <?= include_template(
                    'common/post-card/base.php',
                    [
                        'post_card' => $post_card,
                        'card_modifier' => 'search',
                    ]
                ) ?>
            <?php
            endforeach; ?>
        </div>
    </div>
</div>
