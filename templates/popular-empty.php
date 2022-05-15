<?php
/**
 * Шаблон пустого состояния основного контента странцы 'Популярное'.
 * Используется в случае невозможности отображения карточек публикаци.
 * Вместо карточек публикаций отображает сообщение.
 *
 * @var string $popular_filters_content - разметка секции сортировки и
 * фильтрации по типу контента
 * @var string | null $title - заголовок сообщения
 * @var string $content - описание сообщения
 * @var string | null $link_url - URL ссылки для сообщения
 * @var string | null $link_description - описания ссылки для сообщения
 */

$withLink = isset($link_description) and isset($link_url);
?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= $popular_filters_content ?>
    <div class="popular__empty">
        <h2><?= $title ?></h2>
        <p><?= $content ?></p>
        <?php
        if ($withLink): ?>
            <a href="<?= $link_url ?>"><?= $link_description ?></a>
        <?php
        endif; ?>
    </div>
</div>
