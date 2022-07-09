<?php
/**
 * Шаблон секции сортировки и фильтрации карточки публикации
 * страницы 'Популярное'.
 *
 * @var array $sort_types - массив с типами сортировки
 * @var bool $is_sort_order_reversed - обратное направление сортировки
 * @var array $any_content_filter - ассоциативный массив с данными
 * для снятия фильтров по типу контента
 * @var array $content_filters - массив с фильтрами по типу контента
 */

$any_content_filter_type = $any_content_filter['type'] ?? '';
$is_any_content_filter_active = $any_content_filter['active'] ?? false;
$any_content_filter_url = $any_content_filter['url'] ?? '';
?>

<div class="popular__filters-wrapper">
    <div class="popular__sorting sorting">
        <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
        <ul class="popular__sorting-list sorting__list">
            <?php
            foreach ($sort_types as $sort_type): ?>
                <?php
                $is_active = $sort_type['active'] ?? false;
                ?>
                <li class="sorting__item">
                    <a class="sorting__link <?= $is_active
                        ? 'sorting__link--active'
                        : '' ?> <?= $is_sort_order_reversed
                        ? 'sorting__link--reverse' : '' ?>"
                       href="<?= $sort_type['url'] ?? '' ?>">
                        <span><?= $sort_type['label'] ?? '' ?></span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
            <?php
            endforeach; ?>
        </ul>
    </div>
    <div class="popular__filters filters">
        <b class="popular__filters-caption filters__caption">Тип контента:</b>
        <ul class="popular__filters-list filters__list">
            <li class="popular__filters-item
                popular__filters-item--<?= $any_content_filter_type ?>
                filters__item filters__item--<?= $any_content_filter_type ?>">
                <a class="filters__button filters__button--ellipse
                    filters__button--all <?= $is_any_content_filter_active
                    ? 'filters__button--active' : '' ?>"
                    <?= !$is_any_content_filter_active
                        ? "href='$any_content_filter_url'" : '' ?> >
                    <span><?= $any_content_filter['name'] ?? '' ?></span>
                </a>
            </li>
            <?php
            foreach ($content_filters as $content_filter): ?>
                <?php
                $filter_type = $content_filter['type'] ?? '';
                $filter_name = $content_filter['name'] ?? '';
                $is_filter_active = $content_filter['active'] ?? false;
                $filter_url = $content_filter['url'] ?? '';
                ?>
                <li class="popular__filters-item filters__item">
                    <a class="filters__button
                        filters__button--<?= $filter_type ?>
                        <?= $is_filter_active ? 'filters__button--active'
                        : '' ?> button"
                        <?= !$is_filter_active ? "href='$filter_url'" : '' ?> >
                        <span class="visually-hidden"><?= $filter_name ?></span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-<?= $filter_type ?>"></use>
                        </svg>
                    </a>
                </li>
            <?php
            endforeach; ?>
        </ul>
    </div>
</div>
