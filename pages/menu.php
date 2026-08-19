<?php
include '../config/db_connect.php';

// Get all restaurants
$sql = "SELECT * FROM restaurants";
$result = $conn->query($sql);

include '../includes/header.php';
include '../includes/navigation.php';
?>

<link rel="stylesheet" href="../css/status.css">

<main class="menu-page">

    <h1>Blind Box Restaurants</h1>

    <div class="menu-search-bar">

        <input
            type="text"
            id="menuSearchInput"
            placeholder="Search by restaurant name or food category..."
        >

    </div>

    <p id="menuNoResults" class="menu-no-results">
        No restaurants match your search.
    </p>

    <div class="restaurant-container" id="restaurantContainer">

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

                <div
                    class="restaurant-card"
                    data-name="<?php echo strtolower(htmlspecialchars($row['restaurant_name'])); ?>"
                    data-category="<?php echo strtolower(htmlspecialchars($row['blind_box_food_category'])); ?>"
                >

                    <!-- Restaurant Image -->
                    <img src="../images/BBbox.png" width="200" height="150" alt="Blind Box">

                    <div class="restaurant-info">

                        <h2>
                            <?php echo htmlspecialchars($row['restaurant_name']); ?>
                            <span
                                class="status-badge"
                                data-opening="<?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>"
                            >Checking...</span>
                        </h2>

                        <p>
                            <strong>Opening Hours:</strong>
                            <?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>
                        </p>

                        <p>
                            <strong>Closing Hours:</strong>
                            <?php echo htmlspecialchars($row['restaurant_closing_hours']); ?>
                        </p>

                        <p>
                            <strong>Location:</strong>
                            <?php echo htmlspecialchars($row['restaurant_address']); ?>
                        </p>

                        <p>
                            <strong>Blind Box Price:</strong>
                            RM <?php echo number_format($row['blind_box_price'], 2); ?>
                        </p>

                        <p>
                            <strong>Food Category:</strong>
                            <?php echo htmlspecialchars($row['blind_box_food_category']); ?>
                        </p>

                        <a href="details.php?restaurant=<?php echo urlencode($row['restaurant_name']); ?>" class="details-btn">
                            Details
                        </a>

                    </div>

                </div>

        <?php
            }
        } else {
            echo "<h2>No restaurants available.</h2>";
        }
        ?>

    </div>

</main>

<script src="../js/script.js"></script>
<script src="../js/status.js"></script>
<script src="../js/menu-search.js"></script>

<?php
        $conn->close();
        include '../includes/footer.php';
?>
