<?php
/**
 * Шаблон блока строки запроса для страницы поиска
 *
 * @var string $query - строка поиска
 */
?>

<div class="search__query-wrapper">
    <div class="search__query container">
        <span>Вы искали:</span>
        <span class="search__query-text"><?= $query ?></span>
    </div>
</div>
