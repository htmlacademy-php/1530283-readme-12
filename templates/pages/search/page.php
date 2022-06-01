<?php
/**
 * Шаблон страницы результатов поиска
 *
 * @var string $query_content - разметка блока строки запроса
 * @var string $main_content - разметка основного контента
 */

?>

<h1 class="visually-hidden">Страница результатов поиска</h1>
<section class="search">
    <h2 class="visually-hidden">Результаты поиска</h2>
    <?= $query_content ?>
    <?= $main_content ?>
</section>
