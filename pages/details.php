<?php

session_start();

include '../config/db_connect.php';
require_once '../includes/restaurant_image.php';


// ============================================================
// GET RESTAURANT
// ============================================================

$restaurant_name = isset($_GET['restaurant'])
    ? $_GET['restaurant']
    : '';

$sql = "
    SELECT *
    FROM restaurants
    WHERE restaurant_name = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $restaurant_name
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 1) {

    $restaurant = $result->fetch_assoc();

} else {

    $restaurant = null;

}


$stmt->close();

// ============================================================
// STORE OPEN/CLOSED STATUS
// ============================================================

$store_is_open = $restaurant
    ? is_restaurant_open(
        $restaurant['restaurant_opening_hours'],
        $restaurant['restaurant_closing_hours']
    )
    : false;

$reviews = [];
$average_rating = 0.0;

if ($restaurant) {
    $review_stmt = $conn->prepare(
        "SELECT username, rating, review, created_at
         FROM reviews
         WHERE restaurant_name = ?
         ORDER BY created_at DESC"
    );
    $review_stmt->bind_param('s', $restaurant['restaurant_name']);
    $review_stmt->execute();
    $review_result = $review_stmt->get_result();
    $rating_total = 0;

    while ($review_row = $review_result->fetch_assoc()) {
        $rating_total += (int) $review_row['rating'];
        $reviews[] = $review_row;
    }

    if (count($reviews) > 0) {
        $average_rating = $rating_total / count($reviews);
    }

    $review_stmt->close();
}


// ============================================================
// REVIEW CARD
// ============================================================

function render_review_card(array $review_item, bool $hidden = false): void {
    ?>
    <article class="review-card"<?php echo $hidden ? ' aria-hidden="true" tabindex="-1"' : ''; ?>>
        <div class="review-card-heading">
            <div class="reviewer">
                <span class="reviewer-avatar" aria-hidden="true">
                    <?php echo htmlspecialchars(strtoupper(substr($review_item['username'], 0, 1))); ?>
                </span>
                <div>
                    <strong><?php echo htmlspecialchars($review_item['username']); ?></strong>
                    <small>
                        <?php echo date('d F Y', strtotime($review_item['created_at'])); ?>
                    </small>
                </div>
            </div>
            <span class="review-rating">★ <?php echo (int) $review_item['rating']; ?>.0</span>
        </div>
        <p class="review-text"><?php echo nl2br(htmlspecialchars($review_item['review'])); ?></p>
    </article>
    <?php
}


// ============================================================
// MESSAGES
// ============================================================

$cart_message = "";

$wishlist_message = "";


// ============================================================
// ADD TO CART
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['add_to_cart'])
) {


    if (!isset($_SESSION['username'])) {

        $cart_message =
            "Please log in before adding items to cart.";

    }

    elseif (!$store_is_open) {

        $cart_message =
            "This restaurant is currently closed. You can't add it to your cart right now, but you can still add it to your wishlist.";

    }

    elseif ($restaurant) {


        $username =
            $_SESSION['username'];

        $restaurant_name_db =
            $restaurant['restaurant_name'];

        // ====================================================
        // QUANTITY (from the quantity selector, default 1)
        // ====================================================

        $quantity_to_add =
            isset($_POST['quantity'])
            ? (int) $_POST['quantity']
            : 1;

        if ($quantity_to_add < 1) {
            $quantity_to_add = 1;
        }

        // ====================================================
        // CHECK IF RESTAURANT ALREADY IN CART
        // ====================================================

        $check_sql = "
            SELECT cart_id, quantity
            FROM cart
            WHERE username = ?
            AND restaurant_name = ?
        ";

        $check_stmt =
            $conn->prepare($check_sql);

        $check_stmt->bind_param(
            "ss",
            $username,
            $restaurant_name_db
        );

        $check_stmt->execute();

        $check_result =
            $check_stmt->get_result();


        // ====================================================
        // ALREADY IN CART
        // ====================================================

        if ($check_result->num_rows > 0) {


            $update_sql = "
                UPDATE cart
                SET quantity = quantity + ?
                WHERE username = ?
                AND restaurant_name = ?
            ";

            $update_stmt =
                $conn->prepare($update_sql);

            $update_stmt->bind_param(
                "iss",
                $quantity_to_add,
                $username,
                $restaurant_name_db
            );

            $update_stmt->execute();

            $update_stmt->close();


            $cart_message =
                "Cart quantity updated!";


        }

        // ====================================================
        // NEW CART ITEM
        // ====================================================

        else {


            $insert_sql = "
                INSERT INTO cart
                (
                    username,
                    restaurant_name,
                    quantity
                )
                VALUES (?, ?, ?)
            ";

            $insert_stmt =
                $conn->prepare($insert_sql);

            $insert_stmt->bind_param(
                "ssi",
                $username,
                $restaurant_name_db,
                $quantity_to_add
            );

            $insert_stmt->execute();

            $insert_stmt->close();


            $cart_message =
                "Added to cart successfully!";

        }


        $check_stmt->close();

    }

}


// ============================================================
// ADD TO WISHLIST
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['add_to_wishlist'])
) {


    if (!isset($_SESSION['username'])) {

        $wishlist_message =
            "Please log in before adding items to wishlist.";

    }

    elseif ($restaurant) {


        $username =
            $_SESSION['username'];

        $restaurant_name_db =
            $restaurant['restaurant_name'];


        // ====================================================
        // CHECK IF ALREADY IN WISHLIST
        // ====================================================

        $check_sql = "
            SELECT wishlist_id
            FROM wishlist
            WHERE username = ?
            AND restaurant_name = ?
        ";

        $check_stmt =
            $conn->prepare($check_sql);

        $check_stmt->bind_param(
            "ss",
            $username,
            $restaurant_name_db
        );

        $check_stmt->execute();

        $check_result =
            $check_stmt->get_result();


        // ====================================================
        // ALREADY EXISTS
        // ====================================================

        if ($check_result->num_rows > 0) {


            $wishlist_message =
                "This restaurant is already in your wishlist.";


        }

        // ====================================================
        // ADD NEW WISHLIST ITEM
        // ====================================================

        else {


            $insert_sql = "
                INSERT INTO wishlist
                (
                    username,
                    restaurant_name
                )
                VALUES (?, ?)
            ";

            $insert_stmt =
                $conn->prepare($insert_sql);

            $insert_stmt->bind_param(
                "ss",
                $username,
                $restaurant_name_db
            );

            $insert_stmt->execute();

            $insert_stmt->close();


            $wishlist_message =
                "Added to wishlist successfully!";

        }


        $check_stmt->close();

    }

}   

// ============================================================
// HEADER
// ============================================================

include '../includes/header.php';

include '../includes/navigation.php';

?>

<link rel="stylesheet" href="../css/status.css">
<link rel="stylesheet" href="../css/details.css">


<main class="details-page">

    <?php if ($cart_message != "") : ?>

        <p class="success-message">
            <?php echo htmlspecialchars($cart_message); ?>
        </p>

    <?php endif; ?>

    <?php if ($wishlist_message != "") : ?>

        <p class="success-message">
            <?php echo htmlspecialchars($wishlist_message); ?>
        </p>

    <?php endif; ?>

    <div class="details-overview-grid">


        <?php if ($restaurant) : ?>


            <!-- IMAGE -->

            <div class="details-image-wrap">
                <img
                    src="<?php echo htmlspecialchars(restaurant_image_url($restaurant['blind_box_image'] ?? null)); ?>"
                    width="200"
                    height="150"
                    alt="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?> blind box"
                >

                <div class="details-image-overlay">
                    <span class="details-surprise-label">Today’s surprise</span>
                    <span class="details-rating-chip">
                        ★ <?php echo number_format($average_rating, 1); ?>
                        <small>(<?php echo count($reviews); ?>)</small>
                    </span>
                </div>
            </div>


            <!-- DETAILS -->

            <div class="details-info">


                <h1>

                    <span class="details-title">
                        <?php
                        echo htmlspecialchars(
                            $restaurant['restaurant_name']
                        );
                        ?>
                    </span>

                    <span
                        id="restaurantStatusBadge"
                        class="status-badge"
                        data-opening="<?php echo htmlspecialchars($restaurant['restaurant_opening_hours']); ?>"
                        data-closing="<?php echo htmlspecialchars($restaurant['restaurant_closing_hours']); ?>"
                    ><span class="status-dot"></span>Checking...</span>

                </h1>

                <p class="details-category-badge">
                    🍽
                    <?php
                    echo htmlspecialchars(
                        $restaurant['blind_box_food_category']
                    );
                    ?>
                </p>


                <p class="details-description">

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'blind_box_description'
                        ]
                    );
                    ?>

                </p>


                <div class="details-meta">

                    <div class="meta-row">
                        <span class="meta-icon">🕐</span>
                        <div class="meta-text">
                            <span class="meta-label">Operating Hrs</span>
                            <span class="meta-value">
                                <?php
                                echo htmlspecialchars(
                                    strtolower(date('ga', strtotime($restaurant['restaurant_opening_hours'])))
                                    . ' - ' .
                                    strtolower(date('ga', strtotime($restaurant['restaurant_closing_hours'])))
                                );
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="meta-row">
                        <span class="meta-icon">💰</span>
                        <div class="meta-text">
                            <span class="meta-label">Blind Box Price</span>
                            <span class="meta-value meta-price">
                                RM
                                <?php
                                echo number_format(
                                    $restaurant['blind_box_price'],
                                    2
                                );
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="meta-row">
                        <span class="meta-icon">📞</span>
                        <div class="meta-text">
                            <span class="meta-label">Contact Number</span>
                            <span class="meta-value">
                                <?php
                                echo htmlspecialchars(
                                    $restaurant['restaurant_phone_number']
                                );
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="meta-row">
                        <span class="meta-icon">📍</span>
                        <div class="meta-text">
                            <span class="meta-label">Address</span>
                            <span class="meta-value">
                                <?php
                                echo htmlspecialchars(
                                    $restaurant['restaurant_address']
                                );
                                ?>
                            </span>
                        </div>
                    </div>

                </div>


                <!-- ================================================= -->
                <!-- BUTTONS -->
                <!-- ================================================= -->

                <div class="action-buttons">


                    <!-- ADD TO CART -->

                    <form method="POST" id="addToCartForm">

                        <!-- QUANTITY SELECTOR -->

                        <div class="quantity-selector">

                            <span class="quantity-label">Quantity</span>

                            <div class="quantity-controls">

                                <button
                                    type="button"
                                    class="qty-btn qty-minus"
                                    aria-label="Decrease quantity"
                                >
                                    −
                                </button>

                                <input
                                    type="number"
                                    name="quantity"
                                    id="quantityInput"
                                    class="qty-input"
                                    value="1"
                                    min="1"
                                    readonly
                                >

                                <button
                                    type="button"
                                    class="qty-btn qty-plus"
                                    aria-label="Increase quantity"
                                >
                                    +
                                </button>

                            </div>

                        </div>

                        <button
                            type="submit"
                            name="add_to_cart"
                            class="add-cart-btn"
                        >

                            🛒 Add to Cart

                        </button>

                    </form>


                    <!-- ADD TO WISHLIST -->

                    <form method="POST">

                        <button
                            type="submit"
                            name="add_to_wishlist"
                            class="wishlist-btn"
                        >

                            ♡ Add to Wishlist

                        </button>

                    </form>

                    <p id="cartClosedMessage" class="cart-closed-message">
                        This restaurant is currently closed. You can still add it to your wishlist.
                    </p>


                </div>


                <!-- ================================================= -->
                <!-- VIEW BUTTONS -->
                <!-- ================================================= -->

                <div class="view-buttons">


                    <a
                        href="cart.php"
                        class="view-cart-btn"
                    >
                        View Cart
                    </a>


                    <a
                        href="wishlist.php"
                        class="view-wishlist-btn"
                    >
                        View Wishlist
                    </a>


                </div>


            </div>


        <?php else : ?>


            <h1>
                Restaurant not found
            </h1>


        <?php endif; ?>


    <?php if ($restaurant) : ?>

        <section class="restaurant-reviews">
            <div class="reviews-heading">
                <div>
                    <span class="reviews-eyebrow">Customer feedback</span>
                    <h2>Ratings &amp; Reviews</h2>
                </div>

                <div class="rating-summary">
                    <strong><?php echo number_format($average_rating, 1); ?></strong>
                    <div>
                        <span class="summary-stars" aria-hidden="true">
                            <?php for ($star = 1; $star <= 5; $star++) : ?>
                                <span class="<?php echo $star <= round($average_rating) ? 'is-filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </span>
                        <small>
                            <?php echo count($reviews); ?>
                            <?php echo count($reviews) === 1 ? 'verified review' : 'verified reviews'; ?>
                        </small>
                    </div>
                    <span class="screen-reader-text">
                        <?php echo number_format($average_rating, 1); ?> out of 5
                    </span>
                </div>
            </div>

            <?php if (empty($reviews)) : ?>

                <div class="no-reviews">
                    <span aria-hidden="true">☆</span>
                    <h3>No reviews yet</h3>
                    <p>Complete an order to share the first review.</p>
                </div>

            <?php else : ?>

                <div
                    class="review-carousel"
                    id="reviewCarousel"
                    role="region"
                    aria-label="Customer reviews"
                >
                    <div class="review-list" id="reviewTrack">
                        <?php foreach ($reviews as $review_item) : ?>
                            <?php render_review_card($review_item); ?>
                        <?php endforeach; ?>
                        <?php foreach ($reviews as $review_item) : ?>
                            <?php render_review_card($review_item, true); ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>
        </section>

    <?php endif; ?>

    </div>


    <script src="../js/quantity.js"></script>
    <script src="../js/status.js"></script>
    <script src="../js/reviews-carousel.js"></script>

</main>


<?php

$conn->close();

include '../includes/footer.php';

?>
