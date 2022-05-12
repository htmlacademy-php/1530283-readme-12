<?php

require_once 'helpers.php';

$string_content = strip_tags($string_content);
?>

<div class="post-details__image-wrapper post-photo__image-wrapper">
    <?= embed_youtube_video($string_content); ?>
</div>
