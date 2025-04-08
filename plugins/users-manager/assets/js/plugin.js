function previewFile() {
    const fileInput = document.getElementById('imageUpload');
    const preview = document.getElementById('previewImage');
    const currentImageInput = document.getElementById('currentImage');

    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
            currentImageInput.value = "";
        }

        reader.readAsDataURL(file);
    } else {
        preview.src = currentImageInput.value;
        }
}


function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}