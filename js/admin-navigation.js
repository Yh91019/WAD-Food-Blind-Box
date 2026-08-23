const menuButton = document.getElementById("adminMenuButton");
const sidebar = document.getElementById("adminSidebar");
const adminBackButton = document.getElementById("adminBackButton");

menuButton.addEventListener("click", function (event) {
    event.stopPropagation();
    sidebar.classList.toggle("active");
});

document.addEventListener("click", function (event) {
    if (
        sidebar.classList.contains("active") &&
        !sidebar.contains(event.target) &&
        !menuButton.contains(event.target)
    ) {
        sidebar.classList.remove("active");
    }
});

adminBackButton.addEventListener("click", function () {
    history.back();
});
