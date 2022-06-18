<?php
/**
 * Шаблон контента карточки публикации цитаты для страницы 'Популярное'
 *
 * @var string $text_content - текстовый контент публикации
 * @var string $string_content - строковый контент публикации
 */

?>

<blockquote>
    <p><?= htmlspecialchars($text_content) ?></p>
    <cite><?= htmlspecialchars($string_content) ?></cite>
</blockquote>
