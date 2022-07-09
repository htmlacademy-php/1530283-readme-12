<?php
/**
 * Шаблон основного контента страницы авторизации
 *
 * @var array $form_data - ассоциативный массив с данными полей формы
 * @var array $errors - ассоциативный массив с данными ошибок полей формы
 * @var string $invalid_block_content - разметка блока ошибок валидации
 */

?>

<div class="container">
    <h1 class="page__title page__title--login">Вход</h1>
</div>
<section class="login container">
    <h2 class="visually-hidden">Форма авторизации</h2>
    <form class="login__form form" action="#" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">

                <div class="login__input-wrapper form__input-wrapper">
                    <label class="login__label form__label" for="login-email">Электронная
                        почта</label>
                    <div class="form__input-section <?= isset($errors['email'])
                        ? 'form__input-section--error' : '' ?>">
                        <input class="login__input form__input" id="login-email"
                               type="text" name="email"
                               value="<?= $form_data['email'] ?? '' ?>"
                               placeholder="Укажите эл.почту">
                        <button class="form__error-button button" type="button">
                            !<span
                                    class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['email'] ?? []]
                        ) ?>
                    </div>
                </div>
                <div class="login__input-wrapper form__input-wrapper">
                    <label class="login__label form__label"
                           for="login-password">Пароль</label>
                    <div class="form__input-section
                      <?= isset($errors['password'])
                        ? 'form__input-section--error' : '' ?>">
                        <input class="login__input form__input"
                               id="login-password"
                               type="password" name="password"
                               value="<?= $form_data['password'] ?? '' ?>"
                               placeholder="Введите пароль">
                        <button class="form__error-button button button--main"
                                type="button">!<span class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['password'] ?? []]
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $invalid_block_content ?? '' ?>
        </div>
        <button class="login__submit button button--main" type="submit">
            Отправить
        </button>
    </form>
</section>
