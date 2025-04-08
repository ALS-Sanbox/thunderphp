function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function() {
      const output = document.getElementById('profilePicPreview');
      output.src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}

if (document.getElementById("signupPage")) {
  document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const form = document.querySelector("form");

    confirmPassword.addEventListener("keyup", function () {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords do not match!");
        } else {
            confirmPassword.setCustomValidity("");
        }
    });

    form.addEventListener("submit", function (event) {
        if (password.value !== confirmPassword.value) {
            event.preventDefault(); // Prevent form submission
            alert("Passwords do not match!");
        }
    });
  });
}