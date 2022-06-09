<?php

require_once 'utils/helpers.php';

/**
 * Шаблон основного контента страницы просмотра публикации.
 *
 * @var array $post - ассоциативный массив с данными публикации
 * @var string $post_content - разметка секции контента публикации
 * @var string $author_content - разметка секции автора публикации
 * @var string $comments_content - разметка секции с комментариями
 */

list(
    'id' => $id,
    'title' => $title,
    'likes_count' => $likes_count,
    'comments_count' => $comments_count,
    'views_count' => $views_count,
    'is_liked' => $is_liked
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
                        <a class="post__indicator
                           post__indicator--likes<?= $is_liked ? '-active' : ''?>
                           button"
                           href="like.php?post-id=<?= $id ?>" title="Лайк">
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
                           href="#сomments" title="Комментарии">
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
                <?= $comments_content ?>
            </div>
            <?= $author_content ?>
        </div>
    </section>
</div>
