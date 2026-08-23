/* ===================== PROFILE EDIT TOGGLE ===================== */

document.addEventListener("DOMContentLoaded", function () {

    const profilePage  = document.querySelector(".profile-page");
    const viewSection  = document.getElementById("profile-view");
    const editSection  = document.getElementById("profile-edit");
    const editBtn      = document.getElementById("edit-profile-btn");
    const cancelBtn    = document.getElementById("cancel-edit-btn");
    const voucherCard  = document.getElementById("profile-vouchers");
    const activeVoucherTab = document.getElementById("activeVoucherTab");
    const pastVoucherTab   = document.getElementById("pastVoucherTab");
    const activeVoucherPanel = document.getElementById("activeVoucherPanel");
    const pastVoucherPanel   = document.getElementById("pastVoucherPanel");

    if (!viewSection || !editSection) {
        return;
    }

    function showEdit() {
        viewSection.hidden = true;
        if (voucherCard) voucherCard.hidden = true;
        editSection.hidden = false;
    }

    function showView() {
        editSection.hidden = true;
        viewSection.hidden = false;
        if (voucherCard) voucherCard.hidden = false;
    }

    if (editBtn) {
        editBtn.addEventListener("click", showEdit);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", showView);
    }

    function showVoucherPanel(selectedPanel) {
        const showActive = selectedPanel === "active";

        activeVoucherPanel.hidden = !showActive;
        pastVoucherPanel.hidden = showActive;
        activeVoucherTab.classList.toggle("is-active", showActive);
        pastVoucherTab.classList.toggle("is-active", !showActive);
        activeVoucherTab.setAttribute("aria-selected", String(showActive));
        pastVoucherTab.setAttribute("aria-selected", String(!showActive));
    }

    if (activeVoucherTab && pastVoucherTab && activeVoucherPanel && pastVoucherPanel) {
        activeVoucherTab.addEventListener("click", function () {
            showVoucherPanel("active");
        });

        pastVoucherTab.addEventListener("click", function () {
            showVoucherPanel("past");
        });
    }

    // Reopen the edit form automatically if the page was reloaded
    // after a failed submission (see data-show-edit on <main>)
    if (profilePage && profilePage.dataset.showEdit === "1") {
        showEdit();
    }

});
