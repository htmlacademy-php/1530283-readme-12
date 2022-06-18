<?php
/**
 * Шаблон служебного сообщения вместо основного контента страницы.
 *
 * @var string | null $title - Заголовок сообщения ('Ошибка' - по умолчанию).
 * @var string $content - описание сообщения об ощибке
 * @var string | null $link_url - URL ссылки для сообщения
 * @var string | null $link_description - описания ссылки для сообщения
 * @var bool | null $comments - блок комментариев
 */

if (!isset($title)) {
    $title = 'Ошибка';
}

$withLink = isset($link_description) and isset($link_url);
?>

<div
    <?= $comments ? 'id="comments"' : '' ?>
        class="container container--empty
    <?= $comments ? 'container--comments' : '' ?>">
    <h2><?= $title ?></h2>
    <p><?= $content ?></p>
    <?php
    if ($withLink): ?>
        <a href="<?= $link_url ?>"><?= $link_description ?></a>
    <?php
    endif; ?>
</div>
