<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';


// ============================================================
// RESTAURANT COUNT
// ============================================================

$restaurant_count = 0;

$restaurant_result = $conn->query(
    "SELECT COUNT(*) AS total FROM restaurants"
);

if ($restaurant_result) {
    $restaurant_count =
        (int) $restaurant_result->fetch_assoc()['total'];
}


// ============================================================
// ORDER COUNT
// ============================================================

$order_count = 0;

$order_result = $conn->query(
    "SELECT COUNT(*) AS total FROM history"
);

if ($order_result) {
    $order_count =
        (int) $order_result->fetch_assoc()['total'];
}


$conn->close();

?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/adminNavigation.php'; ?>


<div class="admin-page">

    <div class="admin-card">

        <div class="admin-header">

            <h1>BLIND BITE ADMIN</h1>

        </div>


        <div class="admin-body">

            <p class="admin-welcome">
                Welcome, Admin
            </p>


            <div class="admin-stats">

                <div class="stat-card">

                    <span class="stat-label">
                        Restaurants
                    </span>

                    <span class="stat-number">
                        <?php echo $restaurant_count; ?>
                    </span>

                </div>


                <div class="stat-card">

                    <span class="stat-label">
                        Orders
                    </span>

                    <span class="stat-number">
                        <?php echo $order_count; ?>
                    </span>

                </div>

            </div>


            <div class="admin-actions">
                <a href="restaurants.php" class="manage-btn">
                    Manage Restaurants
                </a>

                <a href="../authentication/logout.php" class="admin-logout-link">
                    Log Out
                </a>

            </div>

        </div>

    </div>

</div>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css">
<?php include '../includes/footer.php'; ?>