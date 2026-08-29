USE blindbite;


CREATE TABLE restaurants (
    restaurant_name VARCHAR(100) PRIMARY KEY,
    restaurant_address VARCHAR(100) NOT NULL UNIQUE,
    restaurant_opening_hours TIME NOT NULL,
    restaurant_closing_hours TIME NOT NULL,
    restaurant_phone_number VARCHAR(20) NOT NULL UNIQUE,
    blind_box_price DECIMAL(10,2) NOT NULL,
    blind_box_description TEXT NOT NULL,
    blind_box_image VARCHAR(255) DEFAULT NULL,
    blind_box_food_category TEXT NOT NULL
);



CREATE TABLE users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    gender ENUM('MALE','FEMALE','OTHER') NOT NULL,
    date_of_birth DATE,
    address TEXT,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(40) NOT NULL UNIQUE,
    title VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL,
    discount_type ENUM('Percentage', 'Fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_spend DECIMAL(10,2) NOT NULL DEFAULT 0,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE user_vouchers (
    user_voucher_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    promotion_id INT NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL DEFAULT NULL,

    UNIQUE KEY unique_user_promotion
    (
        username,
        promotion_id
    ),

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (promotion_id)
        REFERENCES promotions(promotion_id)
        ON DELETE CASCADE
);



CREATE TABLE paymentmethod (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    cardholder_name VARCHAR(100) NOT NULL,
    card_number VARCHAR(20) NOT NULL,
    expiry_date VARCHAR(10) NOT NULL,
    cvv VARCHAR(4) NOT NULL,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE
);



CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name)
        REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
);

CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name)
        REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
);



CREATE TABLE history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    blind_box_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    payment_method VARCHAR(50),
    order_type ENUM('Pickup', 'Delivery')
        NOT NULL DEFAULT 'Pickup',
    voucher_code VARCHAR(40) DEFAULT NULL,
    discount_amount DECIMAL(10,2)
        NOT NULL DEFAULT 0,
    delivery_fee DECIMAL(10,2)
        NOT NULL DEFAULT 0,
    final_total DECIMAL(10,2) DEFAULT NULL,
    status ENUM(
        'Preparing',
        'Completed',
        'Cancelled'
    )
        NOT NULL DEFAULT 'Preparing',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE
);


CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,

    history_id INT NOT NULL UNIQUE,

    username VARCHAR(50) NOT NULL,

    restaurant_name VARCHAR(100) NOT NULL,

    rating TINYINT UNSIGNED NOT NULL,

    review TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_review_rating
        CHECK (rating BETWEEN 1 AND 5),

    FOREIGN KEY (history_id)
        REFERENCES history(history_id)
        ON DELETE CASCADE,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name)
        REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);



CREATE TABLE admin (
    admin_username VARCHAR(50) NOT NULL PRIMARY KEY,
    admin_password VARCHAR(50) NOT NULL
);



CREATE TABLE enquiries (
    enquiry_id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) DEFAULT NULL,

    customer_name VARCHAR(100) NOT NULL,

    customer_email VARCHAR(150) NOT NULL,

    subject VARCHAR(150) NOT NULL,

    message TEXT NOT NULL,

    status ENUM(
        'New',
        'In Progress',
        'Resolved'
    )
        NOT NULL DEFAULT 'New',

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE SET NULL
);