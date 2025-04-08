function previewFile(inputId, previewId) {
    const fileInput = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        reader.readAsDataURL(file);
    }
}
