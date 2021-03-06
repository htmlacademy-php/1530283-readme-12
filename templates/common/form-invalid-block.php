<?php
/**
 * Шаблон блока с ошибками валидации формы
 *
 * @var array $errors - массив с ошибками формы
 */

?>

<div class="form__invalid-block">
    <b class="form__invalid-slogan">Пожалуйста,
        исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php
        foreach ($errors as $error): ?>
            <li class="form__invalid-item">
                <?= $error['title'] ?? '' ?>.
                <?= $error['description'] ?? '' ?>.
            </li>
        <?php
        endforeach; ?>
    </ul>
</div>
