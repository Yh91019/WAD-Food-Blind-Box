USE blindbite;

-- Insert restaurants data
INSERT INTO restaurants (
    restaurant_name,
    restaurant_address,
    restaurant_opening_hours,
    restaurant_closing_hours,
    restaurant_phone_number,
    blind_box_price,
    blind_box_description,
    blind_box_food_category
) VALUES
(
    'The Gourmet Kitchen',
    '123 Main Street, Cityville',
    '10:00',
    '22:00',
    '1234567890',
    15.99,
    'A delightful assortment of gourmet dishes.',
    'Gourmet'
),
(
    'Sushi World',
    '456 Ocean Avenue, Seaside Town',
    '11:00',
    '22:30',
    '9876543210',
    12.50,
    'Fresh sushi and sashimi from the sea.',
    'Japanese'
),
(
    'Pasta Paradise',
    '789 Pasta Lane, Foodie City',
    '09:30',
    '21:30',
    '5551234567',
    10.75,
    'Authentic Italian pasta dishes.',
    'Italian'
),
(
    'Burger Haven',
    '321 Burger Blvd, Fastfood City',
    '08:00',
    '23:00',
    '4449876543',
    8.99,
    'Juicy burgers with a variety of toppings.',
    'Fast Food'
),
(
    'Vegan Delights',
    '654 Green Street, Plantville',
    '10:30',
    '21:00',
    '3335678901',
    11.25,
    'Delicious vegan meals for everyone.',
    'Vegan'
);

INSERT INTO admin (admin_username, admin_password)
VALUES ('admin', 'admin123');
