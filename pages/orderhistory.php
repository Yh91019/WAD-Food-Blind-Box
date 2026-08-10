<?php
include '../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$orders = [];

if (isset($_SESSION['username'])) {

    $sql = "SELECT history_id, restaurant_name, blind_box_price, quantity, payment_method, order_type, status, order_date
            FROM history
            WHERE username = ?
            ORDER BY order_date DESC";

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

<main class="orderhistory-page">

    <h1>📦 Order History</h1>

    <?php if (!isset($_SESSION['username'])) : ?>

        <p>Please <a href="../authentication/login.php">log in</a> to view your order history.</p>

    <?php elseif (empty($orders)) : ?>

        <p>You haven't placed any orders yet.</p>
        <a href="../pages/menu.php" class="browse-menu-btn">Browse Menu</a>

    <?php else : ?>

        <div class="history-container">

            <?php foreach ($orders as $order) : ?>

                <div class="history-card">

                    <div class="top">
                        <h2><?php echo htmlspecialchars($order['restaurant_name']); ?></h2>
                        <span class="status <?php echo strtolower($order['status']); ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>

                    <hr>

                    <p><strong>Order #:</strong> <?php echo (int) $order['history_id']; ?></p>
                    <p><strong>Quantity:</strong> <?php echo (int) $order['quantity']; ?> Blind Box(es)</p>
                    <p><strong>Order Type:</strong> <?php echo htmlspecialchars($order['order_type']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('d F Y, g:i A', strtotime($order['order_date'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Total Paid:</strong> RM <?php echo number_format($order['total'], 2); ?></p>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

   


</main>

 <script src="../js/script.js"></script>

<?php include '../includes/footer.php'; ?>
