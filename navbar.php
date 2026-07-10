<header>

    <div class="menu-icon" onclick="toggleMenu()">
        ☰
    </div>

    <a href="homepage.php">
        <img src="shop logo.png" alt="Blind Bite Logo" class="logo">
    </a>

    <nav id="sidebar" class="sidebar">
        <ul>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <a href="login.php">
        <button class="login">Login/Register</button>
    </a>

</header>

<script>
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("show");
}
</script>