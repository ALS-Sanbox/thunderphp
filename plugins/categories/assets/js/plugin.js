document.addEventListener("DOMContentLoaded", function () {
    const categorySelect = document.getElementById("parentCategorySelect");
    if (categorySelect) {
        categorySelect.addEventListener("change", function () {
            window.location.href = "?parent_id=" + encodeURIComponent(this.value);
        });
    }

    function submitForm(action, extraFieldId = null) {
        let form = document.getElementById("categoryForm");
        if (!form) return;

        const actionInput = document.getElementById("action");
        if (actionInput) actionInput.value = action;

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

    const actions = [
        { btnId: "confirmAddCategoryBtn", action: "add", extraId: "add_category" },
        { btnId: "confirmUpdateCategoryBtn", action: "update" },
        { btnId: "confirmDeleteCategoryBtn", action: "delete" },
        { btnId: "confirmRenameCategoryBtn", action: "rename", extraId: "rename_category" },
    ];

    actions.forEach(({ btnId, action, extraId }) => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener("click", function () {
                submitForm(action, extraId);
            });
        }
    });

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    const categoryInput = document.getElementById('category');
    const slugInput = document.getElementById('slug');

    if (categoryInput && slugInput) {
        categoryInput.addEventListener('input', function () {
            if (!slugInput.dataset.manualEdit || slugInput.dataset.manualEdit === "false") {
                slugInput.value = slugify(this.value);
            }
        });

        slugInput.addEventListener('input', function () {
            this.dataset.manualEdit = "true";
        });
    }
});
