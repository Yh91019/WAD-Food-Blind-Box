const pickupBtn = document.getElementById("pickupBtn");
const deliveryBtn = document.getElementById("deliveryBtn");
const orderTypeInput = document.getElementById("orderTypeInput");
const orderTypeMessage = document.getElementById("orderTypeMessage");
const placeOrderBtn = document.getElementById("openPaymentModalBtn");
const deliveryFeeAmount = document.getElementById("deliveryFeeAmount");
const grandTotal = document.getElementById("cartGrandTotal");
const cartSummary = document.querySelector(".cart-summary");
const discountedSubtotal = Number(cartSummary.dataset.discountedSubtotal);

function selectOrderType(type) {
    pickupBtn.classList.remove("selected");
    deliveryBtn.classList.remove("selected");

    if (type === "Pickup") {
        pickupBtn.classList.add("selected");
        deliveryFeeAmount.textContent = "RM 0.00";
        grandTotal.textContent = "RM " + discountedSubtotal.toFixed(2);
    } else {
        deliveryBtn.classList.add("selected");
        deliveryFeeAmount.textContent = "RM 5.00";
        grandTotal.textContent = "RM " + (discountedSubtotal + 5).toFixed(2);
    }

    orderTypeInput.value = type;
    orderTypeMessage.textContent = "Selected: " + type;
    orderTypeMessage.classList.remove("error-message");
    orderTypeMessage.classList.add("selected-message");
    placeOrderBtn.disabled = false;
}

function checkOrderType() {
    if (orderTypeInput.value === "") {
        orderTypeMessage.textContent = "Please select Pickup or Delivery before placing your order.";
        orderTypeMessage.classList.remove("selected-message");
        orderTypeMessage.classList.add("error-message");
        return false;
    }

    return true;
}

pickupBtn.addEventListener("click", function () {
    selectOrderType("Pickup");
});

deliveryBtn.addEventListener("click", function () {
    selectOrderType("Delivery");
});

placeOrderBtn.addEventListener("click", checkOrderType);

const claimedVoucherSelect = document.getElementById("claimedVoucherSelect");
const voucherCodeInput = document.getElementById("voucherCodeInput");

if (claimedVoucherSelect && voucherCodeInput) {
    claimedVoucherSelect.addEventListener("change", function () {
        voucherCodeInput.value = this.value;
    });
}
