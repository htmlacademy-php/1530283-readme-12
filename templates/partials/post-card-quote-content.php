<?php
$text_content = isset($text_content) ? htmlspecialchars($text_content) : '';
$string_content = isset($string_content) ? htmlspecialchars($string_content) : '';
?>

<blockquote>
    <p><?= $text_content ?></p>
    <cite><?= $string_content ?></cite>
</blockquote>
