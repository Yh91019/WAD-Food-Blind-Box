document.addEventListener("DOMContentLoaded", function () {

    var searchInput = document.getElementById("menuSearchInput");
    var noResults = document.getElementById("menuNoResults");
    var container = document.getElementById("restaurantContainer");
    var ratingSort = document.getElementById("ratingSort");
    var ratingSortDirection = document.getElementById("ratingSortDirection");

    if (!searchInput || !container) {
        return;
    }

    var cards = Array.prototype.slice.call(
        container.querySelectorAll(".restaurant-card")
    );

    cards.forEach(function (card, index) {
        card.dataset.originalIndex = index;
    });

    function sortCards() {

        var direction = ratingSort && ratingSort.checked ? "asc" : "desc";

        cards.sort(function (firstCard, secondCard) {

            var firstRating = parseFloat(firstCard.dataset.rating) || 0;
            var secondRating = parseFloat(secondCard.dataset.rating) || 0;
            var firstReviewCount = Number(firstCard.dataset.reviewCount) || 0;
            var secondReviewCount = Number(secondCard.dataset.reviewCount) || 0;

            if (secondRating !== firstRating) {
                if (direction === "asc") {
                    return firstRating - secondRating;
                }

                if (direction === "desc") {
                    return secondRating - firstRating;
                }
            }

            if (secondReviewCount !== firstReviewCount) {
                return direction === "asc"
                    ? firstReviewCount - secondReviewCount
                    : secondReviewCount - firstReviewCount;
            }

            return (firstCard.dataset.name || "").localeCompare(
                secondCard.dataset.name || ""
            );
        });

        cards.forEach(function (card) {
            container.appendChild(card);
        });
    }

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

    if (ratingSort) {
        ratingSort.addEventListener("change", function () {
            var isAscending = this.checked;

            if (ratingSortDirection) {
                ratingSortDirection.textContent = isAscending ? "↓" : "↑";
            }

            this.setAttribute(
                "aria-label",
                isAscending
                    ? "Rating: lowest to highest. Toggle for highest to lowest."
                    : "Rating: highest to lowest. Toggle for lowest to highest."
            );

            sortCards();
            filterCards();
        });
    }

    sortCards();
    filterCards();

});
