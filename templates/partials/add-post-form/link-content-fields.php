<div class="adding-post__textarea-wrapper form__input-wrapper">
    <label class="adding-post__label form__label"
           for="string-content">Ссылка <span
                class="form__input-required">*</span></label>
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
        <div class="form__error-text">
            <h3 class="form__error-title">
                <?= $errors['string_content']
                    ? $errors['string_content']['title'] : '' ?></h3>
            <p class="form__error-desc"><?= $errors['string_content']
                    ? $errors['string_content']['description'] : '' ?></p>
        </div>
    </div>
</div>
