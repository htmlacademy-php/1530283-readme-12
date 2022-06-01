<?php
/**
 * Шаблон основного контента страницы регистрации
 *
 * @var array $form_data - ассоциативный массив с данными полей формы
 * @var array $errors - ассоциативный массив с данными ошибок полей формы
 * @var string $invalid_block_content - разметка блока ошибок валидации
 */

?>

<div class="container">
    <h1 class="page__title page__title--registration">Регистрация</h1>
</div>
<section class="registration container">
    <h2 class="visually-hidden">Форма регистрации</h2>
    <form class="registration__form form" action="#" method="post"
          enctype="multipart/form-data">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label"
                           for="registration-email">Электронная почта <span
                                class="form__input-required">*</span></label>
                    <div class="form__input-section <?= $errors['email']
                        ? 'form__input-section--error' : '' ?>">
                        <input class="registration__input form__input"
                               id="registration-email" type="text" name="email"
                               value="<?= $form_data['email'] ??
                                          '' ?>"
                               placeholder="Укажите эл.почту">
                        <button class="form__error-button button" type="button">
                            !<span class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['email'] ?? []]
                        ) ?>
                    </div>
                </div>
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label"
                           for="registration-login">Логин <span
                                class="form__input-required">*</span></label>
                    <div class="form__input-section <?= $errors['login']
                        ? 'form__input-section--error' : '' ?>">
                        <input class="registration__input form__input"
                               id="registration-login" type="text" name="login"
                               value="<?= $form_data['login'] ??
                                          '' ?>"
                               placeholder="Укажите логин">
                        <button class="form__error-button button" type="button">
                            !<span class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['login'] ?? []]
                        ) ?>
                    </div>
                </div>
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label"
                           for="registration-password">Пароль<span
                                class="form__input-required">*</span></label>
                    <div class="form__input-section <?= $errors['password']
                        ? 'form__input-section--error' : '' ?>">
                        <input class="registration__input form__input"
                               id="registration-password" type="password"
                               value="<?= $form_data['password'] ??
                                          '' ?>"
                               name="password" placeholder="Придумайте пароль">
                        <button class="form__error-button button" type="button">
                            !<span class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['password'] ?? []]
                        ) ?>
                    </div>
                </div>
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label"
                           for="registration-password-repeat">Повтор пароля<span
                                class="form__input-required">*</span></label>
                    <div class="form__input-section <?= $errors['password_repeat']
                        ? 'form__input-section--error' : '' ?>">
                        <input class="registration__input form__input"
                               id="registration-password-repeat" type="password"
                               name="password-repeat"
                               value="<?= $form_data['password_repeat'] ??
                                          '' ?>"
                               placeholder="Повторите пароль">
                        <button class="form__error-button button" type="button">
                            !<span class="visually-hidden">Информация об ошибке</span>
                        </button>
                        <?= include_template(
                            'common/form-error-text.php',
                            ['error' => $errors['password_repeat'] ?? []]
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $invalid_block_content  ?>
        </div>
        <div class="registration__input-file-container form__input-container form__input-container--file">
            <input class="registration__input-file form__input-file"
                   id="photo-file" type="file" name="photo-file"
                   title=" ">
            <div class="registration__input-file-wrapper form__input-file-wrapper">
                <button id="upload-button"
                        class="registration__input-file-button form__input-file-button button"
                        type="button">
                    <span>Выбрать фото</span>
                    <svg class="registration__attach-icon form__attach-icon"
                         width="10" height="20">
                        <use xlink:href="#icon-attach"></use>
                    </svg>
                </button>
            </div>
            <div class="registration__file form__file dropzone-previews">
                <div id="upload-preview-container"
                     class="dz-preview dz-file-preview">
                    <div class="adding-post__image-wrapper form__file-wrapper">
                        <img id="upload-preview-image"
                             class="form__image"
                             src=""
                             alt="" data-dz-thumbnail></div>
                    <div class="adding-post__file-data form__file-data">
                                            <span id="upload-file-name"
                                                  class="adding-post__file-name form__file-name dz-filename"
                                                  data-dz-name>Имя файла</span>
                        <button id="upload-remove-button"
                                class="adding-post__delete-button form__delete-button button"
                                type="button"
                                data-dz-remove>
                            <span>Удалить</span>
                            <svg class="adding-post__delete-icon form__delete-icon"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 18 18"
                                 width="12" height="12">
                                <path d="M18 1.3L16.7 0 9 7.7 1.3 0 0 1.3 7.7 9 0 16.7 1.3 18 9 10.3l7.7 7.7 1.3-1.3L10.3 9z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button class="registration__submit button button--main" type="submit">
            Отправить
        </button>
    </form>
</section>
