<?php
/**
 * Шаблон секции табов для страницы профиля пользователя
 *
 * @var array $tabs - массив с данными табов
 */

?>

<div class="profile__tabs filters">
    <b class="profile__tabs-caption filters__caption">Показать:</b>
    <ul class="profile__tabs-list filters__list tabs__list">
        <?php
        foreach ($tabs as $tab): ?>
            <?php
            $is_active = $tab['active'] ?? false;
            $url = $tab['url'] ?? '';
            ?>
            <li class="profile__tabs-item filters__item">
                <a class="
                    profile__tabs-link button
                    filters__button tabs__item
                    <?= $is_active
                    ? 'filters__button--active tabs__item--active' : '' ?>"
                    <?= $is_active ? '' : "href='$url'" ?>
                ><?= $tab['label'] ?? '' ?></a>
            </li>
        <?php
        endforeach; ?>
    </ul>
</div>
