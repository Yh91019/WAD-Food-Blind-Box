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

        <a href="<?php echo BASE_URL; ?>/pages/orderhistory.php" class="header-icon-link" aria-label="Order" title="Order">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
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