<?php
if (!isset($content_types)) {
    throw new Exception('Content types variable is not defined');
}
?>

<div class="popular__filters-wrapper">
    <div class="popular__sorting sorting">
        <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
        <ul class="popular__sorting-list sorting__list">
            <li class="sorting__item sorting__item--popular">
                <a class="sorting__link sorting__link--active" href="#">
                    <span>Популярность</span>
                    <svg class="sorting__icon" width="10" height="12">
                        <use xlink:href="#icon-sort"></use>
                    </svg>
                </a>
            </li>
            <li class="sorting__item">
                <a class="sorting__link" href="#">
                    <span>Лайки</span>
                    <svg class="sorting__icon" width="10" height="12">
                        <use xlink:href="#icon-sort"></use>
                    </svg>
                </a>
            </li>
            <li class="sorting__item">
                <a class="sorting__link" href="#">
                    <span>Дата</span>
                    <svg class="sorting__icon" width="10" height="12">
                        <use xlink:href="#icon-sort"></use>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
    <div class="popular__filters filters">
        <b class="popular__filters-caption filters__caption">Тип контента:</b>
        <ul class="popular__filters-list filters__list">
            <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                <a class="filters__button filters__button--ellipse filters__button--all filters__button--active" href="#">
                    <span>Все</span>
                </a>
            </li>
            <?php foreach ($content_types as $content_type): ?>
                <li class="popular__filters-item filters__item">
                    <a class="filters__button filters__button--photo button" href="#">
                        <span class="visually-hidden"><?= $content_type['name'] ?></span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-<?= $content_type['icon'] ?>"></use>
                        </svg>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
