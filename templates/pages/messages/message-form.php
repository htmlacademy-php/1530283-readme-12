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
    <div class="form__input-section
     <?= isset($errors['content']) ? 'form__input-section--error' : '' ?>">
        <input type="hidden" name="conversation-id"
               value="<?= $form_data['conversation_id'] ?? '' ?>">
        <textarea name="content"
                  class="comments__textarea form__textarea form__input"
                  placeholder="Ваше сообщение"><?= $form_data['content'] ??
                                                   '' ?></textarea>
        <label class="visually-hidden">Ваше сообщение</label>
        <button class="form__error-button button" type="button">!
        </button>
        <?= include_template(
            'common/form-error-text.php',
            ['error' => $errors['content'] ?? []]
        ) ?>
    </div>
    <button class="comments__submit button button--green"
            type="submit">Отправить
    </button>
</form>
