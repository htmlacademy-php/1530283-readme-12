DROP DATABASE IF EXISTS readme;

CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE content_types(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    type varchar(255) NOT NULL UNIQUE,
    name varchar(255) NOT NULL UNIQUE
);

CREATE TABLE users(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    created_at timestamp DEFAULT current_timestamp,
    email varchar(255) NOT NULL UNIQUE,
    login varchar(255) NOT NULL,
    password_hash char(60) NOT NULL,
    avatar_url varchar(255)
);

CREATE TABLE hashtags(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL UNIQUE
);

CREATE TABLE posts(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    author_id int unsigned NOT NULL,
    content_type_id int unsigned NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    title varchar(255) NOT NULL,
    text_content varchar(1000),
    string_content varchar(255),
    views_count int unsigned DEFAULT 0,
    FOREIGN KEY (author_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE posts_hashtags(
    post_id int unsigned NOT NULL,
    hashtag_id int unsigned NOT NULL,
    PRIMARY KEY (post_id, hashtag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE comments(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    author_id int unsigned NOT NULL,
    post_id int unsigned NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    content varchar(1000) NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE likes(
    post_id int unsigned NOT NULL,
    author_id int unsigned NOT NULL,
    PRIMARY KEY (post_id, author_id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE
);

CREATE TABLE subscriptions(
    subscriber_id int unsigned NOT NULL CHECK (subscriber_id != observable_id),
    observable_id int unsigned NOT NULL CHECK (subscriber_id != observable_id),
    PRIMARY KEY (subscriber_id, observable_id),
    FOREIGN KEY (subscriber_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (observable_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE messages(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    sender_id int unsigned NOT NULL CHECK (sender_id != receiver_id),
    receiver_id int unsigned NOT NULL CHECK (sender_id != receiver_id),
    created_at timestamp DEFAULT current_timestamp,
    content varchar(1000) NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE INDEX post_text_content_index ON posts(text_content);
