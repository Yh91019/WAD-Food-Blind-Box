<?php

session_start();

include '../config/db_connect.php';


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

        $max_quantity =
            (int) $restaurant['blind_box_remaining_quantity'];

        if ($max_quantity > 0 && $quantity_to_add > $max_quantity) {
            $quantity_to_add = $max_quantity;
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


<main class="details-page">

    <div class="details-card">


        <?php if ($restaurant) : ?>


            <!-- IMAGE -->

            <img
                src="../images/BBbox.png"
                width="200"
                height="150"
                alt="Blind Box"
            >


            <!-- DETAILS -->

            <div class="details-info">


                <h1>

                    <?php
                    echo htmlspecialchars(
                        $restaurant['restaurant_name']
                    );
                    ?>

                </h1>


                <p>

                    <strong>
                        Opening Hours:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'restaurant_opening_hours'
                        ]
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Blind Box Description:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'blind_box_description'
                        ]
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Blind Box Price:
                    </strong>

                    RM

                    <?php
                    echo number_format(
                        $restaurant[
                            'blind_box_price'
                        ],
                        2
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Remaining Quantity:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'blind_box_remaining_quantity'
                        ]
                    );
                    ?>

                    Boxes

                </p>


                <p>

                    <strong>
                        Food Category:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'blind_box_food_category'
                        ]
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Contact Number:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'restaurant_phone_number'
                        ]
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Address:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $restaurant[
                            'restaurant_address'
                        ]
                    );
                    ?>

                </p>


                <!-- ================================================= -->
                <!-- MESSAGES -->
                <!-- ================================================= -->


                <?php if ($cart_message != "") : ?>

                    <p class="success-message">

                        <?php
                        echo htmlspecialchars(
                            $cart_message
                        );
                        ?>

                    </p>

                <?php endif; ?>


                <?php if ($wishlist_message != "") : ?>

                    <p class="success-message">

                        <?php
                        echo htmlspecialchars(
                            $wishlist_message
                        );
                        ?>

                    </p>

                <?php endif; ?>


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
                                    max="<?php echo (int) $restaurant['blind_box_remaining_quantity']; ?>"
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


    </div>


    <script src="../js/script.js"></script>
    <script src="../js/quantity.js"></script>

</main>


<?php

$conn->close();

include '../includes/footer.php';

?>