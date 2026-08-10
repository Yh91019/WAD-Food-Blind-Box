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

            $payment_method = "Cash";
            $order_type = "Pickup";
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
// REMOVE CART ITEM
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['remove_cart'])
    && isset($_SESSION['username'])
) {


    $cart_id =
        (int) $_POST['cart_id'];


    $username =
        $_SESSION['username'];


    $delete_sql = "
        DELETE FROM cart
        WHERE cart_id = ?
        AND username = ?
    ";


    $delete_stmt =
        $conn->prepare($delete_sql);


    $delete_stmt->bind_param(
        "is",
        $cart_id,
        $username
    );


    $delete_stmt->execute();


    $delete_stmt->close();


    header("Location: cart.php");

    exit;

}


$conn->close();


include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/cart.css">

<?php

include '../includes/navigation.php';
?>

<main class="cart-page">


    <h1>
        🛒 My Cart
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


                    <!-- REMOVE -->

                    <form method="POST">

                        <input
                            type="hidden"
                            name="cart_id"
                            value="<?php
                                echo $item['cart_id'];
                            ?>"
                        >


                        <button
                            type="submit"
                            name="remove_cart"
                            class="remove-cart-btn"
                        >

                            Remove

                        </button>

                    </form>


                </div>


            <?php endforeach; ?>


        </div>


        <!-- TOTAL -->

<div class="cart-summary">

    <h2>
        Total: RM <?php echo number_format($total, 2); ?>
    </h2>

    <div class="cart-summary-buttons">

        <a href="../pages/menu.php" class="browse-menu-btn">
            🍔 Add More from Menu
        </a>

        <form method="POST" action="cart.php">
    <button
        type="submit"
        name="place_order"
        class="place-order-btn"
    >
        🛒 Place Order
    </button>
</form>

    </div>

</div>

    <?php endif; ?>


</main>


<?php

include '../includes/footer.php';

?>