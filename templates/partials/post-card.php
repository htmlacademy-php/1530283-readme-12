<?php
list(
    'title' => $title,
    'type' => $type,
    'content' => $content,
    'user_name' => $user_name,
    'avatar' => $avatar,
    'date' => $date,
    ) = $post_card;

$post_content_decorators = [
    'quote' => 'decorate_post_quote_content',
    'text' => 'decorate_post_text_content',
    'photo' => 'decorate_post_photo_content',
    'link' => 'decorate_post_link_content',
];
?>
<article class="popular__post post post-<?= $type ?>">
    <header class="post__header">
        <h2><?= strip_tags($title) ?></h2>
    </header>
    <div class="post__main">
        <?php if (is_callable($post_content_decorators[$type])): ?>
            <?= $post_content_decorators[$type]($content) ?>
        <?php endif; ?>
    </div>
    <footer class="post__footer">
        <div class="post__author">
            <a class="post__author-link" href="#" title="Автор">
                <div class="post__avatar-wrapper">
                    <img class="post__author-avatar" src="img/<?= $avatar ?>" alt="Аватар пользователя">
                </div>
                <div class="post__info">
                    <b class="post__author-name"><?= strip_tags($user_name) ?></b>
                    <time class="post__time" datetime="<?= format_iso_date_time($date) ?>"><?= $date ?></time>
                </div>
            </a>
        </div>
        <div class="post__indicators">
            <div class="post__buttons">
                <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                    <svg class="post__indicator-icon" width="20" height="17">
                        <use xlink:href="#icon-heart"></use>
                    </svg>
                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                        <use xlink:href="#icon-heart-active"></use>
                    </svg>
                    <span>0</span>
                    <span class="visually-hidden">количество лайков</span>
                </a>
                <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                    <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-comment"></use>
                    </svg>
                    <span>0</span>
                    <span class="visually-hidden">количество комментариев</span>
                </a>
            </div>
        </div>
    </footer>
</article>
