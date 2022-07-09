<?php
/**
 * Шаблон основного контента страницы 'Популярное'.
 * Отображает секцию с карточками публикаций.
 *
 * @var array $post_cards - массив с данными для карточек публикаций
 * @var array $pagination - ссылки пагинации
 */

$prev_page_url = $pagination['prev'] ?? '';
$next_page_url = $pagination['next'] ?? '';
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
<?php
if (!(!$prev_page_url && !$next_page_url)): ?>
    <div class="popular__page-links">
        <a class="popular__page-link popular__page-link--prev button button--gray
    <?= !$prev_page_url ? 'button--gray-disabled' : '' ?>"
            <?= $prev_page_url ? "href='$prev_page_url'" : '' ?>>Предыдущая
            страница</a>
        <a class="popular__page-link popular__page-link--next button button--gray
        <?= !$next_page_url ? 'button--gray-disabled' : '' ?>"
            <?= $next_page_url ? "href='$next_page_url'" : '' ?>>Следующая
            страница</a>
    </div>
<?php
endif; ?>
