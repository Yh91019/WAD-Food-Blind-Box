<?php
session_start();
include '../config/db_connect.php';
include '../includes/header.php';
include '../includes/navigation.php';

$cart_items = [];
$total = 0;

if (isset($_SESSION['username'])) {

    $sql = "SELECT * FROM cart WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }

    $stmt->close();
}

$conn->close();
?>

<main class="cart-page">

    <h1>My Cart</h1>

    <?php if (!isset($_SESSION['username'])) : ?>

        <p>Please <a href="../authentication/login.php">log in</a> to view your cart.</p>

    <?php elseif (empty($cart_items)) : ?>

        <p>Your cart is empty.</p>
        <a href="../pages/menu.php" class="browse-menu-btn">Browse Menu</a>

    <?php else : ?>

        <div class="cart-container">

            <?php foreach ($cart_items as $item) : ?>

                <div class="cart-item">
                    <h3><?php echo htmlspecialchars($item['restaurant']); ?></h3>
                    <p><?php echo htmlspecialchars($item['food_name']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p><strong>Price:</strong> RM <?php echo number_format($item['price'], 2); ?></p>
                </div>

            <?php endforeach; ?>

        </div>

        <p class="cart-total"><strong>Total:</strong> RM <?php echo number_format($total, 2); ?></p>

        <a href="../pages/menu.php" class="browse-menu-btn">Add More from Menu</a>

    <?php endif; ?>

    <script src="../js/script.js"></script>
    
</main>

<?php include '../includes/footer.php'; ?>