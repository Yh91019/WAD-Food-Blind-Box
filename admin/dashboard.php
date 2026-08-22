<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

$result = $conn->query(
    "SELECT * FROM restaurants ORDER BY restaurant_name"
);
?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/adminNavigation.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/status.css">

<section class="admin-page">

    <div class="admin-card">

        <div class="admin-header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="admin-body">

            <div class="restaurant-page-actions">

                <a href="restaurants.php" class="admin-action-btn add-btn">
                    Manage Restaurant
                </a>

            </div>

            <div class="restaurant-search">
                <input
                    type="text"
                    id="menuSearchInput"
                    placeholder="Search by restaurant name or food category..."
                    autocomplete="off"
                >
            </div>

            <p id="menuNoResults" class="menu-no-results">
                No restaurants match your search.
            </p>

            <div class="restaurant-container" id="restaurantContainer">

        <?php if ($result && $result->num_rows > 0) : ?>

            <?php while ($row = $result->fetch_assoc()) : ?>

                <div
                    class="restaurant-card"
                    data-name="<?php echo htmlspecialchars(strtolower($row['restaurant_name'])); ?>"
                    data-category="<?php echo htmlspecialchars(strtolower($row['blind_box_food_category'])); ?>"
                >
                    <img
                        src="<?php echo htmlspecialchars(restaurant_image_url($row['blind_box_image'] ?? null)); ?>"
                        width="200"
                        height="150"
                        alt="<?php echo htmlspecialchars($row['restaurant_name']); ?> blind box"
                    >

                    <div class="restaurant-info">
                        <h2>
                            <?php echo htmlspecialchars($row['restaurant_name']); ?>
                            <span
                                class="status-badge"
                                data-opening="<?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>"
                                data-closing="<?php echo htmlspecialchars($row['restaurant_closing_hours']); ?>"
                            >Checking...</span>
                        </h2>

                        <p><strong>Opening Hours:</strong>
                            <?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>
                        </p>
                        <p><strong>Closing Hours:</strong>
                            <?php echo htmlspecialchars($row['restaurant_closing_hours']); ?>
                        </p>
                        <p><strong>Location:</strong>
                            <?php echo htmlspecialchars($row['restaurant_address']); ?>
                        </p>
                        <p><strong>Phone:</strong>
                            <?php echo htmlspecialchars($row['restaurant_phone_number']); ?>
                        </p>
                        <p><strong>Blind Box Price:</strong>
                            RM <?php echo number_format($row['blind_box_price'], 2); ?>
                        </p>
                        <p><strong>Food Category:</strong>
                            <?php echo htmlspecialchars($row['blind_box_food_category']); ?>
                        </p>
                        
                    </div>
                </div>

            <?php endwhile; ?>

        <?php else : ?>

            <h2>No restaurants available.</h2>

        <?php endif; ?>

            </div>

        </div>

    </div>

</section>

<script src="<?php echo BASE_URL; ?>/js/status.js"></script>
<script src="<?php echo BASE_URL; ?>/js/menu-search.js"></script>

<?php
$conn->close();
include '../includes/footer.php';
?>
