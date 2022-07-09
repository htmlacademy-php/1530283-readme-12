<?php

/**
 * Шаблон фильтров по типу контента для страницы 'Моя лента'
 * страницы 'Популярное'.
 *
 * @var array $any_content_filter - ассоциативный массив с данными
 * для снятия фильтров по типу контента
 * @var array $content_filters - массив с фильтрами по типу контента
 */

$is_any_content_filter_active = $any_content_filter['active'] ?? false;
$any_content_filter_url = $any_content_filter['url'] ?? '';
?>

<ul class="feed__filters filters">
    <li class="feed__filters-item filters__item">
        <a class="filters__button <?= $is_any_content_filter_active
            ? 'filters__button--active' : '' ?>"
            <?= !$is_any_content_filter_active
                ? "href='$any_content_filter_url'" : '' ?>>
            <span><?= $any_content_filter['name'] ?? '' ?></span>
        </a>
    </li>
    <?php
    foreach ($content_filters as $content_filter): ?>
        <?php
        $type = $content_filter['type'] ?? '';
        $name = $content_filter['name'] ?? '';
        $is_active = $content_filter['active'] ?? false;
        $url = $content_filter['url'] ?? '';
        ?>
        <li class="feed__filters-item filters__item">
            <a class="filters__button filters__button--<?= $type ?>
                <?= $is_active ? 'filters__button--active' : '' ?> button"
                <?= !$is_active ? "href='$url'" : '' ?>>
                <span class="visually-hidden"><?= $name ?></span>
                <svg class="filters__icon" width="22" height="18">
                    <use xlink:href="#icon-filter-<?= $type ?>"></use>
                </svg>
            </a>
        </li>
    <?php
    endforeach; ?>
</ul>
