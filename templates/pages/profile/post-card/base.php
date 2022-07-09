<?php
/**
 * Общий шаблон карточки публикации для страницы профиля пользователя
 *
 * @var string | null $card_modifier - модификатор карточки
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
$author = $post_card['author'] ?? [];
$likes_count = $post_card['likes_count'] ?? 0;
$comments_count = $post_card['comments_count'] ?? 0;
$reposts_count = $post_card['reposts_count'] ?? 0;
$hashtags = $post_card['hashtags'] ?? [];
$is_liked = $post_card['is_liked'] ?? false;
$is_own = $post_card['is_own'] ?? false;
$original_post = $post_card['original_post'] ?? null;
$created_at = $post_card['created_at'] ?? null;
$comments_list_content = $post_card['comments_list_content'] ?? '';
$comments_form_content = $post_card['comments_form_content'] ?? '';
?>

<article id="post-<?= $id ?>"
         class="<?= isset($card_modifier) && $card_modifier
             ? "${card_modifier}__post" : '' ?>
         post <?= "post-$content_type" ?>">
    <header class="post__header">
        <div class="post__author">
            <?php
            if ($original_post): ?>
                <a class="post__author-link"
                   href="profile.php?user-id=<?= $original_post['author_id'] ??
                                                 '' ?>"
                   title="Автор">
                    <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                        <img class="post__author-avatar"
                             src="/<?= $original_post['author_avatar_url'] ??
                                       AVATAR_PLACEHOLDER ?>"
                             alt="Аватар пользователя" width="60" height="60">
                    </div>
                    <div class="post__info">
                        <b class="post__author-name">Репост:
                            <?= isset($original_post['author_login'])
                                ? htmlspecialchars(
                                    $original_post['author_login']
                                ) : '' ?></b>
                        <time class="post__time"
                              datetime="<?= isset($original_post['created_at'])
                                  ? format_iso_date_time(
                                      $original_post['created_at']
                                  )
                                  : '' ?>"><?= isset($original_post['created_at'])
                                ? format_relative_time(
                                    $original_post['created_at']
                                ) : '' ?>
                            назад
                        </time>
                    </div>
                </a>
            <?php
            endif; ?>
        </div>
    </header>
    <div class="post__main">
        <h2><a href="post.php?post-id=<?= $id ?>"><?= $title ?></a></h2>
        <?= include_template(
            "common/post-card/content/$content_type.php",
            [
                'id' => $id,
                'title' => $title,
                'text_content' => $text_content,
                'string_content' => $string_content,
            ]
        ) ?>
    </div>
    <footer class="post__footer post__indicators">
        <div class="post__buttons">
            <a class="post__indicator
             post__indicator--likes<?= $is_liked ? '-active' : '' ?>
             button" href="like.php?post-id=<?= $id ?>" title="Лайк">
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
            <a class="post__indicator post__indicator--repost button"
                <?= !$is_own ? "href=\"repost.php?post-id=$id\"" : '' ?>
               title="Репост">
                <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-repost"></use>
                </svg>
                <span><?= $reposts_count ?></span>
                <span class="visually-hidden">количество репостов</span>
            </a>
        </div>
        <time class="post__time"
              datetime="<?= $created_at ? format_iso_date_time(
                  $created_at
              ) : '' ?>"><?= $created_at ? format_relative_time($created_at)
                : '' ?>
            назад
        </time>
    </footer>

    <?= include_template(
        'common/post-card/hashtags.php',
        ['hashtags' => $hashtags]
    ) ?>

    <div class="comments">
        <?= $comments_list_content ?>
        <?= $comments_form_content ?>
    </div>
</article>
