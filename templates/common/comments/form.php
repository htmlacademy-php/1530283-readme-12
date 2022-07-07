<?php
/**
 * Шаблон формы добавления комментария для секции комментариев к публикации
 *
 * @var array $user -  данные пользователя
 * @var array $form_data - данные формы
 * @var array $errors - ошибки валиадции
 */

?>

<form id="comments-form" class="comments__form form" action="#comments-form"
      method="post">
    <div class="comments__my-avatar">
        <img class="comments__picture"
             src="/<?= $user['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
             alt="Аватар пользователя">
    </div>
    <input name="post-id" value="<?= $form_data['post_id'] ?? '' ?>"
           type="hidden"/>
    <input name="post-author-id"
           value="<?= $form_data['post_author_id'] ?? '' ?>"
           type="hidden"/>
    <div class="form__input-section
             <?= isset($errors['content']) ?
               'form__input-section--error' : '' ?>">
                <textarea
                        name="content"
                        class="comments__textarea form__textarea form__input"
                        placeholder="Ваш комментарий"><?= $form_data['content']
                                                          ?? '' ?></textarea>
        <label class="visually-hidden">Ваш
            комментарий</label>
        <button class="form__error-button button"
                type="button">!
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
