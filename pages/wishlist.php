<?php

session_start();

include '../config/db_connect.php';


// ============================================================
// CHECK LOGIN
// ============================================================

if (!isset($_SESSION['username'])) {

    include '../includes/header.php';

    include '../includes/navigation.php';
    

    ?>

    <main class="wishlist-page">

        <div class="empty-wishlist">

            <h2>
                Please log in
            </h2>

            <p>
                You need to log in before viewing your wishlist.
            </p>

            <a
                href="../authentication/login.php"
                class="browse-menu-btn"
            >
                Login
            </a>

        </div>

    </main>

    <?php

    include '../includes/footer.php';

    exit;
}


$username =
    $_SESSION['username'];


// ============================================================
// REMOVE FROM WISHLIST
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['wishlist_remove'])
) {


    $wishlist_id =
        (int) $_POST['wishlist_id'];


    $delete_sql = "
        DELETE FROM wishlist
        WHERE wishlist_id = ?
        AND username = ?
    ";


    $delete_stmt =
        $conn->prepare($delete_sql);


    $delete_stmt->bind_param(
        "is",
        $wishlist_id,
        $username
    );


    $delete_stmt->execute();


    $delete_stmt->close();


    header("Location: wishlist.php");

    exit;
}


// ============================================================
// ADD WISHLIST ITEM TO CART
// ============================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['wishlist_add_cart'])
) {


    $wishlist_id =
        (int) $_POST['wishlist_id'];


    // ========================================================
    // GET WISHLIST ITEM
    // ========================================================

    $get_sql = "
        SELECT
            wishlist.restaurant_name
        FROM wishlist
        WHERE wishlist.wishlist_id = ?
        AND wishlist.username = ?
    ";


    $get_stmt =
        $conn->prepare($get_sql);


    $get_stmt->bind_param(
        "is",
        $wishlist_id,
        $username
    );


    $get_stmt->execute();


    $get_result =
        $get_stmt->get_result();


    if ($get_result->num_rows === 1) {


        $item =
            $get_result->fetch_assoc();


        $restaurant_name =
            $item['restaurant_name'];


        // ====================================================
        // CHECK CART
        // ====================================================

        $check_sql = "
            SELECT cart_id
            FROM cart
            WHERE username = ?
            AND restaurant_name = ?
        ";


        $check_stmt =
            $conn->prepare($check_sql);


        $check_stmt->bind_param(
            "ss",
            $username,
            $restaurant_name
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
                SET quantity = quantity + 1
                WHERE username = ?
                AND restaurant_name = ?
            ";


            $update_stmt =
                $conn->prepare($update_sql);


            $update_stmt->bind_param(
                "ss",
                $username,
                $restaurant_name
            );


            $update_stmt->execute();


            $update_stmt->close();


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
                VALUES (?, ?, 1)
            ";


            $insert_stmt =
                $conn->prepare($insert_sql);


            $insert_stmt->bind_param(
                "ss",
                $username,
                $restaurant_name
            );


            $insert_stmt->execute();


            $insert_stmt->close();

        }


        $check_stmt->close();

    }


    $get_stmt->close();


    header("Location: cart.php");

    exit;
}


// ============================================================
// GET WISHLIST
// ============================================================

$wishlist_items = [];


$sql = "
    SELECT
        wishlist.wishlist_id,
        wishlist.restaurant_name,
        restaurants.blind_box_price,
        restaurants.blind_box_description,
        restaurants.blind_box_food_category,
        restaurants.restaurant_address,
        restaurants.restaurant_opening_hours
    FROM wishlist

    INNER JOIN restaurants
        ON wishlist.restaurant_name =
           restaurants.restaurant_name

    WHERE wishlist.username = ?

    ORDER BY wishlist.added_at DESC
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


while (
    $row =
    $result->fetch_assoc()
) {

    $wishlist_items[] =
        $row;

}


$stmt->close();

$conn->close();


// ============================================================
// HEADER
// ============================================================

include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/wishlist.css">

<?php

include '../includes/navigation.php';

?>


<main class="wishlist-page">


    <div class="wishlist-container">


        <h1 class="wishlist-title">
            My Wishlist
        </h1>


        <!-- ================================================= -->
        <!-- EMPTY -->
        <!-- ================================================= -->

        <?php if (empty($wishlist_items)) : ?>


            <div class="empty-wishlist">


                <h2>
                    Your wishlist is empty
                </h2>


                <p>
                    Add a blind box from the menu
                    to your wishlist.
                </p>


                <a
                    href="menu.php"
                    class="browse-menu-btn"
                >
                    Browse Menu
                </a>


            </div>


        <?php else : ?>


            <!-- ================================================= -->
            <!-- WISHLIST GRID -->
            <!-- ================================================= -->

            <div class="wishlist-grid">


                <?php foreach (
                    $wishlist_items
                    as $item
                ) : ?>


                    <div class="wishlist-card">


                        <!-- RESTAURANT -->

                        <h2 class="wishlist-restaurant">

                            <?php
                            echo htmlspecialchars(
                                $item[
                                    'restaurant_name'
                                ]
                            );
                            ?>

                        </h2>


                        <!-- IMAGE -->

                        <img
                            src="../images/BBbox.png"
                            class="wishlist-food-image"
                            alt="Blind Box"
                        >


                        <!-- DESCRIPTION -->

                        <h3 class="wishlist-food-name">

                            <?php
                            echo htmlspecialchars(
                                $item[
                                    'blind_box_description'
                                ]
                            );
                            ?>

                        </h3>


                        <!-- CATEGORY -->

                        <p>

                            <strong>
                                Category:
                            </strong>

                            <?php
                            echo htmlspecialchars(
                                $item[
                                    'blind_box_food_category'
                                ]
                            );
                            ?>

                        </p>


                        <!-- PRICE -->

                        <p class="wishlist-price">

                            RM

                            <?php
                            echo number_format(
                                $item[
                                    'blind_box_price'
                                ],
                                2
                            );
                            ?>

                        </p>


                        <!-- BUTTONS -->

                        <div
                            class="wishlist-button-group"
                        >


                            <!-- ADD TO CART -->

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="wishlist_id"
                                    value="<?php
                                        echo $item[
                                            'wishlist_id'
                                        ];
                                    ?>"
                                >


                                <button
                                    type="submit"
                                    name="wishlist_add_cart"
                                    class="wishlist-cart-btn"
                                >

                                    🛒 Add to Cart

                                </button>

                            </form>


                            <!-- REMOVE -->

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="wishlist_id"
                                    value="<?php
                                        echo $item[
                                            'wishlist_id'
                                        ];
                                    ?>"
                                >


                                <button
                                    type="submit"
                                    name="wishlist_remove"
                                    class="wishlist-remove-btn"
                                >

                                    Remove

                                </button>

                            </form>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>


</main>

<script src="../js/script.js"></script>

<?php

include '../includes/footer.php';

?>