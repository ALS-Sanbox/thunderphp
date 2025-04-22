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

    document.getElementById("confirmUpdateBtn").addEventListener("click", function () {
        submitForm("update");
    });
    
    document.getElementById("confirmRenameBtn").addEventListener("click", function () {
        submitForm("rename", "rename_role");
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        submitForm("delete");
    });

    document.getElementById("confirmAddBtn").addEventListener("click", function () {
        submitForm("add", "add_role");
    });
});
