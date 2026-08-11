<header class="top-header">

    <!-- Hamburger -->
    <button class="menu-btn" onclick="toggleMenu()">☰</button>

    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
        <img src="<?php echo BASE_URL; ?>/images/BBlogo.png" width="70" height="900">
    </a>

    <!-- Sidebar Navigation -->
    <nav id="sidebar" class="sidebar">
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/menu.php">Menu</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/cart.php">Cart</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/wishlist.php">Wishlist</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/orderhistory.php">Order</a></li>
            <li><a href="<?php echo BASE_URL; ?>/authentication/profile.php">Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/aboutus.php">About Us</a></li>
            
            <?php if(isset($_SESSION['username'])){ ?>
                <li><a href="<?php echo BASE_URL; ?>/authentication/logout.php">Log Out</a></li>
            <?php } 
                else
                { ?>
                <li><a href="<?php echo BASE_URL; ?>/authentication/login.php">Log In</a></li> 
                <?php } ?>
        </ul>
    </nav>

    <script>
function toggleMenu() {

    const sidebar = document.getElementById("sidebar");

    sidebar.classList.toggle("active");

}
</script>
</header>