<?php
/**
 * Шаблон карточки публикации для страницы 'Популярное'
 *
 * @var array $post_card - ассоциативный массив с данными публикации
 */

list(
    'id' => $id,
    'title' => $title,
    'string_content' => $string_content,
    'text_content' => $text_content,
    'content_type' => $content_type,
    'author_id' => $author_id,
    'author_login' => $author_login,
    'author_avatar' => $author_avatar,
    'created_at' => $created_at,
    'likes_count' => $likes_count,
    'comments_count' => $comments_count,
    'is_liked' => $is_liked
    )
    = $post_card;
?>
<article
        id="post-<?= $id ?>"
        class="popular__post post post-<?= $content_type ?>">
    <header class="post__header">
        <a href="post.php?post_id=<?= $id ?>">
            <h2><?= strip_tags($title) ?></h2>
        </a>
    </header>
    <div class="post__main">
        <?= include_template(
            "pages/popular/post-card/content/$content_type.php",
            [
                'id' => $id,
                'text_content' => $text_content,
                'string_content' => $string_content,
            ]
        ) ?>
    </div>
    <footer class="post__footer">
        <div class="post__author">
            <a class="post__author-link"
               href="profile.php?user_id=<?= $author_id ?>"
               title="Автор">
                <div class="post__avatar-wrapper">
                    <img class="post__author-avatar"
                         src="/<?= $author_avatar ??
                                   'img/icon-input-user.svg' ?>"
                         alt="Аватар пользователя">
                </div>
                <div class="post__info">
                    <b class="post__author-name"><?= strip_tags(
                            $author_login
                        ) ?></b>
                    <time class="post__time" datetime="<?= format_iso_date_time(
                        $created_at
                    ) ?>"><?= format_relative_time($created_at) ?> назад
                    </time>
                </div>
            </a>
        </div>
        <div class="post__indicators">
            <div class="post__buttons">
                <a class="post__indicator
                 post__indicator--likes<?= $is_liked ? '-active' : '' ?>
                 button"
                   href="like.php?post_id=<?= $id ?>" title="Лайк">
                    <svg class="post__indicator-icon" width="20" height="17">
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
                   href="post.php?post_id=<?= $id ?>#comments" title="Комментарии">
                    <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-comment"></use>
                    </svg>
                    <span><?= $comments_count ?></span>
                    <span class="visually-hidden">количество комментариев</span>
                </a>
            </div>
        </div>
    </footer>
</article>
