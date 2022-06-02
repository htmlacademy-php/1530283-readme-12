<?php

require_once 'utils/helpers.php';

/**
 * Шаблон основного контента страницы просмотра публикации.
 *
 * @var array $post - ассоциативный массив с данными публикации
 * @var string $post_content - разметка секции контента публикации
 * @var string $author_content - разметка секции автора публикации
 * @var array $comments - массив с комментариями к публикации
 */

list(
    'id' => $id,
    'title' => $title,
    'likes_count' => $likes_count,
    'comments_count' => $comments_count,
    'views_count' => $views_count,
    )
    = $post;

?>

<div class="container">
    <h1 class="page__title page__title--publication"><?= $title ?></h1>
    <section class="post-details">
        <h2 class="visually-hidden">Публикация</h2>
        <div class="post-details__wrapper post-photo">
            <div class="post-details__main-block post post--details">
                <?= $post_content ?>
                <div class="post__indicators">
                    <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button"
                           href="#" title="Лайк">
                            <svg class="post__indicator-icon" width="20"
                                 height="17">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                            <svg class="post__indicator-icon post__indicator-icon--like-active"
                                 width="20" height="17">
                                <use xlink:href="#icon-heart-active"></use>
                            </svg>
                            <span><?= $likes_count ?></span>
                            <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--comments button"
                           href="#" title="Комментарии">
                            <svg class="post__indicator-icon" width="19"
                                 height="17">
                                <use xlink:href="#icon-comment"></use>
                            </svg>
                            <span><?= $comments_count ?></span>
                            <span class="visually-hidden">количество комментариев</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button"
                           href="#" title="Репост">
                            <svg class="post__indicator-icon" width="19"
                                 height="17">
                                <use xlink:href="#icon-repost"></use>
                            </svg>
                            <span>5</span>
                            <span class="visually-hidden">количество репостов</span>
                        </a>
                    </div>
                    <span class="post__view"><?= $views_count ?> <?= get_noun_plural_form(
                            $views_count,
                            'просмотр',
                            'просмотра',
                            'просмотров'
                        ) ?></span>
                </div>
                <?= include_template(
                    'common/post-card/hashtags.php',
                    ['hashtags' => $post['hashtags']]
                ) ?>
                <div class="comments">
                    <form class="comments__form form" action="#" method="post">
                        <div class="comments__my-avatar">
                            <img class="comments__picture"
                                 src="/img/userpic-medium.jpg"
                                 alt="Аватар пользователя">
                        </div>
                        <div class="form__input-section form__input-section--error">
                            <textarea
                                    class="comments__textarea form__textarea form__input"
                                    placeholder="Ваш комментарий"></textarea>
                            <label class="visually-hidden">Ваш
                                комментарий</label>
                            <button class="form__error-button button"
                                    type="button">!
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка
                                    валидации</h3>
                                <p class="form__error-desc">Это поле обязательно
                                    к заполнению</p>
                            </div>
                        </div>
                        <button class="comments__submit button button--green"
                                type="submit">Отправить
                        </button>
                    </form>
                    <div class="comments__list-wrapper">
                        <ul class="comments__list">
                            <?php
                            foreach ($comments as $comment): ?>
                                <?= include_template(
                                    'pages/post-details/comment.php',
                                    [
                                        'comment' => $comment
                                    ]
                                ) ?>
                            <?php
                            endforeach; ?>
                        </ul>
                        <a class="comments__more-link" href="#">
                            <span>Показать все комментарии</span>
                            <sup class="comments__amount"><?= $comments_count ?></sup>
                        </a>
                    </div>
                </div>
            </div>
            <?= $author_content ?>
        </div>
    </section>
</div>
