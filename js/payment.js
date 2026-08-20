document.addEventListener("DOMContentLoaded", function () {

    var openBtn = document.getElementById("openPaymentModalBtn");
    var overlay = document.getElementById("paymentModalOverlay");
    var cancelBtn = document.getElementById("cancelPaymentBtn");
    var methodInput = document.getElementById("paymentMethodInput");
    var form = document.getElementById("placeOrderForm");
    var methodButtons = document.querySelectorAll(".payment-method-btn");

    if (!openBtn || !overlay || !form) {
        return;
    }

    /* Open modal when "Place Order" is clicked */
    openBtn.onclick = function () {
        overlay.classList.add("active");
    };

    /* Close modal on Cancel */
    cancelBtn.onclick = function () {
        overlay.classList.remove("active");
    };

    /* Close modal when clicking outside the box */
    overlay.onclick = function (event) {
        if (event.target === overlay) {
            overlay.classList.remove("active");
        }
    };

    /* Select a payment method and submit the order */
    methodButtons.forEach(function (button) {

        button.onclick = function () {

            methodInput.value = button.getAttribute("data-method");

            var redirectUrl = button.getAttribute("data-redirect");

            if (redirectUrl) {
                window.location.href = redirectUrl;
                return;
            }

            form.submit();

        };

    });

});
