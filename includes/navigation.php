<header class="top-header">

    <!-- Hamburger -->
    <button class="menu-btn" onclick="toggleMenu()">☰</button>

    <!-- Logo -->
    <a href="../index.php" class="logo">
        <img src="/WAD-Food-Blind-Box/images/BBlogo.png" width="70" height="900">
    </a>

    <!-- Sidebar Navigation -->
    <nav id="sidebar" class="sidebar">
        <ul>
            <li><a href="/WAD-Food-Blind-Box/index.php">Home</a></li>
            <li><a href="/WAD-Food-Blind-Box/pages/menu.php">Menu</a></li>
            <li><a href="/WAD-Food-Blind-Box/pages/cart.php">Cart</a></li>
            <li><a href="/WAD-Food-Blind-Box/pages/orderhistory.php">Order</a></li>
            <li><a href="/WAD-Food-Blind-Box/pages/wishlist.php">Wishlist</a></li>
            <li><a href="/WAD-Food-Blind-Box/authentication/profile.php">Profile</a></li>
            <li><a href="/WAD-Food-Blind-Box/pages/aboutus.php">About Us</a></li>
            
            <?php if(isset($_SESSION['username'])){ ?>
                <li><a href="/WAD-Food-Blind-Box/authentication/logout.php">Log Out</a></li>
            <?php } 
                else
                { ?>
                <li><a href="/WAD-Food-Blind-Box/authentication/login.php">Log In</a></li> 
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