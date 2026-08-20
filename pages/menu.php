<?php
include '../config/db_connect.php';

// Get all restaurants with their average customer rating.
$sql = "
    SELECT
        restaurants.*,
        COALESCE(review_summary.average_rating, 0) AS average_rating,
        COALESCE(review_summary.review_count, 0) AS review_count
    FROM restaurants
    LEFT JOIN (
        SELECT
            restaurant_name,
            AVG(rating) AS average_rating,
            COUNT(*) AS review_count
        FROM reviews
        GROUP BY restaurant_name
    ) AS review_summary
        ON restaurants.restaurant_name = review_summary.restaurant_name
    ORDER BY
        average_rating DESC,
        review_count DESC,
        restaurants.restaurant_name ASC
";
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

        <label class="rating-sort-toggle" for="ratingSort">
            <span class="rating-sort-title">Rating</span>
            <input
                type="checkbox"
                id="ratingSort"
                aria-label="Rating: highest to lowest. Toggle for lowest to highest."
            >
            <span class="rating-toggle-track" aria-hidden="true">
                <span class="rating-toggle-thumb"></span>
            </span>
            <span id="ratingSortDirection" class="rating-sort-direction">↑</span>
        </label>

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
                    data-rating="<?php echo htmlspecialchars((string) $row['average_rating']); ?>"
                    data-review-count="<?php echo (int) $row['review_count']; ?>"
                >

                    <!-- Restaurant Image -->
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

                        <p class="restaurant-rating">
                            <strong>Rating:</strong>
                            <span aria-label="<?php echo number_format((float) $row['average_rating'], 1); ?> out of 5">
                                ★ <?php echo number_format((float) $row['average_rating'], 1); ?>/5.0
                            </span>
                            <small>
                                (<?php echo (int) $row['review_count']; ?>
                                <?php echo (int) $row['review_count'] === 1 ? 'review' : 'reviews'; ?>)
                            </small>
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
