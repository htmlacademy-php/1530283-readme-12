<?php
/**
 * Шаблон основного контента страницы добаления публикации
 *
 * @var string $content_tabs - разметка секции табов по типу контента
 * @var bool $content_fields - разметка полей контента публикации
 * @var string $title - скрытый заголовок формы
 * @var array $form_data - ассоциативный массив с данными полей формы
 * @var array $errors - ассоциативный массив с данными ошибок полей формы
 * @var bool $invalid - валидность формы
 * @var bool $with_photo_file - наличие секции для загрузки файла фото
 */

?>

<div class="page__main-section">
    <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить
            публикацию</h1>
    </div>
    <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
            <?= $content_tabs ?>
            <div class="adding-post__tab-content">
                <section
                        class="adding-post__photo tabs__content tabs__content--active">
                    <h2 class="visually-hidden"><?= $title ?></h2>
                    <form class="adding-post__form form" action="#"
                          method="post" enctype="multipart/form-data">
                        <div class="form__text-inputs-wrapper">
                            <div class="form__text-inputs">
                                <div class="adding-post__input-wrapper form__input-wrapper">
                                    <label class="adding-post__label form__label"
                                           for="title">Заголовок <span
                                                class="form__input-required">*</span></label>
                                    <div class="form__input-section <?= $errors['title']
                                        ? 'form__input-section--error' : '' ?>">
                                        <input class="adding-post__input form__input"
                                               id="title" type="text"
                                               name="title"
                                               value="<?= $form_data['title'] ??
                                                          '' ?>"
                                               placeholder="Введите заголовок">
                                        <button class="form__error-button button"
                                                type="button">!<span
                                                    class="visually-hidden">Информация об ошибке</span>
                                        </button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">
                                                <?= $errors['title']
                                                    ? $errors['title']['title']
                                                    : '' ?></h3>
                                            <p class="form__error-desc"><?= $errors['title']
                                                    ? $errors['title']['description']
                                                    : '' ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?= $content_fields ?>
                                <div class="adding-post__input-wrapper form__input-wrapper">
                                    <label class="adding-post__label form__label"
                                           for="tags">Теги</label>
                                    <div class="form__input-section">
                                        <input class="adding-post__input form__input"
                                               id="tags" type="text"
                                               name="tags"
                                               value="<?= $form_data['tags'] ??
                                                          '' ?>"
                                               placeholder="Введите теги">
                                        <button class="form__error-button button"
                                                type="button">!<span
                                                    class="visually-hidden">Информация об ошибке</span>
                                        </button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">
                                                Заголовок сообщения</h3>
                                            <p class="form__error-desc">Текст
                                                сообщения об ошибке, подробно
                                                объясняющий, что не так.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($invalid): ?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста,
                                        исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php
                                        foreach ($errors as $error): ?>
                                            <li class="form__invalid-item">
                                                <?= $error['title'] ?>.
                                                <?= $error['description'] ?>.
                                            </li>
                                        <?php
                                        endforeach; ?>
                                    </ul>
                                </div>
                            <?php
                            endif; ?>
                        </div>
                        <?php
                        if ($with_photo_file): ?>
                            <div class="adding-post__input-file-container form__input-container form__input-container--file">
                                <input class="visually-hidden"
                                       id="photo-file"
                                       type="file"
                                       name="photo-file"
                                       title=" ">
                                <div class="adding-post__input-file-wrapper form__input-file-wrapper"
                                     style="position: relative; display: inline-block">
                                    <button id="upload-button"
                                            class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button"
                                            type="button">
                                        <span>Выбрать фото</span>
                                        <svg class="adding-post__attach-icon form__attach-icon"
                                             width="10" height="20">
                                            <use xlink:href="#icon-attach"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
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
                        <?php
                        endif; ?>
                        <div class="adding-post__buttons">
                            <button class="adding-post__submit button button--main"
                                    type="submit">Опубликовать
                            </button>
                            <a class="adding-post__close" href="index.php">Закрыть</a>
                        </div>
                    </form>
                </section>
            </div>
        </
        >
    </div>
</div>
