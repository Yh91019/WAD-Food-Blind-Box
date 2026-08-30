USE blindbite;

-- Store only the filename. The application adds images/restaurants/ when
-- generating each restaurant image URL.
UPDATE restaurants
SET blind_box_image = CASE restaurant_name
    WHEN 'Myosotis Cafe' THEN 'Myosotis Cafe.webp'
    WHEN 'Sushi Mentai' THEN 'Sushi Mentai.webp'
    WHEN 'QQ Pan Mee & Ramen Restaurant' THEN 'QQ Pan Mee & Ramen Restaurant.jpg'
    ELSE blind_box_image
END
WHERE restaurant_name IN (
    'Myosotis Cafe',
    'Sushi Mentai',
    'QQ Pan Mee & Ramen Restaurant'
);

