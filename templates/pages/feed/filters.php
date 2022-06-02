<?php
/**
 * Шаблон фильтров по типу контента для страницы 'Моя лента'
 * страницы 'Популярное'.
 *
 * @var array $any_content_filter - ассоциативный массив с данными
 * для снятия фильтров по типу контента
 * @var array $content_filters - массив с фильтрами по типу контента
 */


?>

<ul class="feed__filters filters">
    <li class="feed__filters-item filters__item">
        <a class="filters__button <?= $any_content_filter['active']
            ? 'filters__button--active' : '' ?>"
            <?= !$any_content_filter['active']
                ? 'href="' . $any_content_filter['url'] . '"' : '' ?>>
            <span><?= $any_content_filter['name'] ?></span>
        </a>
    </li>
    <?php
    foreach ($content_filters as $content_filter): ?>
        <li class="feed__filters-item filters__item">
            <a class="filters__button filters__button--<?= $content_filter['type'] ?> <?= $content_filter['active']
                ? 'filters__button--active' : '' ?> button"
                <?= !$content_filter['active']
                    ? 'href="' . $content_filter['url'] . '"' : '' ?> >
                <span class="visually-hidden"><?= $content_filter['name'] ?></span>
                <svg class="filters__icon" width="22" height="18">
                    <use xlink:href="#icon-filter-<?= $content_filter['type'] ?>"></use>
                </svg>
            </a>
        </li>
    <?php
    endforeach; ?>
</ul>
