document.addEventListener("DOMContentLoaded", function () {

    var input = document.getElementById("quantityInput");
    var minusBtn = document.querySelector(".qty-minus");
    var plusBtn = document.querySelector(".qty-plus");

    if (!input || !minusBtn || !plusBtn) {
        return;
    }

    var min = parseInt(input.min, 10) || 1;
    var max = parseInt(input.max, 10);

    if (!max || max < min) {
        max = 99;
    }

    function updateButtonStates() {

        var value = parseInt(input.value, 10);

        minusBtn.disabled = value <= min;
        plusBtn.disabled = value >= max;

    }

    minusBtn.onclick = function () {

        var value = parseInt(input.value, 10) || min;

        if (value > min) {
            input.value = value - 1;
        }

        updateButtonStates();

    };

    plusBtn.onclick = function () {

        var value = parseInt(input.value, 10) || min;

        if (value < max) {
            input.value = value + 1;
        }

        updateButtonStates();

    };

    updateButtonStates();

});