CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
id             INT AUTO_INCREMENT PRIMARY KEY,
cat_name       CHAR(64) NOT NULL UNIQUE,
character_code CHAR(64) NOT NULL UNIQUE
);

CREATE TABLE lots (
id            INT AUTO_INCREMENT PRIMARY KEY,
dt_add        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
lot_name      CHAR(128) NOT NULL,
description   TEXT,
lot_image_src CHAR(128),
start_price   INT,
dt_end        TIMESTAMP,
price_step    INT,
author_id     INT NOT NULL,
winner_id     INT,
category_code CHAR(64) NOT NULL,
current_price INT
);

CREATE TABLE bets (
id        INT AUTO_INCREMENT PRIMARY KEY,
dt_bet    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
price     INT NOT NULL,
better_id INT NOT NULL,
lot_id    INT NOT NULL
);

CREATE TABLE users (
id         INT AUTO_INCREMENT PRIMARY KEY,
dt_reg     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
email      CHAR(128) NOT NULL UNIQUE,
user_name  CHAR(64) NOT NULL,
password   CHAR(64) NOT NULL,
avatar_src CHAR(128),
contacts   CHAR(128)
);