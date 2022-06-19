DROP DATABASE IF EXISTS readme;

CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE content_types(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    type varchar(255) NOT NULL UNIQUE,
    name varchar(255) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE users(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    email varchar(255) NOT NULL UNIQUE,
    login varchar(255) NOT NULL,
    password_hash char(60) NOT NULL,
    avatar_url varchar(255),
    created_at timestamp DEFAULT current_timestamp
) ENGINE = InnoDB;

CREATE TABLE hashtags(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE posts(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    author_id int unsigned NOT NULL,
    content_type_id int unsigned NOT NULL,
    title varchar(255) NOT NULL,
    text_content varchar(1000),
    string_content varchar(255),
    views_count int unsigned DEFAULT 0,
    created_at timestamp DEFAULT current_timestamp,
    FULLTEXT INDEX post_fulltext_index (title, string_content, text_content),
    FOREIGN KEY (author_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE reposts(
    original_post_id int unsigned NOT NULL,
    repost_id int unsigned NOT NULL,
    PRIMARY KEY (original_post_id, repost_id),
    FOREIGN KEY (original_post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (repost_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

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
) ENGINE = InnoDB;

CREATE TABLE comments(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    author_id int unsigned NOT NULL,
    post_id int unsigned NOT NULL,
    content varchar(1000) NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    FOREIGN KEY (post_id) REFERENCES posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE likes(
    post_id int unsigned NOT NULL,
    author_id int unsigned NOT NULL,
    created_at timestamp DEFAULT current_timestamp,
    PRIMARY KEY (post_id, author_id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE subscriptions(
    subscriber_id int unsigned NOT NULL,
    observable_id int unsigned NOT NULL,
    PRIMARY KEY (subscriber_id, observable_id),
    FOREIGN KEY (subscriber_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (observable_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE conversations(
    id int unsigned PRIMARY KEY AUTO_INCREMENT,
    initiator_id int unsigned NOT NULL,
    interlocutor_id int unsigned NOT NULL,
    user_id_least int unsigned AS (least(initiator_id, interlocutor_id)),
    user_id_greatest int unsigned AS (greatest(initiator_id, interlocutor_id)),
    UNIQUE KEY unique_users (user_id_least, user_id_greatest),
    FOREIGN KEY (initiator_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (interlocutor_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE messages(
     id int unsigned PRIMARY KEY AUTO_INCREMENT,
     conversation_id int unsigned NOT NULL,
     author_id int unsigned NOT NULL,
     content varchar(1000) NOT NULL,
     is_read boolean DEFAULT false,
     created_at timestamp DEFAULT current_timestamp,
     FOREIGN KEY (author_id) REFERENCES users(id)
         ON UPDATE CASCADE
         ON DELETE CASCADE,
     FOREIGN KEY (conversation_id) REFERENCES conversations(id)
         ON UPDATE CASCADE
         ON DELETE CASCADE
) ENGINE = InnoDB;
