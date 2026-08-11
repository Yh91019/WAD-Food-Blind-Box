<header class="top-header">

<!-- Hamburger -->
<button type="button" class="menu-btn" id="adminMenuButton">☰</button>

<!-- Logo -->
<a href="../index.php" class="logo">
    <img src="../images/BBlogo.png" width="70" height="200">
</a>

<!-- Sidebar Navigation -->
<nav class="sidebar" id="adminSidebar">

    <ul>

        <li>
            <a href="../admin/dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="../admin/restaurants.php">
                Restaurants
            </a>
        </li>

        <li>
            <a href="../authentication/logout.php">
                Logout
            </a>
        </li>

    </ul>

</nav>


<script>

const menuButton = document.getElementById("adminMenuButton");
const sidebar = document.getElementById("adminSidebar");


// Hamburger button
menuButton.addEventListener("click", function(event) {

    event.stopPropagation();

    sidebar.classList.toggle("active");

});


// Click outside navigation
document.addEventListener("click", function(event) {

    if (
        sidebar.classList.contains("active") &&
        !sidebar.contains(event.target) &&
        !menuButton.contains(event.target)
    ) {

        sidebar.classList.remove("active");

    }

});

</script>
</header>