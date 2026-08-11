<?php

session_start();

include '../config/db_connect.php';
/* ============================================================
   PLACE ORDER
   ============================================================ */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['place_order'])
) {

    if (!isset($_SESSION['username'])) {

        header("Location: ../authentication/login.php");
        exit();

    }

    $username = $_SESSION['username'];

    /* Payment method chosen by the user in the modal (defaults to Cash) */
    $payment_method =
        (isset($_POST['payment_method']) && $_POST['payment_method'] === 'Card')
        ? 'Card'
        : 'Cash';

    /* Get all cart items */
    $order_sql = "
        SELECT
            cart.restaurant_name,
            cart.quantity,
            restaurants.blind_box_price
        FROM cart
        INNER JOIN restaurants
            ON cart.restaurant_name = restaurants.restaurant_name
        WHERE cart.username = ?
    ";

    $order_stmt = $conn->prepare($order_sql);

    if (!$order_stmt) {
        die("Error preparing order: " . $conn->error);
    }

    $order_stmt->bind_param("s", $username);

    $order_stmt->execute();

    $order_result = $order_stmt->get_result();


    /* Check cart is not empty */

    if ($order_result->num_rows > 0) {

        /* Insert every cart item into history */

        while ($item = $order_result->fetch_assoc()) {

            $restaurant_name = $item['restaurant_name'];
            $price = $item['blind_box_price'];
            $quantity = $item['quantity'];

            $order_type =
    (isset($_POST['order_type'])
    && $_POST['order_type'] === 'Delivery')
    ? 'Delivery'
    : 'Pickup';
            $status = "Completed";


            $history_sql = "
                INSERT INTO history
                (
                    username,
                    restaurant_name,
                    blind_box_price,
                    quantity,
                    payment_method,
                    order_type,
                    status,
                    order_date
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ";

            $history_stmt = $conn->prepare($history_sql);

            if (!$history_stmt) {
                die("Error preparing history: " . $conn->error);
            }

            $history_stmt->bind_param(
                "ssdisss",
                $username,
                $restaurant_name,
                $price,
                $quantity,
                $payment_method,
                $order_type,
                $status
            );

            $history_stmt->execute();

            $history_stmt->close();
        }


        $order_stmt->close();


        /* Remove items from cart */

        $delete_sql = "
            DELETE FROM cart
            WHERE username = ?
        ";

        $delete_stmt = $conn->prepare($delete_sql);

        $delete_stmt->bind_param("s", $username);

        $delete_stmt->execute();

        $delete_stmt->close();


        /* Go to success page */

        $conn->close();

        header("Location: order_complete.php");
        exit();

    }

    else {

        $order_stmt->close();

        header("Location: cart.php");
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
            restaurants.blind_box_remaining_quantity,
            restaurants.blind_box_food_category
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

            // ====================================================
            // CHECK MAXIMUM AVAILABLE QUANTITY
            // ====================================================

            $stock_sql = "
                SELECT
                    restaurants.blind_box_remaining_quantity
                FROM cart
                INNER JOIN restaurants
                    ON cart.restaurant_name =
                       restaurants.restaurant_name
                WHERE cart.cart_id = ?
                AND cart.username = ?
            ";

            $stock_stmt = $conn->prepare($stock_sql);

            $stock_stmt->bind_param(
                "is",
                $cart_id,
                $username
            );

            $stock_stmt->execute();

            $stock_result = $stock_stmt->get_result();

            if ($stock_result->num_rows === 1) {

                $stock_row = $stock_result->fetch_assoc();

                $max_quantity =
                    (int) $stock_row['blind_box_remaining_quantity'];

                if ($new_quantity > $max_quantity) {

                    $new_quantity = $max_quantity;

                }


                // ====================================================
                // UPDATE QUANTITY
                // ====================================================

                $update_sql = "
                    UPDATE cart
                    SET quantity = ?
                    WHERE cart_id = ?
                    AND username = ?
                ";

                $update_stmt =
                    $conn->prepare($update_sql);

                $update_stmt->bind_param(
                    "iis",
                    $new_quantity,
                    $cart_id,
                    $username
                );

                $update_stmt->execute();

                $update_stmt->close();

            }

            $stock_stmt->close();

        }

    }

    $check_stmt->close();

    header("Location: cart.php");
    exit();
}

include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/cart.css">
<style>
.cart-quantity-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 18px;
}

.quantity-form {
    margin: 0;
}

.cart-quantity-btn {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 50%;
    background: #ab794b;
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    line-height: 38px;
    padding: 0;
    cursor: pointer;
    transition: 0.2s ease;
}

.cart-quantity-btn:hover:not(:disabled) {
    background: #8f623c;
    transform: scale(1.08);
}

.cart-quantity-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
}

.cart-quantity-number {
    min-width: 35px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.order-type-section {
    text-align: center;
    margin: 25px 0;
}

.order-type-section h3 {
    color: #ab794b;
    margin-bottom: 15px;
}

.order-type-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.order-type-btn {
    min-width: 120px;
    padding: 10px 18px;
    border: 1.5px solid #ab794b;
    border-radius: 8px;
    background: #fff;
    color: #ab794b;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease;
}

.order-type-btn:hover {
    background: #f3e7dc;
}

.order-type-btn.selected {
    background: #ab794b;
    color: #fff;
}

.order-type-message {
    margin-top: 10px;
    color: #777;
    font-size: 14px;
}

.order-type-message.selected-message {
    color: #2e7d32;
    font-weight: bold;
}

.order-type-message.error-message {
    color: #d32f2f;
    font-weight: bold;
}

.place-order-btn:disabled {
    background: #aaa;
    cursor: not-allowed;
    opacity: 0.65;
}
</style>

<?php

include '../includes/navigation.php';
?>

<main class="cart-page">


    <h1>
        My Cart
    </h1>


    <?php if (!isset($_SESSION['username'])) : ?>


        <div class="cart-message">

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


        <div class="cart-container">


            <?php foreach ($cart_items as $item) : ?>


                <div class="cart-item">


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


                    <p>

                        <strong>
                            Food Category:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $item[
                                'blind_box_food_category'
                            ]
                        );
                        ?>

                    </p>


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

                    </p>


                    <p>

                        <strong>
                            Quantity:
                        </strong>

                        <?php
                        echo $item['quantity'];
                        ?>

                    </p>


                    <p>

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


                    <!-- QUANTITY CONTROLS -->

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
            <?php
            if (
                $item['quantity']
                >=
                $item['blind_box_remaining_quantity']
            ) {
                echo 'disabled';
            }
            ?>
        >
            +
        </button>

    </form>

</div>


                </div>


            <?php endforeach; ?>


        </div>


       <!-- ============================================================
     TOTAL + ORDER TYPE
     ============================================================ -->

<div class="cart-summary">

    <h2>
        Total: RM <?php echo number_format($total, 2); ?>
    </h2>


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


<script>

function selectOrderType(type) {

    const pickupBtn = document.getElementById("pickupBtn");
    const deliveryBtn = document.getElementById("deliveryBtn");
    const orderTypeInput = document.getElementById("orderTypeInput");
    const message = document.getElementById("orderTypeMessage");
    const placeOrderBtn = document.getElementById("openPaymentModalBtn");

    // Remove selected style from both buttons
    pickupBtn.classList.remove("selected");
    deliveryBtn.classList.remove("selected");

    // Select the chosen order type
    if (type === "Pickup") {
        pickupBtn.classList.add("selected");
    } else {
        deliveryBtn.classList.add("selected");
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