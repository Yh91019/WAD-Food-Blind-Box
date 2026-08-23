<?php

session_start();

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
