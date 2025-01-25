// Show Password
const passwordField = document.getElementById("password");
const togglePassword = document.querySelector(".password-toggle-icon i");

togglePassword.addEventListener("click", function () {
    if (passwordField.type === "password") {
        passwordField.type = "text";
        togglePassword.classList.remove("fa-eye-slash");
        togglePassword.classList.add("fa-eye");
    } else {
        passwordField.type = "password";
        togglePassword.classList.remove("fa-eye");
        togglePassword.classList.add("fa-eye-slash");
    }
});

if (sessionData.gagal_nama) {
    swal.fire({
        icon: 'warning',
        title: 'Maaf',
        text: 'Nama Sudah Terdaftar !'
    });
}

if (sessionData.gagal_email) {
    swal.fire({
        icon: 'warning',
        title: 'Maaf',
        text: 'Email Sudah Terdaftar !'
    });
}

$(document).ready(function () {
    $('#openModal').on('show.bs.modal', function (event) {
    });
});