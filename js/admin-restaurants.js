const searchInput = document.getElementById("restaurantSearch");
const restaurantRows = document.querySelectorAll(".restaurant-row");
const noSearchResults = document.getElementById("noSearchResults");

searchInput.addEventListener("input", function () {
    const searchValue = this.value.trim().toLowerCase();
    let foundRestaurant = false;

    restaurantRows.forEach(function (row) {
        const restaurantName = row.getAttribute("data-restaurant-name");

        if (restaurantName.includes(searchValue)) {
            row.style.display = "";
            foundRestaurant = true;
        } else {
            row.style.display = "none";
        }
    });

    if (!foundRestaurant && searchValue !== "") {
        noSearchResults.style.display = "block";
    } else {
        noSearchResults.style.display = "none";
    }
});

document.querySelectorAll(".restaurant-delete-form").forEach(function (form) {
    form.addEventListener("submit", function (event) {
        if (!window.confirm("Are you sure you want to delete this restaurant?")) {
            event.preventDefault();
        }
    });
});
