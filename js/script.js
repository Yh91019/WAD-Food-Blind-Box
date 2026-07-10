function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("active");
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.querySelector(".menu-btn");

    const isClickInsideMenu = sidebar.contains(event.target);
    const isClickOnButton = menuBtn.contains(event.target);

    if (!isClickInsideMenu && !isClickOnButton) {
        sidebar.classList.remove("active");
    }
});