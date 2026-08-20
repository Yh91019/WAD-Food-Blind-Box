document.addEventListener("DOMContentLoaded", function () {

    var track = document.getElementById("reviewTrack");

    if (!track) {
        return;
    }

    // How fast the carousel scrolls, in pixels per second. Kept as a
    // constant so the speed feels the same whether a restaurant has
    // 2 reviews or 20.
    var pixelsPerSecond = 40;

    function updateDuration() {

        // The track's cards are rendered twice (see details.php) so the
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
