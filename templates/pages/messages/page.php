<?php
// todo: сообщения в текущем чате - статичны
// todo: форма добавления сообщения - статична
// todo: карточки разговоров - последнее сообщение статично, сортировка отсутствует
/**
 * Шаблон страницы 'Сообщения'
 *
 * @var array $user - данные пользователя
 * @var array $conversations -  массив с разговорами пользователя
 */
?>

<h1 class="visually-hidden">Личные сообщения</h1>
<section class="messages tabs">
    <h2 class="visually-hidden">Сообщения</h2>
    <div class="messages__contacts">
        <ul class="messages__contacts-list tabs__list">
            <?php
            foreach ($conversations as $conversation): ?>
                <?= include_template(
                    'pages/messages/conversation-card.php',
                    [
                        'conversation' => $conversation
                    ]
                ) ?>
            <?php
            endforeach; ?>
        </ul>
    </div>
    <div class="messages__chat">
        <div class="messages__chat-wrapper">
            <ul class="messages__list tabs__content tabs__content--active">
                <li class="messages__item">
                    <div class="messages__info-wrapper">
                        <div class="messages__item-avatar">
                            <a class="messages__author-link" href="#">
                                <img class="messages__avatar"
                                     src="../img/userpic-larisa-small.jpg"
                                     alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="messages__item-info">
                            <a class="messages__author" href="#">
                                Лариса Роговая
                            </a>
                            <time class="messages__time"
                                  datetime="2019-05-01T14:40">
                                1 ч назад
                            </time>
                        </div>
                    </div>
                    <p class="messages__text">
                        Озеро Байкал – огромное древнее озеро в горах Сибири к
                        северу от монгольской границы. Байкал считается самым
                        глубоким озером в мире. Он окружен сетью пешеходных
                        маршрутов, называемых Большой байкальской тропой.
                        Деревня Листвянка, расположенная на западном берегу
                        озера, – популярная отправная точка для летних
                        экскурсий. Зимой здесь можно кататься на коньках и
                        собачьих упряжках.
                    </p>
                </li>
                <li class="messages__item messages__item--my">
                    <div class="messages__info-wrapper">
                        <div class="messages__item-avatar">
                            <a class="messages__author-link" href="#">
                                <img class="messages__avatar"
                                     src="../img/userpic-medium.jpg"
                                     alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="messages__item-info">
                            <a class="messages__author" href="#">
                                Антон Глуханько
                            </a>
                            <time class="messages__time"
                                  datetime="2019-05-01T14:39">
                                1 ч назад
                            </time>
                        </div>
                    </div>
                    <p class="messages__text">
                        Озеро Байкал – огромное древнее озеро в горах Сибири к
                        северу от монгольской границы. Байкал считается самым
                        глубоким озером в мире. Он окружен сетью пешеходных
                        маршрутов, называемых Большой байкальской тропой.
                        Деревня Листвянка, расположенная на западном берегу
                        озера, – популярная отправная точка для летних
                        экскурсий. Зимой здесь можно кататься на коньках и
                        собачьих упряжках.
                    </p>
                </li>
                <li class="messages__item">
                    <div class="messages__info-wrapper">
                        <div class="messages__item-avatar">
                            <a class="messages__author-link" href="#">
                                <img class="messages__avatar"
                                     src="../img/userpic-larisa-small.jpg"
                                     alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="messages__item-info">
                            <a class="messages__author" href="#">
                                Лариса Роговая
                            </a>
                            <time class="messages__time"
                                  datetime="2019-05-01T14:39">
                                1 ч назад
                            </time>
                        </div>
                    </div>
                    <p class="messages__text">
                        Озеро Байкал – огромное древнее озеро в горах Сибири к
                        северу от монгольской границы. Байкал считается самым
                        глубоким озером в мире. Он окружен сетью пешеходных
                        маршрутов, называемых Большой байкальской тропой.
                        Деревня Листвянка, расположенная на западном берегу
                        озера, – популярная отправная точка для летних
                        экскурсий. Зимой здесь можно кататься на коньках и
                        собачьих упряжках.
                    </p>
                </li>
            </ul>
        </div>
        <div class="comments">
            <form class="comments__form form" action="#" method="post">
                <div class="comments__my-avatar">
                    <img class="comments__picture"
                         src="<?= $user['avatar_url'] ?? AVATAR_PLACEHOLDER ?>"
                         alt="Аватар пользователя">
                </div>
                <div class="form__input-section form__input-section--error">
                <textarea name="content"
                          class="comments__textarea form__textarea form__input"
                          placeholder="Ваше сообщение"></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="form__error-button button" type="button">!
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Ошибка валидации</h3>
                        <p class="form__error-desc">Это поле обязательно к
                            заполнению</p>
                    </div>
                </div>
                <button class="comments__submit button button--green"
                        type="submit">Отправить
                </button>
            </form>
        </div>
    </div>
</section>
