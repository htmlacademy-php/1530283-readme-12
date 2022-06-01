<?php
/**
 * Шаблон блока с ошибкой валидации поля формы
 *
 * @var array $error - массивы с заголовком и описанием ошибки валидации
 */

?>

<div class="form__error-text">
    <h3 class="form__error-title"><?= $error['title'] ?? '' ?></h3>
    <p class="form__error-desc"><?= $error['description'] ?? '' ?></p>
</div>
