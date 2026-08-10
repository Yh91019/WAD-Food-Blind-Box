/* ===================== RESTAURANT OPEN/CLOSED STATUS ===================== */
/*
   Each restaurant only has a single "opening hours" value (no closing
   time in the database), so a restaurant is treated as OPEN once the
   current time on the visitor's own computer has passed that opening
   time (until midnight), and CLOSED before it.
*/

function parseTimeToMinutes(timeString) {

    // Expected format from the database: "HH:MM:SS" (or "HH:MM")
    const parts = timeString.split(":");

    const hours   = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);

    if (isNaN(hours) || isNaN(minutes)) {
        return null;
    }

    return (hours * 60) + minutes;
}

function updateStatusBadges() {

    const badges = document.querySelectorAll(".status-badge");
    const now = new Date();
    const nowMinutes = (now.getHours() * 60) + now.getMinutes();

    badges.forEach(function (badge) {

        const openingMinutes = parseTimeToMinutes(badge.dataset.opening || "");

        if (openingMinutes === null) {
            badge.textContent = "Unknown";
            badge.classList.remove("status-open", "status-closed");
            return;
        }

        const isOpen = nowMinutes >= openingMinutes;

        badge.textContent = isOpen ? "Open" : "Closed";
        badge.classList.toggle("status-open", isOpen);
        badge.classList.toggle("status-closed", !isOpen);

    });
}

document.addEventListener("DOMContentLoaded", function () {

    updateStatusBadges();

    // Keep the status accurate if the page stays open across the opening time
    setInterval(updateStatusBadges, 60000);

});
