<?php

session_start();

$order_total = $_SESSION['order_total'] ?? null;
unset($_SESSION['order_total']);

include "../includes/header.php";
include "../includes/navigation.php";

?>

<link rel="stylesheet" href="../css/order_complete.css">

<div class="order-complete-page">

    <div class="order-complete-box">

        <div class="success-icon">
            ✓
        </div>

        <h1>
            Your Order Has Been Placed Successfully!
        </h1>

        <p>
            Thank you for ordering from Blind Bite.
        </p>

        <?php if ($order_total !== null) : ?>
            <p><strong>Total paid: RM <?php echo number_format((float) $order_total, 2); ?></strong></p>
        <?php endif; ?>

        <div class="order-complete-buttons">

            <a href="orderhistory.php" class="history-btn">
                📦 View Order History
            </a>

            <a href="menu.php" class="menu-btn-complete">
                🍔 Continue Shopping
            </a>

        </div>

    </div>

</div>


<?php include "../includes/footer.php"; ?>
