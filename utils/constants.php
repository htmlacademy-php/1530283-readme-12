<?php

const TIMEZONE = 'UTC';

const HASH_CHAR = '#';

const QUESTION_CHAR = '?';

const DEFAULT_POSTS_LIMIT = 6;

const DEFAULT_COMMENTS_LIMIT = 2;

const INITIAL_POSTS_PAGE = 1;

const AVATAR_PLACEHOLDER = 'img/icon-input-user.svg';

const CONTENT_FILTER_QUERY = 'content-type-id';

const COMMENTS_EXPANDED = 'comments-expanded';

const TAB_QUERY = 'tab';

const COMMENTS_POST_ID_QUERY = 'comments-post-id';

const USER_ID_QUERY = 'user-id';

const CONVERSATION_ID_QUERY = 'conversation-id';

const POST_ID_QUERY = 'post-id';

const LIMIT_QUERY = 'limit';

const PAGE_QUERY = 'page';

const SEARCH_QUERY = 'query';

const SORT_TYPE_QUERY = 'sort_type';

const SORT_ORDER_REVERSED = "sort_order_reversed";

const SERVER_ERROR_STATUS = 500;

const BAD_REQUEST_STATUS = 400;

const NOT_FOUND_STATUS = 404;

const SORT_TYPE_OPTIONS = [
    [
        'label' => 'Популярность',
        'value' => 'views_count',
    ],
    [
        'label' => 'Лайки',
        'value' => 'likes_count',
    ],
    [
        'label' => 'Дата',
        'value' => 'created_at',
    ],
];

const PROFILE_POSTS_TAB = [
    'label' => 'Посты',
    'value' => 'posts',
];

const PROFILE_LIKES_TAB = [
    'label' => 'Лайки',
    'value' => 'likes',
];

const PROFILE_SUBSCRIPTIONS_TAB = [
    'label' => 'Подписки',
    'value' => 'subscriptions',
];

const PROFILE_TABS = [
    PROFILE_POSTS_TAB,
    PROFILE_LIKES_TAB,
    PROFILE_SUBSCRIPTIONS_TAB,
];

const TEXT_SEPARATOR = ' ';

const MAX_POST_CARD_TEXT_CONTENT_LENGTH = 300;

const DAYS_IN_WEEK = 7;

const DAYS_IN_MONTH = 30;

const RELATIVE_TIME_UNITS = [
    'minute' => [
        'one' => 'минуту',
        'two' => 'минуты',
        'many' => 'минут',
    ],
    'hour' => [
        'one' => 'час',
        'two' => 'часа',
        'many' => 'часов',
    ],
    'day' => [
        'one' => 'день',
        'two' => 'дня',
        'many' => 'дней',
    ],
    'week' => [
        'one' => 'неделю',
        'two' => 'недели',
        'many' => 'недель',
    ],
    'month' => [
        'one' => 'месяц',
        'two' => 'месяца',
        'many' => 'месяцев',
    ],
];

const ADD_POST_FORM_TITLE = [
    'link' => 'Форма добавления ссылки',
    'quote' => 'Форма добавления цитаты',
    'video' => 'Форма добавления видео',
    'text' => 'Форма добавления текста',
    'photo' => 'Форма добавления фото',
];

const MAX_PHOTO_FILE_SIZE = 1024 * 1024 * 10;

const ALLOWED_PHOTO_FILE_TYPES = [
    'image/jpg',
    'image/jpeg',
    'image/png',
    'image/gif',
];

const MAX_EMAIL_LENGTH = 255;

const MAX_LOGIN_LENGTH = 255;

const MAX_PASSWORD_BYTES_LENGTH = 72;

const MAX_TITLE_LENGTH = 255;

const MAX_TAG_LENGTH = 255;

const MAX_STRING_CONTENT_LENGTH = 255;

const MIN_TEXT_CONTENT_LENGTH = 4;

const MAX_TEXT_CONTENT_LENGTH = 1000;
