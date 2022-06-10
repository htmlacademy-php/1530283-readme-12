<?php

/**
 * Шаблон секции комментариев к публикации.
 *
 * Ограничения:
 * Для отображения формы добавления комментария необходимо передать в шаблон -
 * данные пользователя, данные формы и массив ошибок валидации
 *
 * @var array $comments - массив с комментариями к публикации
 * @var int $comments_count - числов комментариев
 * @var string | null $expand_comments_url - ссылка показа полного списка
 * комментариев
 * @var array | null $user -  данные пользователя
 * @var array | null $form_data - данные формы
 * @var array | null $errors - ошибки валиадции
 */

$with_form = is_array($user) && is_array($form_data) && is_array($errors);
?>

<div class="comments">
    <?php
    if ($with_form): ?>
        <form class="comments__form form" action="#comments" method="post">
            <div class="comments__my-avatar">
                <img class="comments__picture"
                     src="/<?= $user['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя">
            </div>
            <input name="post-id" value="<?= $form_data['post_id'] ?>"
                   type="hidden"/>
            <input name="post-author-id"
                   value="<?= $form_data['post_author_id'] ?>"
                   type="hidden"/>
            <div class="form__input-section
             <?= $errors['content'] ? 'form__input-section--error' : '' ?>">
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
    <?php
    endif; ?>
    <div class="comments__list-wrapper">
        <ul id="comments" class="comments__list">
            <?php
            foreach ($comments as $comment): ?>
                <?= include_template(
                    'common/comment-item.php',
                    ['comment' => $comment]
                ) ?>
            <?php
            endforeach; ?>
        </ul>
        <?php
        if ($expand_comments_url): ?>
            <a class="comments__more-link" href="<?= $expand_comments_url ?>">
                <span>Показать все комментарии</span>
                <sup class="comments__amount"><?= $comments_count ?></sup>
            </a>
        <?php
        endif; ?>
    </div>
</div>
