<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';


// ============================================================
// RESTAURANT COUNT
// ============================================================

$restaurant_count = 0;

$restaurant_result = $conn->query("SELECT COUNT(*) AS total FROM restaurants");

if ($restaurant_result) {
    $restaurant_count = (int) $restaurant_result->fetch_assoc()['total'];
}


// ============================================================
// ORDER COUNT
// ============================================================

$order_count = 0;

$order_result = $conn->query("SELECT COUNT(*) AS total FROM history");

if ($order_result) {
    $order_count = (int) $order_result->fetch_assoc()['total'];
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blind Bite</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

    <div class="admin-page">

        <div class="admin-card">

            <div class="admin-header">
                <h1>🍱 BLIND BITE ADMIN</h1>
            </div>

            <div class="admin-body">

                <p class="admin-welcome">Welcome, Admin</p>

                <div class="admin-stats">

                    <div class="stat-card">
                        <span class="stat-label">Restaurants</span>
                        <span class="stat-number"><?php echo $restaurant_count; ?></span>
                    </div>

                    <div class="stat-card">
                        <span class="stat-label">Orders</span>
                        <span class="stat-number"><?php echo $order_count; ?></span>
                    </div>

                </div>

                <a href="restaurants.php" class="manage-btn">
                    Manage Restaurants
                </a>

                <a href="logout.php" class="admin-logout-link">
                    Log Out
                </a>

            </div>

        </div>

    </div>

</body>
</html>