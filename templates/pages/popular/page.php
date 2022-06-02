<?php
/**
 * Шаблон странцы 'Популярное'.
 *
 * @var string $filters_content - разметка секции сортировки и фильтрации
 * по типу контента
 * @var string $main_content - разметка основного контента страницы
 */

?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= $filters_content ?>
    <?= $main_content ?>
</div>
