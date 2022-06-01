<?php
/**
 * Шаблон полей контента для формы добавления публикации фото.
 *
 * @var array $form_data - ассоциативный массив с данными полей формы
 * @var array $errors - ассоциативный массив с данными ошибок полей формы
 */

?>
<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label"
           for="string-content">Ссылка из интернета</label>
    <div class="form__input-section <?= $errors['string_content']
        ? 'form__input-section--error' : '' ?>">
        <input class="adding-post__input form__input"
               id="string-content" type="text"
               name="string-content"
               value="<?= $form_data['string_content'] ?? '' ?>"
               placeholder="Введите ссылку">
        <button class="form__error-button button"
                type="button">!<span
                    class="visually-hidden">Информация об ошибке</span>
        </button>
        <?= include_template(
            'common/form-error-text.php',
            ['error' => $errors['string_content'] ?? []]
        ) ?>
    </div>
</div>
