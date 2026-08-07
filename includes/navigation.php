<header class="top-header">

    <!-- Hamburger -->
    <button class="menu-btn" onclick="toggleMenu()">☰</button>

    <!-- Logo -->
    <a href="../index.php" class="logo">
        <img src="../images/BBlogo.png" width="70" height="900">
    </a>

    <!-- Sidebar Navigation -->
    <nav id="sidebar" class="sidebar">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../pages/menu.php">Menu</a></li>
            <li><a href="../pages/cart.php">Cart</a></li>
            <li><a href="../authentication/profile.php">Profile</a></li>
            <li><a href="../pages/aboutus.php">About Us</a></li>
            
            <?php if(isset($_SESSION['username'])){ ?>
                <li><a href="../authentication/logout.php">Log Out</a></li>
            <?php } 
                else
                { ?>
                <li><a href="../authentication/login.php">Log In</a></li> 
                <?php } ?>
        </ul>
    </nav>
</header>