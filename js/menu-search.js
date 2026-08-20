document.addEventListener("DOMContentLoaded", function () {

    var searchInput = document.getElementById("menuSearchInput");
    var noResults = document.getElementById("menuNoResults");
    var container = document.getElementById("restaurantContainer");
    var ratingSortBtn = document.getElementById("ratingSortBtn");
    var ratingSortIcon = document.getElementById("ratingSortIcon");

    if (!searchInput || !container) {
        return;
    }

    var cards = Array.prototype.slice.call(
        container.querySelectorAll(".restaurant-card")
    );

    cards.forEach(function (card, index) {
        card.dataset.originalIndex = index;
    });

    // Sort state cycles: "none" (random order, the default) -> "desc"
    // (highest to lowest, on the first click) -> "asc" (lowest to highest,
    // on the second click) -> "desc" -> "asc" ... on every click after that.
    var sortState = "none";

    var sortLabels = {
        none: {
            icon: "↕",
            aria: "Rating: not sorted. Click to sort highest to lowest."
        },
        desc: {
            icon: "↓",
            aria: "Rating: highest to lowest. Click to sort lowest to highest."
        },
        asc: {
            icon: "↑",
            aria: "Rating: lowest to highest. Click to sort highest to lowest."
        }
    };

    function shuffleCards() {

        for (var i = cards.length - 1; i > 0; i--) {

            var j = Math.floor(Math.random() * (i + 1));

            var temp = cards[i];
            cards[i] = cards[j];
            cards[j] = temp;

        }

    }

    function sortCards() {

        cards.sort(function (firstCard, secondCard) {

            var firstRating = parseFloat(firstCard.dataset.rating) || 0;
            var secondRating = parseFloat(secondCard.dataset.rating) || 0;
            var firstReviewCount = Number(firstCard.dataset.reviewCount) || 0;
            var secondReviewCount = Number(secondCard.dataset.reviewCount) || 0;

            if (secondRating !== firstRating) {
                return sortState === "asc"
                    ? firstRating - secondRating
                    : secondRating - firstRating;
            }

            if (secondReviewCount !== firstReviewCount) {
                return sortState === "asc"
                    ? firstReviewCount - secondReviewCount
                    : secondReviewCount - firstReviewCount;
            }

            return (firstCard.dataset.name || "").localeCompare(
                secondCard.dataset.name || ""
            );

        });

    }

    function renderCards() {

        cards.forEach(function (card) {
            container.appendChild(card);
        });

    }

    function updateSortButton() {

        if (!ratingSortBtn) {
            return;
        }

        ratingSortBtn.dataset.sort = sortState;

        if (ratingSortIcon) {
            ratingSortIcon.textContent = sortLabels[sortState].icon;
        }

        ratingSortBtn.setAttribute("aria-label", sortLabels[sortState].aria);

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

    if (ratingSortBtn) {
        ratingSortBtn.addEventListener("click", function () {

            // First click: none -> desc. Second click: desc -> asc.
            // Every click after that keeps toggling desc <-> asc.
            sortState = sortState === "desc" ? "asc" : "desc";

            updateSortButton();
            sortCards();
            renderCards();
            filterCards();

        });
    }

    // Default view: random order, unsorted, until the button is clicked.
    updateSortButton();
    shuffleCards();
    renderCards();
    filterCards();

});
