<header class="top-header">

<!-- Hamburger -->
<button type="button" class="menu-btn" id="adminMenuButton">☰</button>

<!-- Logo -->
<a href="../admin/dashboard.php" class="logo">
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
                Manage Restaurant
            </a>
        </li>

        <li>
            <a href="../admin/promotions.php">
                Manage Promotions
            </a>
        </li>

        <li>
            <a href="../admin/enquiries.php">
                Enquiries
            </a>
        </li>

        <li>
            <a href="../authentication/logout.php">
                Log Out
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

<!-- Same return bar used throughout the user pages. -->
<div class="back-nav">
    <button type="button" class="back-btn" onclick="goToPreviousAdminPage()" aria-label="Go back" title="Go back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
        </svg>
        <span>Back</span>
    </button>
</div>

<script>
function goToPreviousAdminPage() {
    history.back();
}
</script>
