<?php
/**
 * Шаблон секции табов по типу контента для страницы добавления публикации
 *
 * @var array $content_tabs - массив с данными для табов по типу контента
 */

?>

<div class="adding-post__tabs filters">
    <ul class="adding-post__tabs-list filters__list tabs__list">
        <?php
        foreach ($content_tabs as $content_tab): ?>
            <?php
            $is_active = $content_tab['active'] ?? false;
            $url = $content_tab['url'] ?? '';
            $type = $content_tab['type'] ?? '';
            $name = $content_tab['name'] ?? '';
            ?>
            <li class="adding-post__tabs-item filters__item">
                <a class="adding-post__tabs-link filters__button filters__button--photo
                    <?= $is_active ?
                    'filters__button--active tabs__item--active'
                    : '' ?> tabs__item button"
                    <?= !$is_active ? "href='$url'" : '' ?>>
                    <svg class="filters__icon" width="22" height="18">
                        <use xlink:href="#icon-filter-<?= $type ?>"></use>
                    </svg>
                    <span><?= $name ?></span>
                </a>
            </li>
        <?php
        endforeach; ?>
    </ul>
</div>
