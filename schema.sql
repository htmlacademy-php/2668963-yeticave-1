CREATE DATABASE yeticave 
DEFAULT CHARACTER SET utf8 
DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    name VARCHAR(128) NOT NULL,
    password CHAR(32) NOT NULL,
    contact VARCHAR(128) NOT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL UNIQUE,
    code VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE lots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(128) NOT NULL,
    about VARCHAR(128) NOT NULL,
    img_url VARCHAR(128) NOT NULL,
    start_price INT NOT NULL,
    expiration_date DATETIME NOT NULL,
    bet_step INT NOT NULL,
    author_id INT NOT NULL,
    winner_id INT,
    category_id INT NOT NULL,
    FOREIGN KEY (author_id) REFERENCES users(id),
    FOREIGN KEY (winner_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX lot_title (title)
);

CREATE TABLE bets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount INT NOT NULL,
    user_id INT NOT NULL,
    lot_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (lot_id) REFERENCES lots(id)
);

