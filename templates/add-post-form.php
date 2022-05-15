<?php
/**
 * Шаблон основного контента страницы добаления публикации
 *
 * @var string $content_filters - разметка секции табов по типу контента
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
            <?= $content_filters ?>
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
                                <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                                    <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                                        <input class="adding-post__input-file form__input-file"
                                               id="photo-file"
                                               type="file"
                                               name="photo-file"
                                               title=" ">
                                        <div class="form__file-zone-text">
                                            <span>Перетащите фото сюда</span>
                                        </div>
                                    </div>
                                    <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button"
                                            type="button">
                                        <span>Выбрать фото</span>
                                        <svg class="adding-post__attach-icon form__attach-icon"
                                             width="10" height="20">
                                            <use xlink:href="#icon-attach"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
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
