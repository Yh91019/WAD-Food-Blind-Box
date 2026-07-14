<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Wishlist</title>

    <link rel="stylesheet" href="../css/wishlist.css">

</head>

<body id="wishlist-page">

<div class="wishlist-container">

    <h1 class="wishlist-title">
        ❤️ My Wishlist
    </h1>

    <!-- ===================== SEARCH ===================== -->

    <div class="wishlist-search-box">

        <input
            type="text"
            id="wishlist-search-input"
            placeholder="Search restaurant or food..."
            autocomplete="off"
            onkeyup="showSuggestions()">

        <div id="suggestion-box" class="suggestion-box"></div>

    </div>

    <!-- ===================== WISHLIST ===================== -->

    <div class="wishlist-grid" id="wishlist-grid">

        <!-- ===================== CARD 1 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                McDonald's
            </h2>

            <img
                src="images/burger.jpg"
                class="wishlist-food-image"
                alt="Big Mac">

            <h3 class="wishlist-food-name">
                Big Mac
            </h3>

            <p class="wishlist-price">
                RM18.90
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- ===================== CARD 2 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                KFC
            </h2>

            <img
                src="images/chicken.jpg"
                class="wishlist-food-image"
                alt="Snack Plate">

            <h3 class="wishlist-food-name">
                Snack Plate
            </h3>

            <p class="wishlist-price">
                RM29.80
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- ===================== CARD 3 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                Pizza Hut
            </h2>

            <img
                src="images/pizza.jpg"
                class="wishlist-food-image"
                alt="Pepperoni Pizza">

            <h3 class="wishlist-food-name">
                Pepperoni Pizza
            </h3>

            <p class="wishlist-price">
                RM35.50
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- ===================== CARD 4 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                Subway
            </h2>

            <img
                src="images/subway.jpg"
                class="wishlist-food-image"
                alt="Chicken Teriyaki">

            <h3 class="wishlist-food-name">
                Chicken Teriyaki
            </h3>

            <p class="wishlist-price">
                RM20.90
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- ===================== CARD 5 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                Domino's
            </h2>

            <img
                src="images/domino.jpg"
                class="wishlist-food-image"
                alt="Hawaiian Pizza">

            <h3 class="wishlist-food-name">
                Hawaiian Pizza
            </h3>

            <p class="wishlist-price">
                RM32.90
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

        <!-- ===================== CARD 6 ===================== -->

        <div class="wishlist-card">

            <h2 class="wishlist-restaurant">
                Sushi King
            </h2>

            <img
                src="images/sushi.jpg"
                class="wishlist-food-image"
                alt="Salmon Sushi Set">

            <h3 class="wishlist-food-name">
                Salmon Sushi Set
            </h3>

            <p class="wishlist-price">
                RM27.90
            </p>

            <div class="wishlist-button-group">

                <button class="wishlist-cart-btn">
                    🛒 Add to Cart
                </button>

                <button class="wishlist-remove-btn">
                    Remove
                </button>

            </div>

        </div>

    </div>

</div>

<script src="../js/wishlist.js"></script>

</body>

</html>  