/* ===================== PROFILE EDIT TOGGLE ===================== */

document.addEventListener("DOMContentLoaded", function () {

    const profilePage  = document.querySelector(".profile-page");
    const viewSection  = document.getElementById("profile-view");
    const editSection  = document.getElementById("profile-edit");
    const editBtn      = document.getElementById("edit-profile-btn");
    const cancelBtn    = document.getElementById("cancel-edit-btn");

    if (!viewSection || !editSection) {
        return;
    }

    function showEdit() {
        viewSection.hidden = true;
        editSection.hidden = false;
    }

    function showView() {
        editSection.hidden = true;
        viewSection.hidden = false;
    }

    if (editBtn) {
        editBtn.addEventListener("click", showEdit);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", showView);
    }

    // Reopen the edit form automatically if the page was reloaded
    // after a failed submission (see data-show-edit on <main>)
    if (profilePage && profilePage.dataset.showEdit === "1") {
        showEdit();
    }

});
