<?php
/**
 * Шаблон формы добавления сообщения к текущему разговору.
 *
 * @var array $user - данные текущего пользователя
 * @var array $form_data - данные формы
 * @var array $errors - ошибки валиадции
 */

?>

<form class="comments__form form" action="#" method="post">
    <div class="comments__my-avatar">
        <img class="comments__picture"
             src="<?= $user['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
             alt="Аватар пользователя">
    </div>
    <div class="form__input-section form__input-section--error">
        <textarea name="content"
                  class="comments__textarea form__textarea form__input"
                  placeholder="Ваше сообщение"></textarea>
        <label class="visually-hidden">Ваше сообщение</label>
        <button class="form__error-button button" type="button">!
        </button>
        <div class="form__error-text">
            <h3 class="form__error-title">Ошибка валидации</h3>
            <p class="form__error-desc">Это поле обязательно к
                заполнению</p>
        </div>
    </div>
    <button class="comments__submit button button--green"
            type="submit">Отправить
    </button>
</form>
