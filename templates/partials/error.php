<?php

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
