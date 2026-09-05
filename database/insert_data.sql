USE blindbite;

INSERT INTO admin (admin_username, admin_password)
VALUES ('admin', 'admin123');

INSERT INTO promotions
    (code, title, description, discount_type, discount_value, minimum_spend, starts_at, ends_at, is_active)
VALUES
    ('FIRSTBITE20', '20% Off Your First Bite', 'Save 20% on a Blind Bite order of RM10 or more.', 'Percentage', 20.00, 10.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('SAVE5', 'RM5 Blind Bite Voucher', 'Save RM5 when your cart reaches RM20.', 'Fixed', 5.00, 20.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('NIGHTBITE5', 'RM5 Evening Treat', 'Save RM5 on orders of RM15 or more.', 'Fixed', 5.00, 15.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);

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
    'Myosotis Cafe',
    '5-1, Jalan Sungai Long 1/3, Bandar Sungai Long, 43000 Kajang',
    '09:00',
    '21:00',
    '03-90110696',
    13.90,
    'A cosy cafe favourite among UTAR students, best known for its hearty katsu rice bowls and all-day breakfast plates. Your blind box may feature a katsu bowl, pasta, or one of their signature desserts.',
    'Cafe / Western-Asian Fusion'
),
(
    'Sushi Mentai',
    'No. 20, Jalan SL 1/2, Bandar Sungai Long, 43200 Cheras',
    '12:00',
    '21:30',
    '03-90000002',
    14.90,
    'Each blind box packs a fresh mix of sushi straight from Sushi Mentai''s menu — salmon sashimi, salmon and tuna nigiri, and maki rolls like crab avocado or salmon skin. Exact pieces vary by order, but every box is generously filled and freshly prepared. Great value for sushi lovers who don''t mind a surprise mix.',
    'Japanese / Sushi'
),
(
    'QQ Pan Mee & Ramen Restaurant',
    '28, Jalan SL 1/3, Bandar Sungai Long, 43000 Kajang',
    '11:00',
    '22:00',
    '03-90000003',
    9.90,
    'A no-frills Chinese noodle specialist loved by UTAR-ians for their tom yam pan mee, seafood ramen, and yee mee. Blind box orders may include a bowl of pan mee, ramen, or their lemon chicken rice.',
    'Chinese / Noodles'
);

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
    'Secret Recipe (Sungai Long)',
    'No 1, Jalan SL 1/11, Bandar Sungai Long, 43000 Kajang',
    '10:30',
    '22:30',
    '03-90104618',
    16.90,
    'A well-loved cafe and bakery chain famous for its cakes. Your blind box may feature a slice of their signature Chocolate Indulgence or Tiramisu alongside a savoury main like Shepherd''s Pie or Thai Fried Rice — sweet and savoury in one surprise box.',
    'Cafe / Bakery & Western'
),
(
    'KFC Bandar Sungai Long',
    'No. 9, Jalan SL 1/10, Bandar Sungai Long, 43000 Kajang',
    '10:00',
    '23:00',
    '03-90105973',
    10.90,
    'Crispy, comforting, and always a crowd favourite. Your blind box may come with a mix of Original or Hot & Spicy chicken, a side of coleslaw or mashed potato, and their famous gravy — a familiar treat with a fun twist.',
    'Fast Food / Fried Chicken'
),
(
    'Domino''s Pizza (Sungai Long)',
    '26G, Jalan SL 1/11, Bandar Sungai Long, 43300 Kajang',
    '10:30',
    '23:00',
    '1300888333',
    19.90,
    'Perfect for pizza lovers who enjoy a surprise topping combo. Each blind box may feature a selection of slices from their classic and specialty pizzas, paired with garlic bread or a side of wings.',
    'Pizza / Fast Food'
),
(
    'Amitie Cafe',
    '56-1, Jalan SL 1/3, Bandar Sungai Long, 43200 Kajang',
    '10:00',
    '17:00',
    '03-90111452',
    15.90,
    'A cosy brunch spot loved for its all-day breakfast and pasta dishes. Your blind box might feature their famous Salmon Aglio Olio, a fluffy waffle, or a savoury egg dish — comfort food with a homely touch.',
    'Cafe / Western Brunch'
),
(
    'K Cafe',
    '20-1, Jalan SL 1/2, Bandar Sungai Long, 43000 Kajang',
    '10:00',
    '22:00',
    '03-90000004',
    11.90,
    'A cafe that keeps its menu a surprise every single day — literally. Blind box orders here include whatever chef''s daily rice or noodle special is being served, paired with a drink. True to the blind box spirit!',
    'Cafe / Daily Chef''s Special'
),
(
    'Restoran Ibrahim Maju Bistro',
    '36, Jalan SL 1/2, Bandar Sungai Long, 43000 Kajang',
    '07:00',
    '23:00',
    '03-90100226',
    8.90,
    'A well-known mamak spot for hearty roti and rice dishes. Your blind box could include a plate of nasi campur, a roti canai set, or their signature tomyam — filling, affordable, and full of local flavour.',
    'Mamak / Malaysian'
);

INSERT INTO users (username, password, email, gender, phone_number)
VALUES ('Victoria', '$2y$10$examplePlaceholderHashChangeMe', 'victoria@example.com', 'FEMALE', '0123456780');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Myosotis Cafe', 13.90, 1, 'Cash', 'Pickup', 13.90, 'Completed');
SET @history_myosotis = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_myosotis,
    'Victoria',
    'Myosotis Cafe',
    5,
    'Such a cosy little spot near campus! My blind box came with their katsu bowl and it was so worth it, crispy and generous portion. Great place to unwind between classes.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Sushi Mentai', 14.90, 1, 'Cash', 'Pickup', 14.90, 'Completed');
SET @history_sushimentai = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_sushimentai,
    'Victoria',
    'Sushi Mentai',
    4,
    'Got a lovely mix of sashimi and maki in my blind box, fresh and well packed. Portion was generous for the price. Would give it 5 stars if there was a bit more variety in the rolls!'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'QQ Pan Mee & Ramen Restaurant', 9.90, 1, 'Cash', 'Pickup', 9.90, 'Completed');
SET @history_qqpanmee = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_qqpanmee,
    'Victoria',
    'QQ Pan Mee & Ramen Restaurant',
    4,
    'Solid comfort food pick! My blind box had their tom yam pan mee, nice and spicy with a good noodle texture. Quick service too, perfect for a between-class lunch run.'
);

INSERT INTO users (username, password, email, gender, phone_number) VALUES
('Daniel93',   '$2y$10$examplePlaceholderHashChangeMe', 'daniel93@example.com',  'MALE',   '0111234501'),
('AisyahR',    '$2y$10$examplePlaceholderHashChangeMe', 'aisyahr@example.com',   'FEMALE', '0111234502'),
('WeiJianTan', '$2y$10$examplePlaceholderHashChangeMe', 'weijiantan@example.com','MALE',   '0111234503'),
('PriyaS',     '$2y$10$examplePlaceholderHashChangeMe', 'priyas@example.com',   'FEMALE', '0111234504'),
('FarahN',     '$2y$10$examplePlaceholderHashChangeMe', 'farahn@example.com',   'FEMALE', '0111234505'),
('MarcusLee',  '$2y$10$examplePlaceholderHashChangeMe', 'marcuslee@example.com','MALE',   '0111234506');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'Secret Recipe (Sungai Long)', 16.90, 1, 'Cash', 'Pickup', 16.90, 'Completed');
SET @history_secretrecipe = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_secretrecipe,
    'Daniel93',
    'Secret Recipe (Sungai Long)',
    5,
    'Got a slice of their Chocolate Indulgence cake plus a Shepherd''s Pie in my blind box, such a great combo! Perfect for satisfying both my sweet tooth and dinner cravings in one go.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'KFC Bandar Sungai Long', 10.90, 1, 'Cash', 'Pickup', 10.90, 'Completed');
SET @history_kfc = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_kfc,
    'AisyahR',
    'KFC Bandar Sungai Long',
    4,
    'Simple but reliable! My box had 2 pieces of Hot & Spicy chicken with mashed potato and gravy. Nothing fancy but always hits the spot after a long day of classes.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'Domino''s Pizza (Sungai Long)', 19.90, 1, 'Cash', 'Pickup', 19.90, 'Completed');
SET @history_dominos = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_dominos,
    'WeiJianTan',
    'Domino''s Pizza (Sungai Long)',
    5,
    'Blind box came with 4 slices from two different pizzas plus garlic bread, way more than I expected for the price! Great for sharing with a coursemate between classes.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'Amitie Cafe', 15.90, 1, 'Cash', 'Pickup', 15.90, 'Completed');
SET @history_amitie = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_amitie,
    'PriyaS',
    'Amitie Cafe',
    5,
    'Unboxed their Salmon Aglio Olio and it was so good, generous portion of salmon too! Love the cosy vibe of this cafe, makes it worth the short walk from campus.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'K Cafe', 11.90, 1, 'Cash', 'Pickup', 11.90, 'Completed');
SET @history_kcafe = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_kcafe,
    'FarahN',
    'K Cafe',
    4,
    'This place is basically built for blind boxes since even regular customers don''t know what they''ll get! Mine was a fried rice set with a drink, tasty and filling for the price.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'Restoran Ibrahim Maju Bistro', 8.90, 1, 'Cash', 'Pickup', 8.90, 'Completed');
SET @history_ibrahimmaju = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_ibrahimmaju,
    'MarcusLee',
    'Restoran Ibrahim Maju Bistro',
    4,
    'Got a plate of nasi campur with a good mix of dishes, classic mamak comfort food. Great value for the price and always packed with UTAR students during lunch hour.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'Myosotis Cafe', 13.90, 1, 'Cash', 'Pickup', 13.90, 'Completed');
SET @history_2_myosotis = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_myosotis,
    'Daniel93',
    'Myosotis Cafe',
    4,
    'Nice quiet spot to study after class. My blind box had a breakfast set with eggs and toast, simple but well made. Wifi is a bonus too.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'Sushi Mentai', 14.90, 1, 'Cash', 'Pickup', 14.90, 'Completed');
SET @history_2_sushimentai = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_sushimentai,
    'AisyahR',
    'Sushi Mentai',
    5,
    'Best value sushi near campus, hands down. My box had a good mix of nigiri and a maki roll, all fresh. Will definitely order again!'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'QQ Pan Mee & Ramen Restaurant', 9.90, 1, 'Cash', 'Pickup', 9.90, 'Completed');
SET @history_2_qqpanmee = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_qqpanmee,
    'WeiJianTan',
    'QQ Pan Mee & Ramen Restaurant',
    3,
    'Got their seafood ramen in my box, decent but a bit bland for my taste. Portion was generous though, and it''s always packed with UTAR students at lunchtime.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'K Cafe', 11.90, 1, 'Cash', 'Pickup', 11.90, 'Completed');
SET @history_2_kcafe = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_kcafe,
    'PriyaS',
    'K Cafe',
    4,
    'Love how every visit is a surprise since the menu changes daily. Got a noodle set this time, tasty and generous for the price!'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'Restoran Ibrahim Maju Bistro', 8.90, 1, 'Cash', 'Pickup', 8.90, 'Completed');
SET @history_2_ibrahimmaju = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_ibrahimmaju,
    'FarahN',
    'Restoran Ibrahim Maju Bistro',
    5,
    'My go-to mamak near campus! Blind box had a roti canai set with teh tarik, cheap, filling, and always consistent. Perfect for a quick meal between classes.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'Amitie Cafe', 15.90, 1, 'Cash', 'Pickup', 15.90, 'Completed');
SET @history_2_amitie = LAST_INSERT_ID();

INSERT INTO reviews (history_id, username, restaurant_name, rating, review)
VALUES (
    @history_2_amitie,
    'MarcusLee',
    'Amitie Cafe',
    4,
    'Cosy little cafe, good for catching up with friends. My box came with a waffle and a side of eggs, sweet and savoury combo worked surprisingly well together.'
);

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'Myosotis Cafe', 13.90, 1, 'Cash', 'Pickup', 13.90, 'Completed');
SET @h1 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h1, 'AisyahR', 'Myosotis Cafe', 4, 'Really chill spot to catch up with coursemates. Blind box had a pasta dish and it was pretty good, would come again.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'Myosotis Cafe', 13.90, 1, 'Cash', 'Pickup', 13.90, 'Completed');
SET @h2 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h2, 'WeiJianTan', 'Myosotis Cafe', 3, 'Food was decent, nothing too special, but it''s a nice quiet spot to study between classes. Wifi could be faster though.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'Myosotis Cafe', 13.90, 1, 'Cash', 'Pickup', 13.90, 'Completed');
SET @h3 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h3, 'PriyaS', 'Myosotis Cafe', 5, 'Absolutely loved the katsu bowl I got! Cosy ambience too, perfect spot for a study break.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'Sushi Mentai', 14.90, 1, 'Cash', 'Pickup', 14.90, 'Completed');
SET @h4 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h4, 'Daniel93', 'Sushi Mentai', 4, 'Solid pick for cheap sushi near campus. Box had a good mix of nigiri and maki, fresh and generous portion.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'Sushi Mentai', 14.90, 1, 'Cash', 'Pickup', 14.90, 'Completed');
SET @h5 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h5, 'FarahN', 'Sushi Mentai', 5, 'My favourite spot for a quick sushi fix between lectures! Always fresh and the pay-per-plate system makes it fun to try new things.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'Sushi Mentai', 14.90, 1, 'Cash', 'Pickup', 14.90, 'Completed');
SET @h6 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h6, 'MarcusLee', 'Sushi Mentai', 4, 'Good value for the price, box came with a nice variety. Gets crowded during lunch hour so plan ahead.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'QQ Pan Mee & Ramen Restaurant', 9.90, 1, 'Cash', 'Pickup', 9.90, 'Completed');
SET @h7 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h7, 'AisyahR', 'QQ Pan Mee & Ramen Restaurant', 4, 'Their tom yam pan mee is the perfect pick-me-up on a rainy day. Spicy, comforting, and quick service too.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'QQ Pan Mee & Ramen Restaurant', 9.90, 1, 'Cash', 'Pickup', 9.90, 'Completed');
SET @h8 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h8, 'PriyaS', 'QQ Pan Mee & Ramen Restaurant', 3, 'Noodles were fine but a bit salty for my taste. Portion was generous though and the price is unbeatable.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'QQ Pan Mee & Ramen Restaurant', 9.90, 1, 'Cash', 'Pickup', 9.90, 'Completed');
SET @h9 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h9, 'MarcusLee', 'QQ Pan Mee & Ramen Restaurant', 4, 'Always packed with UTAR students which says a lot. My seafood ramen box was tasty and filling.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Secret Recipe (Sungai Long)', 16.90, 1, 'Cash', 'Pickup', 16.90, 'Completed');
SET @h10 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h10, 'Victoria', 'Secret Recipe (Sungai Long)', 5, 'Their cakes never disappoint! Got a slice of Tiramisu with my box, such a nice treat after a long study session.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'Secret Recipe (Sungai Long)', 16.90, 1, 'Cash', 'Pickup', 16.90, 'Completed');
SET @h11 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h11, 'WeiJianTan', 'Secret Recipe (Sungai Long)', 4, 'Good mix of savoury and sweet in my blind box. A bit pricier than other options nearby but worth it occasionally.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'Secret Recipe (Sungai Long)', 16.90, 1, 'Cash', 'Pickup', 16.90, 'Completed');
SET @h12 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h12, 'FarahN', 'Secret Recipe (Sungai Long)', 5, 'Perfect spot for a celebratory treat! My box had a lovely cake slice and a hearty main, great combo.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'Secret Recipe (Sungai Long)', 16.90, 1, 'Cash', 'Pickup', 16.90, 'Completed');
SET @h13 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h13, 'MarcusLee', 'Secret Recipe (Sungai Long)', 4, 'Consistent quality as always. Nice place to bring family when they visit during semester break.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'KFC Bandar Sungai Long', 10.90, 1, 'Cash', 'Pickup', 10.90, 'Completed');
SET @h14 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h14, 'Victoria', 'KFC Bandar Sungai Long', 4, 'Classic comfort food, my box had 2 pieces of chicken with coleslaw. Quick and convenient for a busy day.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'KFC Bandar Sungai Long', 10.90, 1, 'Cash', 'Pickup', 10.90, 'Completed');
SET @h15 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h15, 'Daniel93', 'KFC Bandar Sungai Long', 3, 'Standard KFC experience, nothing to complain about but nothing exciting either. Gets the job done.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'KFC Bandar Sungai Long', 10.90, 1, 'Cash', 'Pickup', 10.90, 'Completed');
SET @h16 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h16, 'PriyaS', 'KFC Bandar Sungai Long', 4, 'Good for a quick bite between classes. My box had the Hot & Spicy chicken which I really enjoyed.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'KFC Bandar Sungai Long', 10.90, 1, 'Cash', 'Pickup', 10.90, 'Completed');
SET @h17 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h17, 'FarahN', 'KFC Bandar Sungai Long', 5, 'Craving satisfied! Got a generous box with chicken, rice, and gravy. Great comfort food option near campus.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Domino''s Pizza (Sungai Long)', 19.90, 1, 'Cash', 'Pickup', 19.90, 'Completed');
SET @h18 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h18, 'Victoria', 'Domino''s Pizza (Sungai Long)', 4, 'Great for group study sessions, ordered a box to share and everyone loved the variety of slices.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'Domino''s Pizza (Sungai Long)', 19.90, 1, 'Cash', 'Pickup', 19.90, 'Completed');
SET @h19 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h19, 'Daniel93', 'Domino''s Pizza (Sungai Long)', 5, 'Best pizza deal near UTAR! My box had generous slices from two different pizzas plus garlic bread.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'Domino''s Pizza (Sungai Long)', 19.90, 1, 'Cash', 'Pickup', 19.90, 'Completed');
SET @h20 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h20, 'AisyahR', 'Domino''s Pizza (Sungai Long)', 4, 'Delivery was quick and pizza was still hot on arrival. Good variety in the blind box too.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('MarcusLee', 'Domino''s Pizza (Sungai Long)', 19.90, 1, 'Cash', 'Pickup', 19.90, 'Completed');
SET @h21 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h21, 'MarcusLee', 'Domino''s Pizza (Sungai Long)', 3, 'Pizza was good but I wish there was more variety in toppings for the price. Still a solid option overall.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Amitie Cafe', 15.90, 1, 'Cash', 'Pickup', 15.90, 'Completed');
SET @h22 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h22, 'Victoria', 'Amitie Cafe', 5, 'Such a cosy cafe for brunch! My box had their famous waffle and it did not disappoint.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('AisyahR', 'Amitie Cafe', 15.90, 1, 'Cash', 'Pickup', 15.90, 'Completed');
SET @h23 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h23, 'AisyahR', 'Amitie Cafe', 4, 'Nice ambience and friendly staff. Got a pasta dish in my box, simple but tasty.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('FarahN', 'Amitie Cafe', 15.90, 1, 'Cash', 'Pickup', 15.90, 'Completed');
SET @h24 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h24, 'FarahN', 'Amitie Cafe', 5, 'Perfect weekend brunch spot! Blind box included a lovely egg dish and coffee, great start to the day.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'K Cafe', 11.90, 1, 'Cash', 'Pickup', 11.90, 'Completed');
SET @h25 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h25, 'Victoria', 'K Cafe', 4, 'Interesting concept since even they don''t know what''s cooking until the day! Got a nice rice set this time.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Daniel93', 'K Cafe', 11.90, 1, 'Cash', 'Pickup', 11.90, 'Completed');
SET @h26 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h26, 'Daniel93', 'K Cafe', 3, 'Food is okay, more about the novelty of not knowing what you''ll get. Worth trying once.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'K Cafe', 11.90, 1, 'Cash', 'Pickup', 11.90, 'Completed');
SET @h27 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h27, 'WeiJianTan', 'K Cafe', 5, 'Surprisingly good! My box had a delicious noodle dish, way better than I expected for the price.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('Victoria', 'Restoran Ibrahim Maju Bistro', 8.90, 1, 'Cash', 'Pickup', 8.90, 'Completed');
SET @h28 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h28, 'Victoria', 'Restoran Ibrahim Maju Bistro', 4, 'Classic mamak experience, my box had roti canai and teh tarik. Simple, cheap, and satisfying.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('WeiJianTan', 'Restoran Ibrahim Maju Bistro', 8.90, 1, 'Cash', 'Pickup', 8.90, 'Completed');
SET @h29 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h29, 'WeiJianTan', 'Restoran Ibrahim Maju Bistro', 5, 'My favourite mamak near campus! Always fresh and the nasi campur box was a great variety of dishes.');

INSERT INTO history (username, restaurant_name, blind_box_price, quantity, payment_method, order_type, final_total, status)
VALUES ('PriyaS', 'Restoran Ibrahim Maju Bistro', 8.90, 1, 'Cash', 'Pickup', 8.90, 'Completed');
SET @h30 = LAST_INSERT_ID();
INSERT INTO reviews (history_id, username, restaurant_name, rating, review) VALUES
(@h30, 'PriyaS', 'Restoran Ibrahim Maju Bistro', 4, 'Good affordable option for a quick meal. Got a plate of mee goreng in my box, tasty and filling.');

INSERT INTO enquiries (username, customer_name, customer_email, subject, message, status)
VALUES (
    'Victoria',
    'Victoria',
    'vichaha@gmail.com',
    'Order Support - Missing item in my blind box',
    'Hi, I ordered a blind box from Sushi Mentai earlier today for pickup, but when I opened it there were only 2 sushi rolls instead of the mix of sashimi, nigiri, and maki described. Could you please look into this and let me know if I can get the missing items or a refund for the difference? Thank you.',
    'New'
);