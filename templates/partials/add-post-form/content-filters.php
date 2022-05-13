<div class="adding-post__tabs filters">
    <ul class="adding-post__tabs-list filters__list tabs__list">
        <?php
        foreach ($content_filters as $content_filter): ?>
            <li class="adding-post__tabs-item filters__item">
                <a class="adding-post__tabs-link filters__button filters__button--photo <?= $content_filter['active']
                    ? 'filters__button--active tabs__item--active'
                    : '' ?> tabs__item button" href="<?= $content_filter['url'] ?>">
                    <svg class="filters__icon" width="22" height="18">
                        <use xlink:href="#icon-filter-<?= $content_filter['icon'] ?>"></use>
                    </svg>
                    <span><?= $content_filter['name'] ?></span>
                </a>
            </li>
        <?php
        endforeach; ?>
    </ul>
</div>
