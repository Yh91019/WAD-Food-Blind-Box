document.addEventListener("DOMContentLoaded", function () {

    var track = document.getElementById("reviewTrack");

    if (!track) {
        return;
    }

    // Keep the upward speed consistent regardless of review count.
    var pixelsPerSecond = 22;

    function updateDuration() {

        // Reviews are duplicated once so half the vertical track is one loop.
        var distance = track.scrollHeight / 2;

        if (distance > 0) {
            track.style.animationDuration = (distance / pixelsPerSecond) + "s";
        }

    }

    updateDuration();

    window.addEventListener("resize", updateDuration);

});
