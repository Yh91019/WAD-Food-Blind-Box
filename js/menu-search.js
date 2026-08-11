document.addEventListener("DOMContentLoaded", function () {

    var searchInput = document.getElementById("menuSearchInput");
    var noResults = document.getElementById("menuNoResults");
    var container = document.getElementById("restaurantContainer");

    if (!searchInput || !container) {
        return;
    }

    var cards = container.querySelectorAll(".restaurant-card");

    function filterCards() {

        var query = searchInput.value.trim().toLowerCase();

        var visibleCount = 0;

        cards.forEach(function (card) {

            var name = card.getAttribute("data-name") || "";
            var category = card.getAttribute("data-category") || "";

            var matches =
                query === "" ||
                name.indexOf(query) !== -1 ||
                category.indexOf(query) !== -1;

            card.style.display = matches ? "" : "none";

            if (matches) {
                visibleCount++;
            }

        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? "block" : "none";
        }

    }

    searchInput.addEventListener("input", filterCards);

    filterCards();

});