<?php
/**
 * Шаблон страницы результатов поиска
 *
 * @var string $query_content - разметка блока строки запроса
 */

?>

<h1 class="visually-hidden">Страница результатов поиска (нет результатов)</h1>
<section class="search">
    <h2 class="visually-hidden">Результаты поиска</h2>
    <?= $query_content ?>
    <div class="search__results-wrapper">
        <div class="search__no-results container">
            <p class="search__no-results-info">К сожалению, ничего не
                найдено.</p>
            <p class="search__no-results-desc">
                Попробуйте изменить поисковый запрос или просто зайти в раздел
                &laquo;Популярное&raquo;, там живет самый крутой контент.
            </p>
            <div class="search__links">
                <a class="search__popular-link button button--main"
                   href="popular.php">Популярное</a>
                <a class="search__back-link" href="#">Вернуться назад</a>
            </div>
        </div>
    </div>
</section>
