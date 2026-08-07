 
USE blindbite;

-- create restaurants
CREATE TABLE restaurants(
    restaurant_name VARCHAR(100) PRIMARY KEY,
    restaurant_address VARCHAR(100) NOT NULL UNIQUE,
    restaurant_opening_hours TIME NOT NULL,
    restaurant_phone_number VARCHAR(20) NOT NULL UNIQUE,
    blind_box_price DECIMAL(10, 2) NOT NULL,
    blind_box_description TEXT NOT NULL,
    blind_box_remaining_quantity INTEGER NOT NULL,
    blind_box_food_category TEXT NOT NULL
);

-- create users
CREATE TABLE users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    gender ENUM("MALE", "FEMALE", "OTHER") NOT NULL,
    date_of_birth DATE,
    address TEXT,
    phone_number INTEGER(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users
MODIFY password VARCHAR(255) NOT NULL;


