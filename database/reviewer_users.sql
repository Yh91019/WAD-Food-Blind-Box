USE blindbite;

SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'user_type'
    ) = 0,
    'ALTER TABLE users ADD COLUMN user_type ENUM(''CUSTOMER'', ''REVIEWER'') NOT NULL DEFAULT ''CUSTOMER'' AFTER phone_number',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO users
(
    username,
    password,
    email,
    gender,
    date_of_birth,
    address,
    phone_number,
    user_type
)
VALUES
(
    'john',
    'john123',
    'john@gmail.com',
    'MALE',
    '2002-05-15',
    'Kuala Lumpur, Malaysia',
    '0123456789',
    'REVIEWER'
),
(
    'jane',
    'jane123',
    'jane@gmail.com',
    'FEMALE',
    '2003-08-20',
    'Petaling Jaya, Malaysia',
    '0134567890',
    'REVIEWER'
)
ON DUPLICATE KEY UPDATE
    user_type = VALUES(user_type);


INSERT INTO history
(
    username,
    restaurant_name,
    blind_box_price,
    quantity,
    payment_method,
    order_type,
    discount_amount,
    delivery_fee,
    final_total,
    status
)
SELECT
    u.username,
    r.restaurant_name,
    r.blind_box_price,
    1,
    'Review Only',
    'Pickup',
    0.00,
    0.00,
    r.blind_box_price,
    'Completed'
FROM users u
CROSS JOIN restaurants r
WHERE u.username IN ('john', 'jane')
  AND u.user_type = 'REVIEWER'
  AND NOT EXISTS
  (
      SELECT 1
      FROM history h
      WHERE h.username = u.username
        AND h.restaurant_name = r.restaurant_name
        AND h.payment_method = 'Review Only'
  );