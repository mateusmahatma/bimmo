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

// Alert
$('body').on('click', '.tombol-login', function (e) {
    e.preventDefault();
    $('.tombol-login').prop('disabled', true);
    $('.tombol-login').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
    var formData = {
        username: $('#username').val(),
        password: $('#password').val(),
    };

    $.ajax({
        url: '/pointech',
        type: 'post',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('.tombol-login').prop('disabled', false);
            $('.tombol-login').html('Masuk');
            if (response.success) {
                var toastMixin = Swal.mixin({
                    toast: true,
                    icon: 'success',
                    title: 'Berhasil Masuk',
                    animation: false,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: false,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    },
                    customClass: {
                        title: 'swal2-title-create',
                        popup: 'swal2-popup-create',
                    },
                    iconColor: '#ffffff'
                });
                toastMixin.fire({
                    animation: true,
                    title: 'Berhasil Masuk'
                });
                window.location.href = '/dashboard';
            } else {
                $('.tombol-login').prop('disabled', false);
                $('.tombol-login').html('Masuk');
                $('.tombol-login .spinner-border').remove();
                var toastFailedLogin = Swal.mixin({
                    toast: true,
                    icon: 'error',
                    title: 'Gagal Masuk',
                    animation: false,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: false,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    },
                });
                toastFailedLogin.fire({
                    animation: true,
                    title: 'Gagal Login',
                    customClass: {
                        title: 'swal2-title-create',
                        popup: 'swal2-popup-danger',
                    },
                    iconColor: '#ffffff'
                });
            }
        },
    });
});