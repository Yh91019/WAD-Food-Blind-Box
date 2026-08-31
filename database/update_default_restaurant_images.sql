USE blindbite;

-- Store only the filename. The application adds images/restaurants/ when
-- generating each restaurant image URL.
UPDATE restaurants
SET blind_box_image = CASE restaurant_name
   
    WHEN 'Amitie Cafe' THEN 'Amitie Cafe.jpg'
    WHEN 'Domino Pizza (Sungai Long)' THEN 'domino.jpg'
    WHEN 'K Cafe' THEN 'K Cafe.webp'
    WHEN 'KFC Bandar Sungai Long' THEN 'KFC Bandar Sungai Long.wbep'
    WHEN 'Myosotis Cafe' THEN 'Myosotis Cafe.webp'
    WHEN 'QQ Pan Mee & Ramen Restaurant' THEN 'QQ Pan Mee & Ramen Restaurant.jpg'
    WHEN 'Restoran Ibrahim Maju Bistro' THEN 'Restoran Ibrahim Maju Bistro.jpg'
    WHEN 'Secret Recipe (Sg Long)' THEN 'Secret Recipe (Sg Long).webp'
    WHEN 'Sushi Mentai' THEN 'Sushi Mentai.webp'
    ELSE blind_box_image
END
WHERE restaurant_name IN (
    'Amitie Cafe',
    'Domino Pizza (Sungai Long)',
    'K Cafe',
    'KFC Bandar Sungai Long',
    'Myosotis Cafe',
    'QQ Pan Mee & Ramen Restaurant',
    'Restoran Ibrahim Maju Bistro',
    'Secret Recipe (Sg Long)',
    'Sushi Mentai'

);

