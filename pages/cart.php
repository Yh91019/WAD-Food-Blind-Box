<?php include "../includes/header.php"; ?>
<?php include "../includes/navigation.php"; ?>

<?php
$cart = [
    [
        "restaurant" => "McDonald's",
        "food"       => "Big Mac",
        "price"      => 18.90,
        "image"      => "../images/burger.jpg",
        "quantity"   => 1
    ],
    [
        "restaurant" => "KFC",
        "food"       => "Snack Plate",
        "price"      => 29.80,
        "image"      => "../images/chicken.jpg",
        "quantity"   => 2
    ],
    [
        "restaurant" => "Pizza Hut",
        "food"       => "Pepperoni Pizza",
        "price"      => 35.50,
        "image"      => "../images/pizza.jpg",
        "quantity"   => 1
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Cart</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="cart-container">

        <h1>🛒 My Cart</h1>

        <table>
            <thead>
                <tr>
                    <th>Restaurant</th>
                    <th>Food</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>
            </thead>

            <tbody id="cartBody">
                <?php foreach ($cart as $item) { ?>

                    <tr>
                        <td>
                            <?php echo $item['restaurant']; ?>
                        </td>

                        <td>
                            <img src="<?php echo $item['image']; ?>" class="food-img">
                            <br>
                            <?php echo $item['food']; ?>
                        </td>

                        <td>
                            <div class="quantity">
                                <button class="minus">-</button>

                                <input
                                    type="text"
                                    class="qty"
                                    value="<?php echo $item['quantity']; ?>"
                                    readonly>

                                <button class="plus">+</button>
                            </div>
                        </td>

                        <td class="price">
                            <?php echo number_format($item['price'], 2); ?>
                        </td>

                        <td class="subtotal">
                            <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </td>

                        <td>
                            <button class="remove-btn">
                                Remove
                            </button>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>

        <div class="order-type">
            <h2>Order Type</h2>

            <label>
                <input
                    type="radio"
                    name="delivery"
                    value="pickup"
                    checked>
                Pickup
            </label>

            <label>
                <input
                    type="radio"
                    name="delivery"
                    value="delivery">
                Delivery (+RM5)
            </label>
        </div>

        <div class="summary">
            <h2>
                Subtotal :
                RM <span id="subtotal">0.00</span>
            </h2>

            <h3>
                Delivery Fee :
                RM <span id="deliveryFee">0.00</span>
            </h3>

            <h2>
                Grand Total :
                RM <span id="grandTotal">0.00</span>
            </h2>

            <button class="checkout-btn">
                Checkout
            </button>
        </div>

    </div>

    <script src="../js/cart.js"></script>
    <script src="../js/script.js"></script>

</body>

</html>