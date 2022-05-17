<?php

/**
 * Шаблон служебного сообщения вместо основного контента страницы.
 *
 * @var string | null $title - Заголовок сообщения ('Ошибка' - по умолчанию).
 * @var string $content - описание сообщения об ощибке
 * @var string | null $link_url - URL ссылки для сообщения
 * @var string | null $link_description - описания ссылки для сообщения
 */

if ( ! isset($title)) {
    $title = 'Ошибка';
}

$withLink = isset($link_description) and isset($link_url);
?>
<section class="page__main page__main--empty">
    <div class="container">
        <h1><?= $title ?></h1>
        <p><?= $content ?></p>
        <?php
        if ($withLink): ?>
            <a href="<?= $link_url ?>"><?= $link_description ?></a>
        <?php
        endif; ?>
    </div>
</section>
