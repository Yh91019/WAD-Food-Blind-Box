<?php
session_start();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<?php
if(isset($_SESSION['login_message'])){
?>

<div id="login-popup">
    <?php echo $_SESSION['login_message']; ?>
</div>

<?php
unset($_SESSION['login_message']);
}
?>

<?php
if(isset($_SESSION['logout_message'])){
?>

<div id="logout-popup">
    <?php echo  $_SESSION['logout_message']; ?>
</div>

<?php
unset($_SESSION['logout_message']);
}
?>

<main>
    <section class="home-hero">
        <img src="images/bg.jpg" alt="Blind Bite">
    </section>


    <?php
    include 'config/db_connect.php';

    $sql = "SELECT * FROM restaurants";

    $result = $conn->query($sql);
    ?>

    <section class="home-restaurants">
        <div class="restaurants-header">

            <h1>Explore Our Blind Box Restaurants</h1>

            <p>
                Discover surprise meals from restaurants near you
            </p>

        </div>

        <div class="home-restaurant-container">

            <?php

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

            ?>

                    <a href="pages/details.php?restaurant=<?php
                        echo urlencode($row['restaurant_name']);
                    ?>" class="home-restaurant-card">


                        <!-- Restaurant Image -->

                        <div class="home-restaurant-image">

                            <img
                                src="<?php echo htmlspecialchars(restaurant_image_url($row['blind_box_image'] ?? null)); ?>"
                                alt="<?php echo htmlspecialchars($row['restaurant_name']); ?> blind box"
                            >

                        </div>


                        <!-- Restaurant Information -->

                        <div class="home-restaurant-info">

                            <h2>
                                <?php
                                echo htmlspecialchars(
                                    $row['restaurant_name']
                                );
                                ?>
                            </h2>


                            <p class="restaurant-category">
                                <?php
                                echo htmlspecialchars(
                                    $row['blind_box_food_category']
                                );
                                ?>
                            </p>


                            <p class="restaurant-price">
                                RM
                                <?php
                                echo number_format(
                                    $row['blind_box_price'],
                                    2
                                );
                                ?>
                            </p>


                            <p class="restaurant-location">
                                <?php
                                echo htmlspecialchars(
                                    $row['restaurant_address']
                                );
                                ?>
                            </p>

                        </div>

                    </a>

            <?php

                }

            } else {

                echo '<p class="no-restaurants">
                        No restaurants available.
                      </p>';

            }

            ?>

        </div>


        <!-- View All Button -->

        <div class="view-all-container">

            <a href="pages/menu.php" class="view-all-btn">
                View All Restaurants
            </a>

        </div>

    </section>
</main>
<?php include('includes/footer.php'); ?>
