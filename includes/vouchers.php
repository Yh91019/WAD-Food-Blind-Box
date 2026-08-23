<?php

function voucher_discount_amount(array $promotion, float $subtotal): float
{
    if ($subtotal < (float) $promotion['minimum_spend']) {
        return 0.0;
    }

    $discount = $promotion['discount_type'] === 'Percentage'
        ? $subtotal * ((float) $promotion['discount_value'] / 100)
        : (float) $promotion['discount_value'];

    return round(min($subtotal, max(0, $discount)), 2);
}

function active_promotion_by_code(mysqli $conn, string $code): ?array
{
    $code = strtoupper(trim($code));
    if ($code === '') {
        return null;
    }

    $stmt = $conn->prepare(
        "SELECT * FROM promotions
         WHERE code = ? AND is_active = 1
           AND NOW() BETWEEN starts_at AND ends_at
         LIMIT 1"
    );
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $promotion = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    return $promotion;
}

function claim_promotion(mysqli $conn, string $username, int $promotion_id): bool
{
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO user_vouchers (username, promotion_id)
         VALUES (?, ?)"
    );
    $stmt->bind_param('si', $username, $promotion_id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function unused_user_promotion(mysqli $conn, string $username, string $code): ?array
{
    $stmt = $conn->prepare(
        "SELECT promotions.*, user_vouchers.user_voucher_id
         FROM user_vouchers
         INNER JOIN promotions ON promotions.promotion_id = user_vouchers.promotion_id
         WHERE user_vouchers.username = ?
           AND promotions.code = ?
           AND user_vouchers.used_at IS NULL
           AND promotions.is_active = 1
           AND NOW() BETWEEN promotions.starts_at AND promotions.ends_at
         LIMIT 1"
    );
    $stmt->bind_param('ss', $username, $code);
    $stmt->execute();
    $promotion = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $promotion;
}
