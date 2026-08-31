USE blindbite;

-- Store only the filename. The application adds images/restaurants/ when
-- generating each restaurant image URL.
UPDATE restaurants
SET blind_box_image = CASE restaurant_name
   
    WHEN 'Amitie Cafe' THEN 'Amitie Cafe.jpg'
    WHEN 'Domino (Sg Long)' THEN 'Domino (Sg Long).webp'
    WHEN 'K Cafe' THEN 'K Cafe.webp'
    WHEN 'KFC Bandar Sungai Long' THEN 'KFC Bandar Sungai Long.webp'
    WHEN 'Myosotis Cafe' THEN 'Myosotis Cafe.webp'
    WHEN 'QQ Pan Mee & Ramen Restaurant' THEN 'QQ Pan Mee & Ramen Restaurant.jpg'
    WHEN 'Restaurant Ibrahim Maju Bistro' THEN 'Restaurant Ibrahim Maju Bistro.webp'
    WHEN 'Secret Recipe (Sg Long)' THEN 'Secret Recipe (Sg Long).webp'
    WHEN 'Sushi Mentai' THEN 'Sushi Mentai.webp'
    ELSE blind_box_image
END
WHERE restaurant_name IN (
    'Amitie Cafe',
    'Domino (Sg Long)',
    'K Cafe',
    'KFC Bandar Sungai Long',
    'Myosotis Cafe',
    'QQ Pan Mee & Ramen Restaurant',
    'Restaurant Ibrahim Maju Bistro',
    'Secret Recipe (Sg Long)',
    'Sushi Mentai'

);

