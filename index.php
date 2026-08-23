<?php
session_start();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/index.css">

<?php
if(isset($_SESSION['login_message'])){
?>

<div id="login-popup">
    <?php echo $_SESSION['login_message']; ?>
</div>

<?php
unset($_SESSION['login_message']);
}
?>

<?php
if(isset($_SESSION['logout_message'])){
?>

<div id="logout-popup">
    <?php echo  $_SESSION['logout_message']; ?>
</div>

<?php
unset($_SESSION['logout_message']);
}
?>

<main>
    <section class="home-hero">
        <img src="images/bg.jpg" alt="Blind Bite">
    </section>

    <section class="home-promotions" aria-labelledby="promotionsTitle">
        <div class="promotion-heading">
            <div>
                <span class="home-section-kicker">Available now</span>
                <h2 id="promotionsTitle">Promotions</h2>
            </div>
            <p>More surprise, less spend. Pick an offer for your next Blind Bite.</p>
        </div>

        <div class="promotion-grid">
            <article class="promotion-card promotion-card-featured">
                <span class="promotion-icon" aria-hidden="true">🎁</span>
                <div class="promotion-copy">
                    <span class="promotion-tag">New foodies</span>
                    <h3>20% Off Your First Bite</h3>
                    <p>Start your food-saving journey with a delicious surprise.</p>
                    <span class="promotion-code">Use code <strong>FIRSTBITE20</strong></span>
                </div>
                <a href="pages/menu.php" class="promotion-link">Claim offer →</a>
            </article>

            <article class="promotion-card">
                <span class="promotion-icon" aria-hidden="true">🛵</span>
                <div class="promotion-copy">
                    <span class="promotion-tag">Delivery deal</span>
                    <h3>Free Delivery</h3>
                    <p>Enjoy delivery on orders of RM40 or more.</p>
                    <span class="promotion-code">Applied at checkout</span>
                </div>
                <a href="pages/menu.php" class="promotion-link">Browse boxes →</a>
            </article>

            <article class="promotion-card">
                <span class="promotion-icon" aria-hidden="true">⏰</span>
                <div class="promotion-copy">
                    <span class="promotion-tag">Happy hour</span>
                    <h3>RM5 Evening Treat</h3>
                    <p>Save RM5 on a Blind Box ordered from 8–10 PM.</p>
                    <span class="promotion-code">Use code <strong>NIGHTBITE5</strong></span>
                </div>
                <a href="pages/menu.php" class="promotion-link">Explore now →</a>
            </article>
        </div>
    </section>


    <?php
    include 'config/db_connect.php';

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
        LIMIT 5
    ";

    $result = $conn->query($sql);

    $top_restaurants = [];

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $top_restaurants[] = $row;
        }

    }

    // Renders a single "Top 5" restaurant card. Used twice for each
    // restaurant (see the carousel track below) so the auto-scroll
    // animation can loop seamlessly, the same technique used for the
    // review carousel on the details page.
    function render_home_restaurant_card(array $row, bool $hidden = false): void {
    ?>

        <a href="pages/details.php?restaurant=<?php
            echo urlencode($row['restaurant_name']);
        ?>" class="home-restaurant-card"<?php echo $hidden ? ' aria-hidden="true" tabindex="-1"' : ''; ?>>


            <!-- Restaurant Image -->

            <div class="home-restaurant-image">

                <img
                    src="<?php echo htmlspecialchars(restaurant_image_url($row['blind_box_image'] ?? null)); ?>"
                    alt="<?php echo htmlspecialchars($row['restaurant_name']); ?> blind box"
                >

            </div>


            <!-- Restaurant Information -->

            <div class="home-restaurant-info">

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $row['restaurant_name']
                    );
                    ?>
                </h2>


                <p class="restaurant-category">
                    <?php
                    echo htmlspecialchars(
                        $row['blind_box_food_category']
                    );
                    ?>
                </p>

                <p class="home-restaurant-rating">
                    <span>★ <?php echo number_format((float) $row['average_rating'], 1); ?>/5.0</span>
                    <small>
                        <?php echo (int) $row['review_count']; ?>
                        <?php echo (int) $row['review_count'] === 1 ? 'review' : 'reviews'; ?>
                    </small>
                </p>


                <p class="restaurant-price">
                    RM
                    <?php
                    echo number_format(
                        $row['blind_box_price'],
                        2
                    );
                    ?>
                </p>


                <p class="restaurant-location">
                    <?php
                    echo htmlspecialchars(
                        $row['restaurant_address']
                    );
                    ?>
                </p>

            </div>

        </a>

    <?php
    }
    ?>

    <section class="home-restaurants">
        <div class="restaurants-header">

            <h1>Top 5 Highest-Rated Restaurants</h1>

            <p>
                Discover the restaurants our customers rate most highly
            </p>

        </div>

        <?php if (empty($top_restaurants)) : ?>

            <p class="no-restaurants">
                No restaurants available.
            </p>

        <?php else : ?>

            <!-- Auto-scrolling carousel, same technique as the review
                 carousel on the details page: the mask fades the edges,
                 and the card list is rendered twice so the animation
                 can loop seamlessly from the duplicate back to the start. -->
            <div
                class="home-restaurants-carousel"
                id="homeRestaurantCarousel"
                role="region"
                aria-label="Top 5 highest-rated restaurants, automatically scrolling"
            >
                <div class="home-restaurant-container" id="homeRestaurantTrack">

                    <?php foreach ($top_restaurants as $row) : ?>
                        <?php render_home_restaurant_card($row); ?>
                    <?php endforeach; ?>

                    <?php foreach ($top_restaurants as $row) : ?>
                        <?php render_home_restaurant_card($row, true); ?>
                    <?php endforeach; ?>

                </div>
            </div>

        <?php endif; ?>


        <!-- View All Button -->

        <div class="view-all-container">

            <a href="pages/menu.php" class="view-all-btn">
                View All Restaurants
            </a>

        </div>

    </section>
</main>
<script src="<?php echo BASE_URL; ?>/js/home-restaurants-carousel.js"></script>
<?php
$conn->close();
include('includes/footer.php');
?>
