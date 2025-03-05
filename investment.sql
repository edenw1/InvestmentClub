CREATE DATABASE investment_club; 
USE investment_club;  
CREATE TABLE users ( 
    user_id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL, 
    email VARCHAR(100) NOT NULL UNIQUE, 
    admin BOOLEAN DEFAULT FALSE 
) ENGINE=InnoDB; 

CREATE TABLE stocks ( 
    stock_id INT AUTO_INCREMENT PRIMARY KEY, 
    symbol VARCHAR(10) NOT NULL UNIQUE, 
    name VARCHAR(100) NOT NULL, 
    active BOOLEAN DEFAULT FALSE, 
    watchlist BOOLEAN DEFAULT FALSE 
) ENGINE=InnoDB; 

CREATE TABLE presentation ( 
    presentation_id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    title VARCHAR(255) NOT NULL, 
    file_path VARCHAR(255) NOT NULL, 
    date DATETIME NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE 
) ENGINE=InnoDB; 

CREATE TABLE stockProposal ( 
    proposal_id INT AUTO_INCREMENT PRIMARY KEY, 
    presentation_id INT NOT NULL, 
    stock_symbol VARCHAR(10) NOT NULL, 
    stock_name VARCHAR(100) NOT NULL, 
    proposed_by INT NOT NULL, 
    action ENUM('add', 'remove', 'increase', 'decrease') DEFAULT 'add', 
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', 
    quantity INT, 
    FOREIGN KEY (presentation_id) REFERENCES presentation(presentation_id) ON DELETE CASCADE, 
    FOREIGN KEY (proposed_by) REFERENCES users(user_id) ON DELETE CASCADE 
) ENGINE=InnoDB; 

CREATE TABLE transactions ( 
    transaction_id INT AUTO_INCREMENT PRIMARY KEY, 
    transaction_type ENUM('Buy', 'Sell') NOT NULL, 
    quantity INT NOT NULL, 
    price_per_share DECIMAL(10,2) NOT NULL, 
    buy_sell_date DATE NOT NULL, 
    stock_id INT NOT NULL, 
    FOREIGN KEY (stock_id) REFERENCES stocks(stock_id) ON DELETE CASCADE 
) ENGINE=InnoDB; 

CREATE TABLE historicalData ( 
    historic_id INT AUTO_INCREMENT PRIMARY KEY, 
    stock_id INT NOT NULL, 
    date DATE NOT NULL, 
    open_price DECIMAL(10,2), 
    high_price DECIMAL(10,2), 
    low_price DECIMAL(10,2), 
    close_price DECIMAL(10,2), 
    FOREIGN KEY (stock_id) REFERENCES stocks(stock_id) ON DELETE CASCADE 
) ENGINE=InnoDB; 

CREATE TABLE vote ( 
    vote_id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    presentation_id INT NOT NULL, 
    yes_or_no BOOLEAN NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE, 
    FOREIGN KEY (presentation_id) REFERENCES presentation(presentation_id) ON DELETE CASCADE 
) ENGINE=InnoDB; 

CREATE TABLE content ( 
    content_id INT AUTO_INCREMENT PRIMARY KEY, 
    title VARCHAR(255), 
    description TEXT, 
    url VARCHAR(255), 
    type ENUM('photo', 'video', 'text') NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE member (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(100),
    name VARCHAR(100),
    description VARCHAR(255),
    photo_path VARCHAR(255)
) ENGINE=InnoDB;


INSERT INTO users (username, password, email, admin) VALUES ('root', '$2y$10$nHi7VJxiVO3Dju8eTlYSROcceAIH9E8MrVbd.deAFHr2fZJmTB0ty', 'admin@muskingum.edu', 1); 