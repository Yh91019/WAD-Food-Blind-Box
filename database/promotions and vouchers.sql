USE blindbite;

CREATE TABLE IF NOT EXISTS promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(40) NOT NULL UNIQUE,
    title VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL,
    discount_type ENUM('Percentage', 'Fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_spend DECIMAL(10,2) NOT NULL DEFAULT 0,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_vouchers (
    user_voucher_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    promotion_id INT NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_user_promotion (username, promotion_id),
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotions(promotion_id) ON DELETE CASCADE
);

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'history' AND COLUMN_NAME = 'voucher_code') = 0,
    'ALTER TABLE history ADD COLUMN voucher_code VARCHAR(40) DEFAULT NULL AFTER order_type',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'history' AND COLUMN_NAME = 'discount_amount') = 0,
    'ALTER TABLE history ADD COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER voucher_code',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'history' AND COLUMN_NAME = 'delivery_fee') = 0,
    'ALTER TABLE history ADD COLUMN delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER discount_amount',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'history' AND COLUMN_NAME = 'final_total') = 0,
    'ALTER TABLE history ADD COLUMN final_total DECIMAL(10,2) DEFAULT NULL AFTER delivery_fee',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO promotions
    (code, title, description, discount_type, discount_value, minimum_spend, starts_at, ends_at, is_active)
VALUES
    ('FIRSTBITE20', '20% Off Your First Bite', 'Save 20% on a Blind Bite order of RM10 or more.', 'Percentage', 20.00, 10.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('SAVE5', 'RM5 Blind Bite Voucher', 'Save RM5 when your cart reaches RM20.', 'Fixed', 5.00, 20.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
    ('NIGHTBITE5', 'RM5 Evening Treat', 'Save RM5 on orders of RM15 or more.', 'Fixed', 5.00, 15.00, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1)
ON DUPLICATE KEY UPDATE
    title = VALUES(title), description = VALUES(description),
    discount_type = VALUES(discount_type), discount_value = VALUES(discount_value),
    minimum_spend = VALUES(minimum_spend), starts_at = VALUES(starts_at),
    ends_at = VALUES(ends_at), is_active = VALUES(is_active);
