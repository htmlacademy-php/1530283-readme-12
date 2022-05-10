<?php

$string_content = isset($string_content) ? strip_tags($string_content) : '';
?>

<div class="post-photo__image-wrapper">
    <img src="img/<?= $string_content ?>" alt="Фото от пользователя" width="360"
         height="240">
</div>
