<?php

/**
 * Шаблон контента карточки публикации цитаты для страницы 'Популярное'
 *
 * @var string $text_content - текстовый контент публикации
 * @var string $string_content - строковый контент публикации
 */

$text_content   = isset($text_content) ? htmlspecialchars($text_content) : '';
$string_content = isset($string_content) ? htmlspecialchars($string_content)
    : '';
?>

<blockquote>
    <p><?= $text_content ?></p>
    <cite><?= $string_content ?></cite>
</blockquote>
