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
            <li class="adding-post__tabs-item filters__item">
                <a class="adding-post__tabs-link filters__button filters__button--photo <?= $content_tab['active']
                    ? 'filters__button--active tabs__item--active'
                    : '' ?> tabs__item button" <?= !$content_tab['active']
                    ? 'href="' . $content_tab['url'] . '"'
                    : '' ?>>
                    <svg class="filters__icon" width="22" height="18">
                        <use xlink:href="#icon-filter-<?= $content_tab['type'] ?>"></use>
                    </svg>
                    <span><?= $content_tab['name'] ?></span>
                </a>
            </li>
        <?php
        endforeach; ?>
    </ul>
</div>
