<?php
session_start();
include 'config/db_connect.php';
require_once 'includes/vouchers.php';

if (empty($_SESSION['promotion_csrf'])) {
    $_SESSION['promotion_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_promotion'])) {
    $token = $_POST['csrf_token'] ?? '';
    $promotion_id = (int) ($_POST['promotion_id'] ?? 0);

    if (!hash_equals($_SESSION['promotion_csrf'], $token)) {
        $_SESSION['promotion_message'] = 'Your session expired. Please try again.';
    } elseif (empty($_SESSION['username'])) {
        $_SESSION['promotion_message'] = 'Please log in before claiming a voucher.';
    } else {
        $promotion_stmt = $conn->prepare(
            "SELECT promotion_id FROM promotions
             WHERE promotion_id = ? AND is_active = 1
               AND NOW() BETWEEN starts_at AND ends_at"
        );
        $promotion_stmt->bind_param('i', $promotion_id);
        $promotion_stmt->execute();
        $valid_promotion = $promotion_stmt->get_result()->num_rows === 1;
        $promotion_stmt->close();

        $_SESSION['promotion_message'] = $valid_promotion
            && claim_promotion($conn, $_SESSION['username'], $promotion_id)
            ? 'Voucher claimed! You can apply it in your cart.'
            : 'This promotion is no longer available.';
    }

    header('Location: index.php#promotions');
    exit();
}

$promotion_message = $_SESSION['promotion_message'] ?? '';
unset($_SESSION['promotion_message']);

$promotions = [];
$promotion_result = $conn->query(
    "SELECT * FROM promotions
     WHERE is_active = 1 AND NOW() BETWEEN starts_at AND ends_at
     ORDER BY discount_value DESC, promotion_id ASC"
);
while ($promotion_result && $promotion = $promotion_result->fetch_assoc()) {
    $promotions[] = $promotion;
}

$claimed_promotions = [];
if (!empty($_SESSION['username'])) {
    $claimed_stmt = $conn->prepare(
        "SELECT promotion_id, used_at FROM user_vouchers WHERE username = ?"
    );
    $claimed_stmt->bind_param('s', $_SESSION['username']);
    $claimed_stmt->execute();
    $claimed_result = $claimed_stmt->get_result();
    while ($claimed = $claimed_result->fetch_assoc()) {
        $claimed_promotions[(int) $claimed['promotion_id']] = $claimed['used_at'];
    }
    $claimed_stmt->close();
}
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

    <section class="home-promotions" id="promotions" aria-labelledby="promotionsTitle">
        <div class="promotion-heading">
            <div>
                <span class="home-section-kicker">Available now</span>
                <h2 id="promotionsTitle">Promotions</h2>
            </div>
            <p>More surprise, less spend. Pick an offer for your next Blind Bite.</p>
        </div>

        <?php if ($promotion_message !== '') : ?>
            <p class="promotion-message"><?php echo htmlspecialchars($promotion_message); ?></p>
        <?php endif; ?>

        <div class="promotion-grid">
            <?php foreach ($promotions as $index => $promotion) : ?>
                <?php
                $promotion_id = (int) $promotion['promotion_id'];
                $is_claimed = array_key_exists($promotion_id, $claimed_promotions);
                $is_used = $is_claimed && $claimed_promotions[$promotion_id] !== null;
                ?>
                <article class="promotion-card <?php echo $index === 0 ? 'promotion-card-featured' : ''; ?>">
                    <span class="promotion-icon" aria-hidden="true">🎁</span>
                    <div class="promotion-copy">
                        <span class="promotion-tag">Available voucher</span>
                        <h3><?php echo htmlspecialchars($promotion['title']); ?></h3>
                        <p><?php echo htmlspecialchars($promotion['description']); ?></p>
                        <span class="promotion-code">
                            Code <strong><?php echo htmlspecialchars($promotion['code']); ?></strong>
                            · Min. RM<?php echo number_format((float) $promotion['minimum_spend'], 2); ?>
                        </span>
                    </div>

                    <?php if ($is_used) : ?>
                        <span class="promotion-link promotion-claimed">Used</span>
                    <?php elseif ($is_claimed) : ?>
                        <a href="pages/cart.php" class="promotion-link">Use in cart →</a>
                    <?php else : ?>
                        <form method="POST" action="index.php#promotions" class="promotion-claim-form">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['promotion_csrf']); ?>">
                            <input type="hidden" name="promotion_id" value="<?php echo $promotion_id; ?>">
                            <button type="submit" name="claim_promotion" class="promotion-link">Claim voucher →</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>


    <?php
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
