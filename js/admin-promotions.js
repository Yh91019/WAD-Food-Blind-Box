document.addEventListener("DOMContentLoaded", function () {
    const activeTab = document.getElementById("activePromotionTab");
    const pastTab = document.getElementById("pastPromotionTab");
    const activePanel = document.getElementById("activePromotionPanel");
    const pastPanel = document.getElementById("pastPromotionPanel");

    if (!activeTab || !pastTab || !activePanel || !pastPanel) {
        return;
    }

    function showPromotionGroup(group) {
        const showActive = group === "active";

        activePanel.hidden = !showActive;
        pastPanel.hidden = showActive;
        activeTab.classList.toggle("is-active", showActive);
        pastTab.classList.toggle("is-active", !showActive);
        activeTab.setAttribute("aria-selected", String(showActive));
        pastTab.setAttribute("aria-selected", String(!showActive));
    }

    activeTab.addEventListener("click", function () {
        showPromotionGroup("active");
    });

    pastTab.addEventListener("click", function () {
        showPromotionGroup("past");
    });
});
