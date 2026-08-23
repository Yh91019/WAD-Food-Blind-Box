<header class="top-header">

    <!-- Hamburger -->
    <button class="menu-btn" onclick="toggleMenu()">☰</button>

    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
        <img src="<?php echo BASE_URL; ?>/images/BBlogo.png" width="70" height="900">
    </a>

    <!-- Header Icons -->
    <div class="header-icons">

        <a href="<?php echo BASE_URL; ?>/pages/cart.php" class="header-icon-link" aria-label="Cart" title="Cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
        </a>

        <a href="<?php echo BASE_URL; ?>/pages/menu.php" class="header-icon-link" aria-label="Menu" title="Menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <!-- Fork -->
                <path d="M7 2v7"></path>
                <path d="M4.5 2v5a2.5 2.5 0 0 0 5 0V2"></path>
                <path d="M7 9v13"></path>
                <!-- Knife -->
                <path d="M17 2c-2 1.5-3 4-3 7s1 4 3 5v8"></path>
            </svg>
        </a>

        <?php if (isset($_SESSION['username'])) { ?>
            <a href="<?php echo BASE_URL; ?>/authentication/profile.php" class="header-icon-link" aria-label="Profile" title="Profile">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php } else { ?>
            <a href="<?php echo BASE_URL; ?>/authentication/login.php" class="header-icon-link" aria-label="Login" title="Login">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php } ?>

    </div>

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
            <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact Us</a></li>
            
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

<!-- Return Bar: quick way back to the previous page -->
<div class="back-nav">
    <button type="button" class="back-btn" onclick="goToPreviousPage()" aria-label="Go back" title="Go back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
        </svg>
        <span>Back</span>
    </button>
</div>

<script>
function goToPreviousPage() {
    history.back();
}
</script>
