<?php
/**
 * Общий шаблон карточки публикации
 *
 * @var string | null $card_modifier - модификатор карточки
 * @var array $post_card - ассоциативный массив с данными публикации
 */

list(
    'id' => $id,
    'title' => $title,
    'string_content' => $string_content,
    'text_content' => $text_content,
    'content_type' => $content_type,
    'author_login' => $author_login,
    'author_avatar' => $author_avatar,
    'created_at' => $created_at,
    'likes_count' => $likes_count,
    'comments_count' => $comments_count,
    'hashtags' => $hashtags,
    'is_liked' => $is_liked
    )
    = $post_card;
?>

<article class="<?= $card_modifier ? "${card_modifier}__post"
    : '' ?> post <?= "post-$content_type" ?>">
    <header class="post__header post__author">
        <a class="post__author-link" href="#" title="Автор">
            <div class="post__avatar-wrapper">
                <img class="post__author-avatar"
                     src="/<?= $author_avatar ?? AVATAR_PLACEHOLDER ?>"
                     alt="Аватар пользователя" width="60" height="60">
            </div>
            <div class="post__info">
                <b class="post__author-name"><?= $author_login ?></b>
                <time class="post__time" datetime="<?= format_iso_date_time(
                    $created_at
                ) ?>"><?= format_relative_time($created_at) ?> назад
                </time>
            </div>
        </a>
    </header>
    <div class="post__main">
        <h2><a href="post.php?post_id=<?= $id ?>"><?= $title ?></a></h2>
        <?= include_template(
            "common/post-card/content/$content_type.php",
            [
                'id' => $id,
                'text_content' => $text_content,
                'string_content' => $string_content,
            ]
        ) ?>
    </div>
    <footer class="post__footer post__indicators">
        <div class="post__buttons">
            <a class="post__indicator
             post__indicator--likes<?= $is_liked ? '-active' : '' ?>
             button" href="#" title="Лайк">
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
            <a class="post__indicator post__indicator--comments button" href="#"
               title="Комментарии">
                <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-comment"></use>
                </svg>
                <span><?= $comments_count ?></span>
                <span class="visually-hidden">количество комментариев</span>
            </a>
            <a class="post__indicator post__indicator--repost button" href="#"
               title="Репост">
                <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-repost"></use>
                </svg>
                <span>5</span>
                <span class="visually-hidden">количество репостов</span>
            </a>
        </div>
    </footer>
    <?= include_template(
        'common/post-card/hashtags.php',
        ['hashtags' => $hashtags]
    ) ?>
</article>
