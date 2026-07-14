<?php

$orders = [

    [
        "id" => "BB1001",
        "restaurant" => "McDonald's",
        "food" => "Big Mac Blind Box",
        "date" => "10 July 2026",
        "type" => "Pickup",
        "status" => "Completed",
        "price" => 18.90
    ],

    [
        "id" => "BB1002",
        "restaurant" => "KFC",
        "food" => "Snack Plate Blind Box",
        "date" => "8 July 2026",
        "type" => "Delivery",
        "status" => "Completed",
        "price" => 29.80
    ],

    [
        "id" => "BB1003",
        "restaurant" => "Pizza Hut",
        "food" => "Pizza Surprise Box",
        "date" => "5 July 2026",
        "type" => "Delivery",
        "status" => "Preparing",
        "price" => 35.50
    ],

    [
        "id" => "BB1004",
        "restaurant" => "Starbucks",
        "food" => "Coffee Surprise Box",
        "date" => "2 July 2026",
        "type" => "Pickup",
        "status" => "Cancelled",
        "price" => 15.90
    ]

];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order History</title>

    <link rel="stylesheet" href="orderhistory.css">

</head>

<body>

<header>

    <h1>📦 Order History</h1>

</header>

<div class="history-container">

<?php foreach($orders as $order){ ?>

    <div class="history-card">

        <div class="top">

            <h2><?php echo $order['restaurant']; ?></h2>

            <span class="status <?php echo strtolower($order['status']); ?>">

                <?php echo $order['status']; ?>

            </span>

        </div>

        <hr>

        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>

        <p><strong>Food Item:</strong> <?php echo $order['food']; ?></p>

        <p><strong>Order Date:</strong> <?php echo $order['date']; ?></p>

        <p><strong>Order Type:</strong> <?php echo $order['type']; ?></p>

        <p><strong>Total Paid:</strong> RM <?php echo number_format($order['price'],2); ?></p>

        <div class="buttons">

            <button class="view-btn">

                View Details

            </button>

            <button class="reorder-btn">

                Reorder

            </button>

        </div>

    </div>

<?php } ?>

</div>

</body>

</html>