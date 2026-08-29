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

INSERT INTO promotions
    (code, title, description, discount_type, discount_value, minimum_spend, starts_at, ends_at, is_active)
VALUES
    ('FIRSTBITE20', '20% Off Your First Bite', 'Save 20% on a Blind Bite order of RM10 or more.', 'Percentage', 20.00, 10.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('SAVE5', 'RM5 Blind Bite Voucher', 'Save RM5 when your cart reaches RM20.', 'Fixed', 5.00, 20.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('NIGHTBITE5', 'RM5 Evening Treat', 'Save RM5 on orders of RM15 or more.', 'Fixed', 5.00, 15.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Insert 2 normal users
INSERT INTO users
    (username, password, email, gender, date_of_birth, address, phone_number)
VALUES
    (
        'john',
        'john123',
        'john@gmail.com',
        'MALE',
        '2002-05-15',
        'Kuala Lumpur, Malaysia',
        '0123456789'
    ),
    (
        'jane',
        'jane123',
        'jane@gmail.com',
        'FEMALE',
        '2003-08-20',
        'Petaling Jaya, Malaysia',
        '0134567890'
    );