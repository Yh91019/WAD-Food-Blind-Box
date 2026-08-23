const userMenuButton = document.getElementById("userMenuButton");
const userSidebar = document.getElementById("sidebar");
const userBackButton = document.getElementById("userBackButton");

userMenuButton.addEventListener("click", function () {
    userSidebar.classList.toggle("active");
});

document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.querySelector(".menu-btn");

    const isClickInsideMenu = sidebar.contains(event.target);
    const isClickOnButton = menuBtn.contains(event.target);

    if (!isClickInsideMenu && !isClickOnButton) {
        sidebar.classList.remove("active");
    }
});

userBackButton.addEventListener("click", function () {
    history.back();
});
