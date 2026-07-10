CREATE TABLE restaurants(
    restaurant_name VARCHAR(100) PRIMARY KEY,
    restaurant_address TEXT NOT NULL UNIQUE,
    restaurant_opening_hours TIME NOT NULL,
    restaurant_phone_number INTEGER(20) NOT NULL UNIQUE,
    blind_box_price DECIMAL(10,2) NOT NULL,
    blind_box_description TEXT NOT NULL,
    blind_box_remaining_quantity INTEGER NOT NULL,
    blind_box_food_category TEXT NOT NULL,
)