<?php
/**
 * Шаблон полей контента для формы добавления публикации текста.
 *
 * @var array $form_data - ассоциативный массив с данными полей формы
 * @var array $errors - ассоциативный массив с данными ошибок полей формы
 */

?>
<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label"
           for="text-content">Текст поста <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?= isset($errors['text_content'])
        ? 'form__input-section--error' : '' ?>">
        <textarea class="adding-post__textarea form__textarea form__input"
                  id="text-content" name="text-content"
                  placeholder="Введите текст публикации"><?= $form_data['text_content']
                                                             ?? '' ?></textarea>
        <button class="form__error-button button"
                type="button">!<span
                    class="visually-hidden">Информация об ошибке</span>
        </button>
        <?= include_template(
            'common/form-error-text.php',
            ['error' => $errors['text_content'] ?? []]
        ) ?>
    </div>
</div>
