<?php
/**
 * Шаблон списка хэштегов поста
 *
 * @var array $hashtags - массив хэштегов
 */

?>

<ul class="post__tags">
    <?php
    foreach ($hashtags as $hashtag): ?>
        <li>
            <a href="<?= 'search.php?query=' . urlencode(
                "#$hashtag"
            ) ?>">#<?= htmlspecialchars($hashtag) ?></a>
        </li>
    <?php
    endforeach; ?>
</ul>
