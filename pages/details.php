<?php
session_start();
include '../config/db_connect.php';

$restaurant_name = isset($_GET['restaurant']) ? $_GET['restaurant'] : '';
$cart_message = "";

// Handle "Add to Cart" form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    if (!isset($_SESSION['username'])) {
        header("Location: ../authentication/login.php");
        exit();
    }

    $restaurant_to_add = $_POST['restaurant_name'];
    $food_name_to_add = $_POST['food_name'];
    $price_to_add = $_POST['price'];

    // Check if this item is already in the user's cart
    $check_sql = "SELECT cart_id, quantity FROM cart WHERE username = ? AND restaurant = ? AND food_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $_SESSION['username'], $restaurant_to_add, $food_name_to_add);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Already in cart — increase quantity
        $existing = $check_result->fetch_assoc();
        $new_qty = $existing['quantity'] + 1;

        $update_sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_qty, $existing['cart_id']);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Not in cart yet — insert new row
        $insert_sql = "INSERT INTO cart (username, restaurant, food_name, quantity, price) VALUES (?, ?, ?, 1, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssd", $_SESSION['username'], $restaurant_to_add, $food_name_to_add, $price_to_add);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $check_stmt->close();
    $cart_message = "Added to cart!";
}

// Fetch the restaurant details to display
$sql = "SELECT * FROM restaurants WHERE restaurant_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $restaurant_name);
$stmt->execute();
$result = $stmt->get_result();
$restaurant = $result->num_rows === 1 ? $result->fetch_assoc() : null;
$stmt->close();
$conn->close();

include '../includes/header.php';
include '../includes/navigation.php';
?>

<main class="details-page">

    <div class="details-card">

        <img src="../images/BBbox.png" width="200" height="150">

        <?php if ($restaurant) : ?>

            <h1><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></h1>
            <p><strong>Opening Hours:</strong> <?php echo htmlspecialchars($restaurant['restaurant_opening_hours']); ?></p>
            <p><strong>Blind Box Description:</strong> <?php echo htmlspecialchars($restaurant['blind_box_description']); ?></p>
            <p><strong>Blind Box Price:</strong> RM <?php echo number_format($restaurant['blind_box_price'], 2); ?></p>
            <p><strong>Remaining Quantity:</strong> <?php echo htmlspecialchars($restaurant['blind_box_remaining_quantity']); ?> Boxes</p>
            <p><strong>Food Category:</strong> <?php echo htmlspecialchars($restaurant['blind_box_food_category']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($restaurant['restaurant_phone_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($restaurant['restaurant_address']); ?></p>

            <?php if (!empty($cart_message)) : ?>
                <p class="cart-message"><?php echo htmlspecialchars($cart_message); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="restaurant_name" value="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>">
                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>

        <?php else : ?>

            <h1>Restaurant not found</h1>

        <?php endif; ?>

    </div>

</main>

<?php include '../includes/footer.php'; ?>