<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <?= include_template('partials/popular-filters.php') ?>
    <div class="popular__posts">
        <?php foreach ($post_cards as $post_card): ?>
            <?php
            $is_post_card_invalid =
                !(isset($post_card['title']) and
                    isset($post_card['type']) and
                    isset($post_card['content']) and
                    isset($post_card['user_name']) and
                    isset($post_card['avatar']));

            if ($is_post_card_invalid) {
                continue;
            }

            list(
                'title' => $title,
                'type' => $type,
                'content' => $content,
                'user_name' => $user_name,
                'avatar' => $avatar
                ) = $post_card;
            ?>
            <article class="popular__post post <?= $type ?>">
                <header class="post__header">
                    <h2><?= $title ?></h2>
                </header>
                <div class="post__main">
                    <?php switch ($type):
                        case 'post-quote': ?>
                            <blockquote>
                                <p><?= $content ?></p>
                                <cite>Неизвестный Автор</cite>
                            </blockquote>
                            <?php break; ?>
                        <?php case 'post-text': ?>
                            <?= decorate_post_text_content($content) ?>
                            <?php break; ?>
                        <?php case 'post-photo': ?>
                            <div class="post-photo__image-wrapper">
                                <img src="img/<?= $content ?>" alt="Фото от пользователя" width="360" height="240">
                            </div>
                            <?php break; ?>
                        <?php case 'post-link': ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="http://<?= $content ?>" title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="https://www.google.com/s2/favicons?domain=vitadental.ru" alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= $title ?></h3>
                                        </div>
                                    </div>
                                    <span><?= $content ?></span>
                                </a>
                            </div>
                            <?php break; ?>
                        <?php endswitch; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" src="img/<?= $avatar ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= $user_name ?></b>
                                <time class="post__time" datetime="">дата</time>
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
        <?php endforeach; ?>
    </div>
</div>
