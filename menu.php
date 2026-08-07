<?php
include 'config/db_connect.php';
include 'includes/header.php';
include 'includes/navigation.php';

// Get all restaurants
$sql = "SELECT * FROM restaurants";
$result = $conn->query($sql);
?>

<main class="menu-page">

    <h1>Blind Box Restaurants</h1>

    <div class="restaurant-container">

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

                <div class="restaurant-card">

                    <!-- Restaurant Image -->
                    <img src="images/BBbox.png" width="200" height="150" alt="Blind Box">

                    <div class="restaurant-info">

                        <h2>
                            <?php echo htmlspecialchars($row['restaurant_name']); ?>
                        </h2>

                        <p>
                            <strong>Opening Hours:</strong>
                            <?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>
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
                            <strong>Remaining Quantity:</strong>
                            <?php echo $row['blind_box_remaining_quantity']; ?> Boxes
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

<?php
        $conn->close();
        include 'includes/footer.php';