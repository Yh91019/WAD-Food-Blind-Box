document.addEventListener("DOMContentLoaded", function () {

    var track = document.getElementById("homeRestaurantTrack");

    if (!track) {
        return;
    }

    // How fast the carousel scrolls, in pixels per second. Kept as a
    // constant so the speed feels the same whether there are 2 or 5
    // top-rated restaurants.
    var pixelsPerSecond = 40;

    function updateDuration() {

        // The track's cards are rendered twice (see index.php) so the
        // loop can wrap seamlessly, so one full pass is half the track's
        // total width.
        var distance = track.scrollWidth / 2;

        if (distance > 0) {
            track.style.animationDuration = (distance / pixelsPerSecond) + "s";
        }

    }

    updateDuration();

    window.addEventListener("resize", updateDuration);

});
