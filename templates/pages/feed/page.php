<?php
/**
 * Шаблон страницы 'Моя лента'
 *
 * @var string $filters_content - разметка секции фильтров по типу контента
 * @var string $main_content - разметка основного контента
 * @var string $promo_content - разметка промо-секции
 */

?>

<div class="container">
    <h1 class="page__title page__title--feed">Моя лента</h1>
</div>
<div class="page__main-wrapper container">
    <section class="feed">
        <h2 class="visually-hidden">Лента</h2>
        <?= $main_content ?>
        <?= $filters_content ?>
    </section>
    <?= $promo_content ?>
</div>
