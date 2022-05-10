<?php

list(
    'id' => $id,
    'title' => $title,
    'content_type' => $content_type,
    'string_content' => $string_content,
    'text_content' => $text_content,
    'author_login' => $author_login,
    'author_avatar' => $author_avatar,
    'created_at' => $created_at,
    'likes_count' => $likes_count,
    'comments_count' => $comments_count,
    )
    = $post_card;

$post_content_decorators = [
    'quote' => function () use ($text_content, $string_content) {
        return include_template(
            'partials/post-card/quote-content.php',
            [
                'text_content'   => $text_content,
                'string_content' => $string_content,
            ]
        );
    },
    'text'  => function () use ($text_content, $string_content) {
        return include_template(
            'partials/post-card/text-content.php',
            [
                'text_content' => $text_content,
            ]
        );
    },
    'photo' => function () use ($string_content) {
        return include_template(
            'partials/post-card/photo-content.php',
            [
                'string_content' => $string_content,
            ]
        );
    },
    'link'  => function () use ($string_content) {
        return include_template(
            'partials/post-card/link-content.php',
            [
                'string_content' => $string_content,
            ]
        );
    },
];
?>
<article class="popular__post post post-<?= $content_type ?>">
    <header class="post__header">
        <a href="post.php?post_id=<?= $id ?>">
            <h2><?= strip_tags($title) ?></h2>
        </a>
    </header>
    <div class="post__main">
        <?php
        if (is_callable($post_content_decorators[$content_type])): ?>
            <?= $post_content_decorators[$content_type]() ?>
        <?php
        endif; ?>
    </div>
    <footer class="post__footer">
        <div class="post__author">
            <a class="post__author-link" href="#" title="Автор">
                <div class="post__avatar-wrapper">
                    <img class="post__author-avatar"
                         src="img/<?= $author_avatar ?>"
                         alt="Аватар пользователя">
                </div>
                <div class="post__info">
                    <b class="post__author-name"><?= strip_tags(
                            $author_login
                        ) ?></b>
                    <time class="post__time" datetime="<?= format_iso_date_time(
                        $created_at
                    ) ?>"><?= format_relative_time($created_at) ?></time>
                </div>
            </a>
        </div>
        <div class="post__indicators">
            <div class="post__buttons">
                <a class="post__indicator post__indicator--likes button"
                   href="#" title="Лайк">
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
                   href="#" title="Комментарии">
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
