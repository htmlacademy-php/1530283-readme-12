<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label"
           for="text-content">Текст поста <span
                class="form__input-required">*</span></label>
    <div class="form__input-section">
        <textarea class="adding-post__textarea form__textarea form__input"
                  id="text-content" name="text-content"
                  placeholder="Введите текст публикации"><?= $form_data['text_content']
                                                             ?? '' ?></textarea>
        <button class="form__error-button button"
                type="button">!<span
                    class="visually-hidden">Информация об ошибке</span>
        </button>
        <div class="form__error-text">
            <h3 class="form__error-title">Заголовок сообщения</h3>
            <p class="form__error-desc">Текст сообщения об ошибке, подробно
                объясняющий, что не так.</p>
        </div>
    </div>
</div>
