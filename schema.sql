DROP DATABASE IF EXISTS readme;

CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE content_types(
    id int PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL UNIQUE,
    icon varchar(255) NOT NULL UNIQUE
);

CREATE TABLE users(
    id int PRIMARY KEY AUTO_INCREMENT,
    created_at timestamp DEFAULT current_timestamp,
    email varchar(255) NOT NULL UNIQUE,
    login varchar(255) NOT NULL,
    password_hash varchar(255) NOT NULL,
    avatar_url varchar(255)
);

CREATE TABLE hashtags(
    id int PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL UNIQUE
);

CREATE TABLE posts(
    id int PRIMARY KEY AUTO_INCREMENT,
    author_id int NOT NULL,
    content_type_id int NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    title varchar(255) NOT NULL,
    text_content text,
    string_content varchar(255),
    views_count int DEFAULT 0
);

CREATE TABLE posts_hashtags(
    post_id int NOT NULL,
    hashtag_id int NOT NULL,
    PRIMARY KEY (post_id, hashtag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE comments(
    id int PRIMARY KEY AUTO_INCREMENT,
    author_id int NOT NULL,
    post_id int NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    content text NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE likes(
    post_id int NOT NULL,
    author_id int NOT NULL,
    PRIMARY KEY (post_id, author_id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE
);

CREATE TABLE subscriptions(
    subscriber_id int NOT NULL CHECK (subscriber_id != observable_id),
    observable_id int NOT NULL CHECK (subscriber_id != observable_id),
    PRIMARY KEY (subscriber_id, observable_id),
    FOREIGN KEY (subscriber_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (observable_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE messages(
    id int PRIMARY KEY AUTO_INCREMENT,
    sender_id int NOT NULL CHECK (sender_id != receiver_id),
    receiver_id int NOT NULL CHECK (sender_id != receiver_id),
    created_at timestamp DEFAULT current_timestamp,
    content text NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);
