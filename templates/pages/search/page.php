<?php
/**
 * Шаблон страницы результатов поиска
 *
 * @var string $query_content - разметка блока строки запроса
 * @var array $post_cards - массив с данными для карточек публикаций
 */

?>

<h1 class="visually-hidden">Страница результатов поиска</h1>
<section class="search">
    <h2 class="visually-hidden">Результаты поиска</h2>
    <?= $query_content ?>
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
</section>
