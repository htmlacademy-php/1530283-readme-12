<?php
/**
 * Шаблон карточки публикации для страницы 'Популярное'
 *
 * @var array $post_card - ассоциативный массив с данными публикации
 */

$id = $post_card['id'] ?? null;
$title =
    isset($post_card['title']) ? htmlspecialchars($post_card['title']) : '';
$string_content =
    isset($post_card['string_content']) ? htmlspecialchars(
        $post_card['string_content']
    ) : '';
$text_content =
    isset($post_card['text_content']) ? htmlspecialchars(
        $post_card['text_content']
    ) : '';
$content_type = $post_card['content_type'] ?? '';
$likes_count = $post_card['likes_count'] ?? 0;
$comments_count = $post_card['comments_count'] ?? 0;
$is_liked = $post_card['is_liked'] ?? false;
$created_at = $post_card['created_at'] ?? null;
$iso_date_time = $created_at ? format_iso_date_time($created_at) : '';
$relative_time = $created_at ? format_relative_time($created_at) : '';
$author = $post_card['author'] ?? [];
$author_id = $author['id'] ?? null;
$author_login =
    isset($author['login']) ? htmlspecialchars($author['login']) : '';
$author_avatar_url = $author['avatar_url'] ?? AVATAR_PLACEHOLDER;
?>
<article
        id="post-<?= $id ?>"
        class="popular__post post post-<?= $content_type ?>">
    <header class="post__header">
        <a href="post.php?post-id=<?= $id ?>">
            <h2><?= $title ?></h2>
        </a>
    </header>
    <div class="post__main">
        <?= include_template(
            "pages/popular/post-card/content/$content_type.php",
            [
                'id' => $id,
                'title' => $title,
                'text_content' => $text_content,
                'string_content' => $string_content,
            ]
        ) ?>
    </div>
    <footer class="post__footer">
        <div class="post__author">
            <a class="post__author-link"
               href="profile.php?user-id=<?= $author_id ?>"
               title="Автор">
                <div class="post__avatar-wrapper">
                    <img class="post__author-avatar"
                         src="/<?= $author_avatar_url ?>"
                         alt="Аватар пользователя" width="40" height="40">
                </div>
                <div class="post__info">
                    <b class="post__author-name"><?= $author_login ?></b>
                    <time class="post__time"
                          datetime="<?= $iso_date_time ?>"><?= $relative_time ?>
                        назад
                    </time>
                </div>
            </a>
        </div>
        <div class="post__indicators">
            <div class="post__buttons">
                <a class="post__indicator
                 post__indicator--likes<?= $is_liked ? '-active' : '' ?>
                 button"
                   href="like.php?post-id=<?= $id ?>" title="Лайк">
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
                   href="post.php?post-id=<?= $id ?>#comments"
                   title="Комментарии">
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
