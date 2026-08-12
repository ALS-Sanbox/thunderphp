document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("roleSelect").addEventListener("change", function () {
        window.location.href = "?role_id=" + encodeURIComponent(this.value);
    });

    function submitForm(action, extraFieldId = null) {
        let form = document.getElementById("roleData");
        document.getElementById("action").value = action;

        if (extraFieldId) {
            let extraInput = document.getElementById(extraFieldId);
            if (extraInput) {
                let hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = extraInput.name;
                hiddenInput.value = extraInput.value;
                form.appendChild(hiddenInput);
            }
        }

        form.submit();
    }

    document.getElementById("confirmUpdateBtn")?.addEventListener("click", function () {
        submitForm("update");
    });

    document.getElementById("confirmRenameBtn")?.addEventListener("click", function () {
        submitForm("rename", "rename_role");
    });

    document.getElementById("confirmDeleteBtn")?.addEventListener("click", function () {
        submitForm("delete");
    });

    document.getElementById("confirmAddBtn")?.addEventListener("click", function () {
        submitForm("add", "add_role");
    });

    const permInputs = Array.from(document.querySelectorAll(".permission-input"));
    const permItems = Array.from(document.querySelectorAll(".permission-item"));
    const permGroups = Array.from(document.querySelectorAll(".permission-group"));
    const permCount = document.getElementById("permCheckedCount");

    function updatePermCount() {
        if (permCount) {
            permCount.textContent = permInputs.filter((i) => i.checked).length;
        }
    }

    permInputs.forEach((input) => input.addEventListener("change", updatePermCount));
    updatePermCount();

    document.getElementById("permissionSearch")?.addEventListener("input", function () {
        const term = this.value.trim().toLowerCase();

        permItems.forEach((item) => {
            const matches = !term || item.dataset.permName.includes(term);
            item.style.display = matches ? "" : "none";
        });

        permGroups.forEach((group) => {
            const anyVisible = Array.from(group.querySelectorAll(".permission-item")).some((item) => item.style.display !== "none");
            group.style.display = anyVisible ? "" : "none";
        });
    });

    document.getElementById("selectAllPerms")?.addEventListener("click", function () {
        permItems.forEach((item) => {
            if (item.style.display !== "none") {
                item.querySelector(".permission-input").checked = true;
            }
        });
        updatePermCount();
    });

    document.getElementById("clearAllPerms")?.addEventListener("click", function () {
        permItems.forEach((item) => {
            if (item.style.display !== "none") {
                item.querySelector(".permission-input").checked = false;
            }
        });
        updatePermCount();
    });

    document.querySelectorAll(".group-toggle-all").forEach((btn) => {
        btn.addEventListener("click", function () {
            const group = this.closest(".permission-group");
            const inputs = Array.from(group.querySelectorAll(".permission-input"));
            const allChecked = inputs.every((i) => i.checked);
            inputs.forEach((i) => { i.checked = !allChecked; });
            updatePermCount();
        });
    });
});
