<?php

session_start();

include '../config/db_connect.php';
require_once '../includes/vouchers.php';

$cart_notice = $_SESSION['cart_notice'] ?? '';
unset($_SESSION['cart_notice']);
/* ============================================================
   PLACE ORDER
   ============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($_SESSION['username'])) {
        header('Location: ../authentication/login.php');
        exit();
    }

    $username = $_SESSION['username'];
    $allowed_payment_methods = ['Cash', 'Card', 'TNG'];
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $payment_method = in_array($payment_method, $allowed_payment_methods, true)
        ? $payment_method : 'Cash';
    $order_type = ($_POST['order_type'] ?? '') === 'Delivery' ? 'Delivery' : 'Pickup';
    $delivery_fee = $order_type === 'Delivery' ? 5.00 : 0.00;

    if ($payment_method === 'Card') {
        $payment_check = $conn->prepare(
            'SELECT payment_id FROM paymentmethod WHERE username = ? LIMIT 1'
        );
        $payment_check->bind_param('s', $username);
        $payment_check->execute();
        $has_saved_card = $payment_check->get_result()->num_rows === 1;
        $payment_check->close();
        if (!$has_saved_card) {
            $_SESSION['profile_error'] = 'Please save a payment method before placing an order by card.';
            header('Location: ../authentication/profile.php');
            exit();
        }
    }

    $order_stmt = $conn->prepare(
        "SELECT cart.restaurant_name, cart.quantity, restaurants.blind_box_price
         FROM cart INNER JOIN restaurants
           ON cart.restaurant_name = restaurants.restaurant_name
         WHERE cart.username = ?"
    );
    $order_stmt->bind_param('s', $username);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order_items = [];
    $order_subtotal = 0.0;
    while ($item = $order_result->fetch_assoc()) {
        $order_items[] = $item;
        $order_subtotal += (float) $item['blind_box_price'] * (int) $item['quantity'];
    }
    $order_stmt->close();

    if (empty($order_items)) {
        header('Location: cart.php');
        exit();
    }

    $voucher_code = strtoupper(trim($_POST['voucher_code'] ?? ''));
    $promotion = null;
    $discount = 0.0;
    if ($voucher_code !== '') {
        $promotion = unused_user_promotion($conn, $username, $voucher_code);
        if (!$promotion) {
            $_SESSION['cart_notice'] = 'This voucher is invalid, expired, or has already been used.';
            header('Location: cart.php');
            exit();
        }
        $discount = voucher_discount_amount($promotion, $order_subtotal);
        if ($discount <= 0) {
            $_SESSION['cart_notice'] = 'Your cart does not meet this voucher’s minimum spend.';
            header('Location: cart.php');
            exit();
        }
    }

    $conn->begin_transaction();
    try {
        $history_stmt = $conn->prepare(
            "INSERT INTO history
                (username, restaurant_name, blind_box_price, quantity,
                 payment_method, order_type, voucher_code, discount_amount,
                 delivery_fee, final_total, status, order_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $allocated_discount = 0.0;
        $last_index = count($order_items) - 1;

        foreach ($order_items as $index => $item) {
            $restaurant_name = $item['restaurant_name'];
            $price = (float) $item['blind_box_price'];
            $quantity = (int) $item['quantity'];
            $line_subtotal = $price * $quantity;
            $line_discount = $index === $last_index
                ? round($discount - $allocated_discount, 2)
                : round($discount * ($line_subtotal / $order_subtotal), 2);
            $allocated_discount += $line_discount;
            $line_delivery_fee = $index === 0 ? $delivery_fee : 0.0;
            $line_total = round($line_subtotal - $line_discount + $line_delivery_fee, 2);
            $status = 'Completed';
            $history_voucher_code = $promotion ? $promotion['code'] : null;

            $history_stmt->bind_param(
                'ssdisssddds',
                $username, $restaurant_name, $price, $quantity,
                $payment_method, $order_type, $history_voucher_code,
                $line_discount, $line_delivery_fee, $line_total, $status
            );
            if (!$history_stmt->execute()) {
                throw new RuntimeException($history_stmt->error);
            }
        }
        $history_stmt->close();

        if ($promotion) {
            $used_stmt = $conn->prepare(
                'UPDATE user_vouchers SET used_at = NOW() WHERE user_voucher_id = ? AND used_at IS NULL'
            );
            $used_stmt->bind_param('i', $promotion['user_voucher_id']);
            $used_stmt->execute();
            $used_stmt->close();
        }

        $delete_stmt = $conn->prepare('DELETE FROM cart WHERE username = ?');
        $delete_stmt->bind_param('s', $username);
        $delete_stmt->execute();
        $delete_stmt->close();
        $conn->commit();
        unset($_SESSION['applied_voucher_code']);
        $_SESSION['order_total'] = round($order_subtotal - $discount + $delivery_fee, 2);
        header('Location: order_complete.php');
        exit();
    } catch (Throwable $error) {
        $conn->rollback();
        $_SESSION['cart_notice'] = 'The order could not be completed. Please try again.';
        header('Location: cart.php');
        exit();
    }
}

// ============================================================
// CART ITEMS
// ============================================================

$cart_items = [];

$total = 0;


// ============================================================
// CHECK LOGIN
// ============================================================

if (isset($_SESSION['username'])) {


    $username =
        $_SESSION['username'];


    // ========================================================
    // GET CART + RESTAURANT INFORMATION
    // ========================================================

    $sql = "
        SELECT
            cart.cart_id,
            cart.restaurant_name,
            cart.quantity,
            restaurants.blind_box_price,
            restaurants.blind_box_description,
            restaurants.blind_box_food_category,
            restaurants.blind_box_image
        FROM cart

        INNER JOIN restaurants
            ON cart.restaurant_name =
               restaurants.restaurant_name

        WHERE cart.username = ?
    ";


    $stmt =
        $conn->prepare($sql);


    $stmt->bind_param(
        "s",
        $username
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    // ========================================================
    // STORE ITEMS
    // ========================================================

    while (
        $row =
        $result->fetch_assoc()
    ) {


        $cart_items[] =
            $row;


        $total +=
            $row['blind_box_price']
            *
            $row['quantity'];

    }


    $stmt->close();

}


$available_vouchers = [];
$applied_promotion = null;
$voucher_discount = 0.0;

if (!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_voucher'])) {
        unset($_SESSION['applied_voucher_code']);
        $_SESSION['cart_notice'] = 'Voucher removed from your cart.';
        header('Location: cart.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_voucher'])) {
        $voucher_code = trim($_POST['voucher_code'] ?? '');
        if ($voucher_code === '') {
            $voucher_code = trim($_POST['claimed_voucher'] ?? '');
        }
        $voucher_code = strtoupper($voucher_code);
        if ($voucher_code === '') {
            $_SESSION['cart_notice'] = 'Choose a voucher or enter a voucher code.';
        } else {
            $promotion = active_promotion_by_code($conn, $voucher_code);
            if (!$promotion) {
                $_SESSION['cart_notice'] = 'Voucher code not found or no longer active.';
            } else {
                claim_promotion($conn, $username, (int) $promotion['promotion_id']);
                $user_promotion = unused_user_promotion($conn, $username, $promotion['code']);
                $discount = $user_promotion
                    ? voucher_discount_amount($user_promotion, $total) : 0.0;

                if (!$user_promotion) {
                    $_SESSION['cart_notice'] = 'This voucher has already been used.';
                } elseif ($discount <= 0) {
                    $_SESSION['cart_notice'] = 'Spend at least RM'
                        . number_format((float) $promotion['minimum_spend'], 2)
                        . ' to use this voucher.';
                } else {
                    $_SESSION['applied_voucher_code'] = $promotion['code'];
                    $_SESSION['cart_notice'] = $promotion['code'] . ' applied successfully.';
                }
            }
        }
        header('Location: cart.php');
        exit();
    }

    $voucher_stmt = $conn->prepare(
        "SELECT promotions.*
         FROM user_vouchers
         INNER JOIN promotions ON promotions.promotion_id = user_vouchers.promotion_id
         WHERE user_vouchers.username = ? AND user_vouchers.used_at IS NULL
           AND promotions.is_active = 1
           AND NOW() BETWEEN promotions.starts_at AND promotions.ends_at
         ORDER BY promotions.title"
    );
    $voucher_stmt->bind_param('s', $username);
    $voucher_stmt->execute();
    $voucher_result = $voucher_stmt->get_result();
    while ($voucher = $voucher_result->fetch_assoc()) {
        $available_vouchers[] = $voucher;
    }
    $voucher_stmt->close();

    $applied_code = $_SESSION['applied_voucher_code'] ?? '';
    if ($applied_code !== '') {
        $applied_promotion = unused_user_promotion($conn, $username, $applied_code);
        $voucher_discount = $applied_promotion
            ? voucher_discount_amount($applied_promotion, $total) : 0.0;
        if (!$applied_promotion || $voucher_discount <= 0) {
            unset($_SESSION['applied_voucher_code']);
            $applied_promotion = null;
            $voucher_discount = 0.0;
        }
    }
}

$cart_total_after_discount = max(0, $total - $voucher_discount);

// ============================================================
// UPDATE CART QUANTITY
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['update_quantity'])
    && isset($_SESSION['username'])
) {

    $cart_id = (int) $_POST['cart_id'];
    $change = (int) $_POST['change'];
    $username = $_SESSION['username'];

    // Get current quantity
    $check_sql = "
        SELECT quantity
        FROM cart
        WHERE cart_id = ?
        AND username = ?
    ";

    $check_stmt = $conn->prepare($check_sql);

    $check_stmt->bind_param(
        "is",
        $cart_id,
        $username
    );

    $check_stmt->execute();

    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 1) {

        $cart_row = $check_result->fetch_assoc();

        $current_quantity = (int) $cart_row['quantity'];

        $new_quantity = $current_quantity + $change;


        // ====================================================
        // IF QUANTITY BECOMES 0, REMOVE ITEM
        // ====================================================

        if ($new_quantity <= 0) {

            $delete_sql = "
                DELETE FROM cart
                WHERE cart_id = ?
                AND username = ?
            ";

            $delete_stmt = $conn->prepare($delete_sql);

            $delete_stmt->bind_param(
                "is",
                $cart_id,
                $username
            );

            $delete_stmt->execute();

            $delete_stmt->close();

        } else {

            $update_sql = "
                UPDATE cart
                SET quantity = ?
                WHERE cart_id = ?
                AND username = ?
            ";

            $update_stmt = $conn->prepare($update_sql);

            $update_stmt->bind_param(
                "iis",
                $new_quantity,
                $cart_id,
                $username
            );

            $update_stmt->execute();

            $update_stmt->close();

        }

    }

    $check_stmt->close();

    header("Location: cart.php");
    exit();
}

// ============================================================
// REMOVE ITEM FROM CART
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['remove_item'])
    && isset($_SESSION['username'])
) {

    $cart_id = (int) $_POST['cart_id'];
    $username = $_SESSION['username'];

    // Remove the entire order line, regardless of its quantity.

    $delete_sql = "
        DELETE FROM cart
        WHERE cart_id = ?
        AND username = ?
    ";

    $delete_stmt = $conn->prepare($delete_sql);

    $delete_stmt->bind_param(
        "is",
        $cart_id,
        $username
    );

    $delete_stmt->execute();

    $delete_stmt->close();

    header("Location: cart.php");
    exit();
}

include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/cart.css">

<?php

include '../includes/navigation.php';
?>

<main class="cart-page">


    <h1>
        My Cart
    </h1>

    <?php if ($cart_notice !== '') : ?>
        <p class="cart-notice"><?php echo htmlspecialchars($cart_notice); ?></p>
    <?php endif; ?>


    <?php if (!isset($_SESSION['username'])) : ?>


        <div class="cart-message">

            <div class="cart-message-icon">🔒</div>

            <h2>
                Please log in
            </h2>

            <p>
                You need to log in before viewing your cart.
            </p>

            <a
                href="../authentication/login.php"
                class="browse-menu-btn"
            >
                Login
            </a>

        </div>


    <?php elseif (empty($cart_items)) : ?>


        <div class="cart-message">

            <div class="cart-message-icon">🛒</div>

            <h2>
                Your cart is empty
            </h2>

            <p>
                Add some blind boxes from the menu!
            </p>

            <a
                href="menu.php"
                class="browse-menu-btn"
            >
                Browse Menu
            </a>

        </div>


    <?php else : ?>


        <div class="cart-layout">

        <div class="cart-container">


            <?php $item_index = 0; foreach ($cart_items as $item) : $item_index++; ?>


                <div class="cart-item" style="--i: <?php echo $item_index; ?>">


                    <img
                        class="cart-item-image"
                        src="<?php echo htmlspecialchars(restaurant_image_url($item['blind_box_image'] ?? null)); ?>"
                        alt="<?php echo htmlspecialchars($item['restaurant_name']); ?> blind box"
                    >


                    <div class="cart-item-body">


                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $item['restaurant_name']
                        );
                        ?>

                    </h3>


                    <p class="cart-food-name">

                        <?php
                        echo htmlspecialchars(
                            $item['blind_box_description']
                        );
                        ?>

                    </p>


                    <span class="cart-category-badge">
                        🍱
                        <?php
                        echo htmlspecialchars(
                            $item[
                                'blind_box_food_category'
                            ]
                        );
                        ?>
                    </span>


                    <p>

                        <strong>
                            Price:
                        </strong>

                        RM

                        <?php
                        echo number_format(
                            $item['blind_box_price'],
                            2
                        );
                        ?>

                        each

                    </p>


                    <p class="cart-item-subtotal">

                        <strong>
                            Subtotal:
                        </strong>

                        RM

                        <?php
                        echo number_format(
                            $item['blind_box_price']
                            *
                            $item['quantity'],
                            2
                        );
                        ?>

                    </p>


                    <!-- QUANTITY + REMOVE -->

<div class="cart-item-footer">

<div class="cart-quantity-controls">

    <!-- MINUS -->

    <form method="POST" class="quantity-form">

        <input
            type="hidden"
            name="cart_id"
            value="<?php echo $item['cart_id']; ?>"
        >

        <input
            type="hidden"
            name="change"
            value="-1"
        >

        <input
            type="hidden"
            name="update_quantity"
            value="1"
        >

        <button
            type="submit"
            class="cart-quantity-btn"
        >
            −
        </button>

    </form>


    <!-- CURRENT QUANTITY -->

    <span class="cart-quantity-number">
        <?php echo $item['quantity']; ?>
    </span>


    <!-- PLUS -->

    <form method="POST" class="quantity-form">

        <input
            type="hidden"
            name="cart_id"
            value="<?php echo $item['cart_id']; ?>"
        >

        <input
            type="hidden"
            name="change"
            value="1"
        >

        <input
            type="hidden"
            name="update_quantity"
            value="1"
        >

        <button
            type="submit"
            class="cart-quantity-btn"
        >
            +
        </button>

    </form>

</div>


                    <!-- REMOVE ITEM -->

                    <form method="POST" class="remove-item-form">

                        <input
                            type="hidden"
                            name="cart_id"
                            value="<?php echo $item['cart_id']; ?>"
                        >

                        <input
                            type="hidden"
                            name="remove_item"
                            value="1"
                        >

                        <button
                            type="submit"
                            class="remove-cart-btn"
                        >
                            🗑 Remove
                        </button>

                    </form>

</div>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


       <!-- ============================================================
     TOTAL + ORDER TYPE
     ============================================================ -->

<div class="cart-summary">

    <h2 class="cart-summary-total">
        <span class="cart-summary-total-label">Total</span>
        <span class="cart-summary-total-amount" id="cartGrandTotal">RM <?php echo number_format($cart_total_after_discount, 2); ?></span>
    </h2>

    <div class="cart-price-breakdown">
        <p><span>Subtotal</span><strong>RM <?php echo number_format($total, 2); ?></strong></p>
        <p><span>Voucher discount</span><strong class="cart-discount">− RM <?php echo number_format($voucher_discount, 2); ?></strong></p>
        <p><span>Delivery fee</span><strong id="deliveryFeeAmount">RM 0.00</strong></p>
    </div>

    <div class="voucher-section">
        <h3>Voucher</h3>
        <?php if ($applied_promotion) : ?>
            <div class="applied-voucher">
                <span><strong><?php echo htmlspecialchars($applied_promotion['code']); ?></strong> applied</span>
                <span>− RM <?php echo number_format($voucher_discount, 2); ?></span>
            </div>
            <form method="POST" action="cart.php">
                <button type="submit" name="remove_voucher" class="remove-voucher-btn">Remove voucher</button>
            </form>
        <?php else : ?>
            <form method="POST" action="cart.php" class="voucher-form">
                <?php if (!empty($available_vouchers)) : ?>
                    <select name="claimed_voucher" id="claimedVoucherSelect" aria-label="Claimed vouchers">
                        <option value="">Choose claimed voucher</option>
                        <?php foreach ($available_vouchers as $voucher) : ?>
                            <option value="<?php echo htmlspecialchars($voucher['code']); ?>">
                                <?php echo htmlspecialchars($voucher['code'] . ' — ' . $voucher['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <div class="voucher-code-row">
                    <input type="text" name="voucher_code" id="voucherCodeInput" maxlength="40" placeholder="Enter voucher code">
                    <button type="submit" name="apply_voucher">Apply</button>
                </div>
            </form>
        <?php endif; ?>
    </div>


    <!-- ORDER TYPE -->

    <div class="order-type-section">

        <h3>
            Choose Order Type
        </h3>

        <div class="order-type-buttons">

            <button
                type="button"
                class="order-type-btn"
                id="pickupBtn"
                onclick="selectOrderType('Pickup')"
            >
                🛍️ Pickup
            </button>


            <button
                type="button"
                class="order-type-btn"
                id="deliveryBtn"
                onclick="selectOrderType('Delivery')"
            >
                🚚 Delivery
            </button>

        </div>


        <p
            id="orderTypeMessage"
            class="order-type-message"
        >
            Please select Pickup or Delivery.
        </p>

    </div>


    <!-- BUTTONS -->

    <div class="cart-summary-buttons">

        <a
            href="../pages/menu.php"
            class="browse-menu-btn"
        >
            🍔 Add More from Menu
        </a>


        <form
            method="POST"
            action="cart.php"
            id="placeOrderForm"
        >

            <input
                type="hidden"
                name="place_order"
                value="1"
            >

            <input
                type="hidden"
                name="payment_method"
                id="paymentMethodInput"
                value=""
            >

            <input
                type="hidden"
                name="order_type"
                id="orderTypeInput"
                value=""
            >

            <input
                type="hidden"
                name="voucher_code"
                value="<?php echo $applied_promotion ? htmlspecialchars($applied_promotion['code']) : ''; ?>"
            >


            <button
    type="button"
    id="openPaymentModalBtn"
    class="place-order-btn"
    onclick="checkOrderType()"
    disabled
>
    🛒 Place Order
</button>

        </form>

    </div>

</div>

</div>


<script>

function selectOrderType(type) {

    const pickupBtn = document.getElementById("pickupBtn");
    const deliveryBtn = document.getElementById("deliveryBtn");
    const orderTypeInput = document.getElementById("orderTypeInput");
    const message = document.getElementById("orderTypeMessage");
    const placeOrderBtn = document.getElementById("openPaymentModalBtn");
    const deliveryFeeAmount = document.getElementById("deliveryFeeAmount");
    const grandTotal = document.getElementById("cartGrandTotal");
    const discountedSubtotal = <?php echo json_encode((float) $cart_total_after_discount); ?>;

    // Remove selected style from both buttons
    pickupBtn.classList.remove("selected");
    deliveryBtn.classList.remove("selected");

    // Select the chosen order type
    if (type === "Pickup") {
        pickupBtn.classList.add("selected");
        deliveryFeeAmount.textContent = "RM 0.00";
        grandTotal.textContent = "RM " + discountedSubtotal.toFixed(2);
    } else {
        deliveryBtn.classList.add("selected");
        deliveryFeeAmount.textContent = "RM 5.00";
        grandTotal.textContent = "RM " + (discountedSubtotal + 5).toFixed(2);
    }

    // Save the selected order type
    orderTypeInput.value = type;

    // Update message
    message.textContent = "Selected: " + type;
    message.classList.remove("error-message");
    message.classList.add("selected-message");

    // Enable Place Order only after an order type is selected
    placeOrderBtn.disabled = false;
}


function checkOrderType() {

    const orderTypeInput = document.getElementById("orderTypeInput");
    const message = document.getElementById("orderTypeMessage");

    // Do not allow checkout without Pickup or Delivery
    if (orderTypeInput.value === "") {

        message.textContent = "Please select Pickup or Delivery before placing your order.";
        message.classList.remove("selected-message");
        message.classList.add("error-message");

        return false;
    }

    // Let payment.js open the payment modal
    return true;
}

</script>

<script>
const claimedVoucherSelect = document.getElementById('claimedVoucherSelect');
const voucherCodeInput = document.getElementById('voucherCodeInput');
if (claimedVoucherSelect && voucherCodeInput) {
    claimedVoucherSelect.addEventListener('change', function () {
        voucherCodeInput.value = this.value;
    });
}
</script>

<!-- PAYMENT METHOD MODAL -->
<div id="paymentModalOverlay" class="payment-modal-overlay">

    <div class="payment-modal">

        <h2>Select Payment Method</h2>

        <p>Choose how you'd like to pay for this order.</p>

        <div class="payment-method-buttons">

            <button type="button" class="payment-method-btn" data-method="Cash">
                💵 Cash
            </button>

            <button type="button" class="payment-method-btn" data-method="Card">
                💳 Card
            </button>

            <button
                type="button"
                class="payment-method-btn"
                data-method="TNG"
                data-redirect="https://consumer.touchngo.com.my"
            >
                📱 Pay with TNG
            </button>

        </div>

        <button type="button" id="cancelPaymentBtn" class="cancel-payment-btn">
            Cancel
        </button>

    </div>

</div>

<script src="../js/payment.js"></script>

    <?php endif; ?>


</main>


<?php include('../includes/footer.php'); ?>
