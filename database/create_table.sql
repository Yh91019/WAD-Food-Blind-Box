USE blindbite;

-- Create Restaurants Table
CREATE TABLE restaurants (
    restaurant_name VARCHAR(100) PRIMARY KEY,
    restaurant_address VARCHAR(100) NOT NULL UNIQUE,
    restaurant_opening_hours TIME NOT NULL,
    restaurant_closing_hours TIME NOT NULL,
    restaurant_phone_number VARCHAR(20) NOT NULL UNIQUE,
    blind_box_price DECIMAL(10,2) NOT NULL,
    blind_box_description TEXT NOT NULL,
    blind_box_remaining_quantity INT NOT NULL,
    blind_box_food_category TEXT NOT NULL
);

-- Create Users Table
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

-- Create Payment Method Table
CREATE TABLE paymentmethod (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    cardholder_name VARCHAR(100) NOT NULL,
    card_number VARCHAR(20) NOT NULL,
    expiry_date VARCHAR(10) NOT NULL,
    cvv VARCHAR(4) NOT NULL,

    FOREIGN KEY (username) REFERENCES users(username)
        ON DELETE CASCADE
);

-- Create Cart Table
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username) REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name) REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
);

-- Create Wishlist Table
CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username) REFERENCES users(username)
        ON DELETE CASCADE,

    FOREIGN KEY (restaurant_name) REFERENCES restaurants(restaurant_name)
        ON DELETE CASCADE
);

-- Create Order History Table
CREATE TABLE history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    restaurant_name VARCHAR(100) NOT NULL,
    blind_box_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    payment_method VARCHAR(50),
    order_type ENUM('Pickup', 'Delivery') NOT NULL DEFAULT 'Pickup',
    status ENUM('Preparing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Preparing',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (username)
        REFERENCES users(username)
        ON DELETE CASCADE
)