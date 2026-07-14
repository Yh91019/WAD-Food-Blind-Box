<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Wishlist</title>

    <link rel="stylesheet" href="wishlist.css">

</head>

<body>

<div class="wishlist-container">

    <h1>❤️ My Wishlist</h1>

    <!-- Search -->

    <div class="search-box">

        <input
            type="text"
            id="searchInput"
            placeholder="Search restaurant or food..."
            onkeyup="searchWishlist()">

    </div>

    <!-- Wishlist Items -->

    <div class="wishlist-grid" id="wishlistGrid">

        <!-- Card 1 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                McDonald's
            </h2>

            <img src="images/burger.jpg" class="food-image">

            <h3 class="food-name">
                Big Mac
            </h3>

            <p class="price">
                RM18.90
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- Card 2 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                KFC
            </h2>

            <img src="images/chicken.jpg" class="food-image">

            <h3 class="food-name">
                Snack Plate
            </h3>

            <p class="price">
                RM29.80
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- Card 3 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                Pizza Hut
            </h2>

            <img src="images/pizza.jpg" class="food-image">

            <h3 class="food-name">
                Pepperoni Pizza
            </h3>

            <p class="price">
                RM35.50
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- Card 4 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                Subway
            </h2>

            <img src="images/subway.jpg" class="food-image">

            <h3 class="food-name">
                Chicken Teriyaki
            </h3>

            <p class="price">
                RM20.90
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- Card 5 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                Domino's
            </h2>

            <img src="images/domino.jpg" class="food-image">

            <h3 class="food-name">
                Hawaiian Pizza
            </h3>

            <p class="price">
                RM32.90
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- Card 6 -->

        <div class="wishlist-card">

            <h2 class="restaurant">
                Sushi King
            </h2>

            <img src="images/sushi.jpg" class="food-image">

            <h3 class="food-name">
                Salmon Sushi Set
            </h3>

            <p class="price">
                RM27.90
            </p>

            <div class="button-group">

                <button class="cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="remove-btn">
                    Remove
                </button>

            </div>

        </div>

    </div>

</div>

<script src="wishlist.js"></script>

</body>

</html>