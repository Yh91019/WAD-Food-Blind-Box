/* ===================== RESTAURANT OPEN/CLOSED STATUS ===================== */
/*
   Each restaurant has an opening and closing time (stored in the database
   as "HH:MM:SS"). The restaurant is treated as OPEN while the current time
   on the visitor's own computer falls between those two times. If the
   closing time is earlier than (or equal to) the opening time, the
   restaurant is treated as closing after midnight.
*/

function parseTimeToMinutes(timeString) {

    const parts = (timeString || "").split(":");

    const hours   = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);

    if (isNaN(hours) || isNaN(minutes)) {
        return null;
    }

    return (hours * 60) + minutes;
}

function isRestaurantOpenNow(openingTime, closingTime) {

    const openingMinutes = parseTimeToMinutes(openingTime);
    const closingMinutes = parseTimeToMinutes(closingTime);

    if (openingMinutes === null || closingMinutes === null) {
        return null;
    }

    const now = new Date();
    const nowMinutes = (now.getHours() * 60) + now.getMinutes();

    if (closingMinutes > openingMinutes) {
        // Normal same-day hours (e.g. 09:00 - 22:00)
        return nowMinutes >= openingMinutes && nowMinutes < closingMinutes;
    }

    // Closing time is on/after midnight (e.g. 18:00 - 02:00)
    return nowMinutes >= openingMinutes || nowMinutes < closingMinutes;
}

function updateStatusBadges() {

    const badges = document.querySelectorAll(".status-badge");

    badges.forEach(function (badge) {

        const isOpen = isRestaurantOpenNow(
            badge.dataset.opening || "",
            badge.dataset.closing || ""
        );

        if (isOpen === null) {
            badge.textContent = "Unknown";
            badge.classList.remove("status-open", "status-closed");
            return;
        }

        badge.textContent = isOpen ? "Open" : "Closed";
        badge.classList.toggle("status-open", isOpen);
        badge.classList.toggle("status-closed", !isOpen);

    });

}

/* ===================== GATE "ADD TO CART" WHILE CLOSED ===================== */
/*
   On the restaurant details page, adding to the cart should only be
   possible while the restaurant is open. Adding to the wishlist stays
   available at all times, so it's left untouched here.
*/

function applyCartGating() {

    const badge = document.getElementById("restaurantStatusBadge");
    const cartForm = document.getElementById("addToCartForm");

    if (!badge || !cartForm) {
        return;
    }

    const isClosed = badge.classList.contains("status-closed");

    if (isClosed) {

        // Lock the whole form -- quantity buttons included -- while closed.
        cartForm
            .querySelectorAll("button, input")
            .forEach(function (control) {
                control.disabled = true;
            });

    } else {

        // Only re-enable the submit button here; the quantity +/- buttons
        // stay governed by quantity.js's own min/max logic so we don't
        // fight it.
        const submitBtn = cartForm.querySelector('button[name="add_to_cart"]');

        if (submitBtn) {
            submitBtn.disabled = false;
        }

    }

    const closedMessage = document.getElementById("cartClosedMessage");

    if (closedMessage) {
        closedMessage.style.display = isClosed ? "block" : "none";
    }

}

document.addEventListener("DOMContentLoaded", function () {

    updateStatusBadges();
    applyCartGating();

    // Keep the status (and cart gating) accurate if the page stays open
    // across the opening/closing time
    setInterval(function () {
        updateStatusBadges();
        applyCartGating();
    }, 60000);

});
