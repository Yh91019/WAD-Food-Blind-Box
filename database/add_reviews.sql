USE blindbite;

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    history_id INT NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5),

    FOREIGN KEY (history_id) REFERENCES history(history_id)
        ON DELETE CASCADE,

    FOREIGN KEY (username) REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name) REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
