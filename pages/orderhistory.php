<?php
include '../config/db_connect.php';
require_once '../includes/restaurant_image.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$review_success = $_SESSION['review_success'] ?? '';
$review_error = $_SESSION['review_error'] ?? '';
$order_error = $_SESSION['order_error'] ?? '';
unset($_SESSION['review_success'], $_SESSION['review_error'], $_SESSION['order_error']);

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['order_again'])
) {
    if (!isset($_SESSION['username'])) {
        header('Location: ../authentication/login.php');
        exit();
    }

    $history_id = isset($_POST['history_id']) ? (int) $_POST['history_id'] : 0;
    $username = $_SESSION['username'];
    $order_again_stmt = $conn->prepare(
        "SELECT
            history.restaurant_name,
            history.quantity,
            restaurants.restaurant_opening_hours,
            restaurants.restaurant_closing_hours
         FROM history
         INNER JOIN restaurants
            ON history.restaurant_name = restaurants.restaurant_name
         WHERE history.history_id = ? AND history.username = ?"
    );
    $order_again_stmt->bind_param('is', $history_id, $username);
    $order_again_stmt->execute();
    $previous_order = $order_again_stmt->get_result()->fetch_assoc();
    $order_again_stmt->close();

    if (!$previous_order) {
        $_SESSION['order_error'] = 'This order is no longer available to reorder.';
        header('Location: orderhistory.php#order-' . $history_id);
        exit();
    }

    if (!is_restaurant_open(
        $previous_order['restaurant_opening_hours'],
        $previous_order['restaurant_closing_hours']
    )) {
        $_SESSION['order_error'] = 'This restaurant is currently closed. Please try ordering again during its opening hours.';
        header('Location: orderhistory.php#order-' . $history_id);
        exit();
    }

    $restaurant_name = $previous_order['restaurant_name'];
    $quantity = max(1, (int) $previous_order['quantity']);
    $cart_check = $conn->prepare(
        'SELECT cart_id FROM cart WHERE username = ? AND restaurant_name = ?'
    );
    $cart_check->bind_param('ss', $username, $restaurant_name);
    $cart_check->execute();
    $existing_cart_item = $cart_check->get_result()->fetch_assoc();
    $cart_check->close();

    if ($existing_cart_item) {
        $cart_update = $conn->prepare(
            'UPDATE cart SET quantity = quantity + ? WHERE cart_id = ? AND username = ?'
        );
        $cart_update->bind_param('iis', $quantity, $existing_cart_item['cart_id'], $username);
        $cart_update->execute();
        $cart_update->close();
    } else {
        $cart_insert = $conn->prepare(
            'INSERT INTO cart (username, restaurant_name, quantity) VALUES (?, ?, ?)'
        );
        $cart_insert->bind_param('ssi', $username, $restaurant_name, $quantity);
        $cart_insert->execute();
        $cart_insert->close();
    }

    $_SESSION['cart_notice'] = $restaurant_name . ' was added to your cart again.';
    $conn->close();
    header('Location: cart.php');
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit_review'])
) {
    if (!isset($_SESSION['username'])) {
        header('Location: ../authentication/login.php');
        exit();
    }

    $history_id = isset($_POST['history_id']) ? (int) $_POST['history_id'] : 0;
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $review = trim($_POST['review'] ?? '');

    if ($history_id < 1 || $rating < 1 || $rating > 5 || $review === '') {
        $_SESSION['review_error'] = 'Please select a rating and enter a review.';
    } elseif (strlen($review) > 1000) {
        $_SESSION['review_error'] = 'Your review must be 1,000 characters or fewer.';
    } else {
        $order_check = $conn->prepare(
            "SELECT restaurant_name, status
             FROM history
             WHERE history_id = ? AND username = ?"
        );
        $order_check->bind_param('is', $history_id, $_SESSION['username']);
        $order_check->execute();
        $owned_order = $order_check->get_result()->fetch_assoc();
        $order_check->close();

        if (!$owned_order) {
            $_SESSION['review_error'] = 'That order could not be found.';
        } elseif ($owned_order['status'] !== 'Completed') {
            $_SESSION['review_error'] = 'You can review an order after it is completed.';
        } else {
            $save_review = $conn->prepare(
                "INSERT INTO reviews
                    (history_id, username, restaurant_name, rating, review)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    rating = VALUES(rating),
                    review = VALUES(review),
                    updated_at = CURRENT_TIMESTAMP"
            );
            $save_review->bind_param(
                'issis',
                $history_id,
                $_SESSION['username'],
                $owned_order['restaurant_name'],
                $rating,
                $review
            );
            $save_review->execute();
            $save_review->close();

            $_SESSION['review_success'] = 'Your rating and review have been saved.';
        }
    }

    header('Location: orderhistory.php#order-' . $history_id);
    exit();
}

$orders = [];

if (isset($_SESSION['username'])) {

    $sql = "SELECT
                history.history_id,
                history.restaurant_name,
                history.blind_box_price,
                history.quantity,
                history.payment_method,
                history.order_type,
                history.status,
                history.order_date,
                reviews.review_id,
                reviews.rating,
                reviews.review
            FROM history
            LEFT JOIN reviews ON history.history_id = reviews.history_id
            WHERE history.username = ?
            ORDER BY history.order_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['total'] = $row['blind_box_price'] * $row['quantity'];
        $orders[] = $row;
    }

    $stmt->close();
}

$conn->close();

include '../includes/header.php';
include '../includes/navigation.php';
?>

<link rel="stylesheet" href="../css/orderhistory.css">

<main class="orderhistory-page">

    <h1>Order History</h1>

    <?php if ($review_success !== '') : ?>
        <p class="review-message review-success">
            <?php echo htmlspecialchars($review_success); ?>
        </p>
    <?php endif; ?>

    <?php if ($review_error !== '') : ?>
        <p class="review-message review-error">
            <?php echo htmlspecialchars($review_error); ?>
        </p>
    <?php endif; ?>

    <?php if ($order_error !== '') : ?>
        <p class="review-message review-error">
            <?php echo htmlspecialchars($order_error); ?>
        </p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['username'])) : ?>

        <div class="history-empty">

            <div class="history-empty-icon">🔒</div>

            <h2>Please log in</h2>

            <p>You need to log in before viewing your order history.</p>

            <a href="../authentication/login.php" class="browse-menu-btn">Login</a>

        </div>

    <?php elseif (empty($orders)) : ?>

        <div class="history-empty">

            <div class="history-empty-icon">📦</div>

            <h2>You haven't placed any orders yet</h2>

            <p>Browse the menu and grab a blind box to get started.</p>

            <a href="../pages/menu.php" class="browse-menu-btn">Browse Menu</a>

        </div>

    <?php else : ?>

        <p class="history-count">
            <?php echo count($orders); ?> order<?php echo count($orders) === 1 ? '' : 's'; ?> found
        </p>

        <div class="history-container">

            <?php $order_index = 0; foreach ($orders as $order) : $order_index++; ?>

                <div
                    class="history-card"
                    id="order-<?php echo (int) $order['history_id']; ?>"
                    style="--i: <?php echo $order_index; ?>"
                >

                    <div class="top">
                        <h2><?php echo htmlspecialchars($order['restaurant_name']); ?></h2>
                        <span class="status <?php echo strtolower($order['status']); ?>">
                            <?php
                            $status_icons = [
                                'completed' => '✓',
                                'preparing' => '⏳',
                                'cancelled' => '✕',
                            ];
                            echo $status_icons[strtolower($order['status'])] ?? '';
                            ?>
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>

                    <hr>

                    <div class="history-details">
                        <p><strong>Order #</strong><span><?php echo (int) $order['history_id']; ?></span></p>
                        <p><strong>Quantity</strong><span><?php echo (int) $order['quantity']; ?> Blind Box(es)</span></p>
                        <p><strong>Order Type</strong><span><?php echo htmlspecialchars($order['order_type']); ?></span></p>
                        <p><strong>Order Date</strong><span><?php echo date('d F Y, g:i A', strtotime($order['order_date'])); ?></span></p>
                        <p><strong>Payment Method</strong><span><?php echo htmlspecialchars($order['payment_method']); ?></span></p>
                    </div>

                    <div class="total-row">
                        <span>Total Paid</span>
                        <span class="total-amount">RM <?php echo number_format($order['total'], 2); ?></span>
                    </div>

                    <div class="history-actions">
                        <form method="POST" action="orderhistory.php" class="order-again-form">
                            <input type="hidden" name="history_id" value="<?php echo (int) $order['history_id']; ?>">
                            <button type="submit" name="order_again" class="order-again-btn">
                                ↻ Order Again
                            </button>
                        </form>

                    <?php if ($order['status'] === 'Completed') : ?>

                        <details class="review-panel">
                            <summary>
                                <?php echo $order['review_id'] ? 'Edit Review' : 'Leave Review'; ?>
                            </summary>

                        <form method="POST" action="orderhistory.php" class="review-form">
                            <input
                                type="hidden"
                                name="history_id"
                                value="<?php echo (int) $order['history_id']; ?>"
                            >

                            <h3><?php echo $order['review_id'] ? 'Update Your Review' : 'Rate This Restaurant'; ?></h3>

                            <fieldset class="rating-fieldset">
                                <legend>Rating</legend>
                                <div class="rating-picker" aria-label="Choose a rating out of 5">
                                    <?php for ($score = 5; $score >= 1; $score--) : ?>
                                        <input
                                            type="radio"
                                            name="rating"
                                            id="rating-<?php echo (int) $order['history_id']; ?>-<?php echo $score; ?>"
                                            value="<?php echo $score; ?>"
                                            <?php echo (int) $order['rating'] === $score ? 'checked' : ''; ?>
                                            required
                                        >
                                        <label
                                            for="rating-<?php echo (int) $order['history_id']; ?>-<?php echo $score; ?>"
                                            title="<?php echo $score; ?> out of 5"
                                        >★</label>
                                    <?php endfor; ?>
                                </div>
                            </fieldset>

                            <label for="review-<?php echo (int) $order['history_id']; ?>">Review</label>
                            <textarea
                                name="review"
                                id="review-<?php echo (int) $order['history_id']; ?>"
                                maxlength="1000"
                                placeholder="Share your experience..."
                                required
                            ><?php echo htmlspecialchars($order['review'] ?? ''); ?></textarea>

                            <small class="review-hint">Maximum 1,000 characters</small>

                            <button type="submit" name="submit_review">
                                <?php echo $order['review_id'] ? 'Update Review' : 'Submit Review'; ?>
                            </button>
                        </form>

                        </details>

                    <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

   


</main>

 <script src="../js/script.js"></script>

<?php include '../includes/footer.php'; ?>
