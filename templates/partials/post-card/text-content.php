<?php

require_once 'constants.php';
require_once 'functions.php';

/**
 * Шаблон контента карточки публикации текста для страницы 'Популярное'
 *
 * @var string $text_content - текстовый контент публикации
 */

$text_content = isset($text_content) ? htmlspecialchars($text_content) : '';
$cropped_text_content = crop_text(
    $text_content,
    MAX_POST_CARD_TEXT_CONTENT_LENGTH
);
$is_cropped = $text_content !== $cropped_text_content;
?>

<?php
if ($is_cropped): ?>
    <p><?= $cropped_text_content ?></p>
    <a class='post-text__more-link' href='#'>Читать далее</a>
<?php
else: ?>
    <p><?= $text_content ?></p>
<?php
endif; ?>
