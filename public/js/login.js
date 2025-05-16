// Handle Show Password
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

// Handle Tombol Log in
$('body').on('click', '.tombol-login', function (e) {
    e.preventDefault();
    $('.tombol-login').prop('disabled', true);
    $('.tombol-login').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');
    var formData = {
        username: $('#username').val(),
        password: $('#password').val(),
    };

    $.ajax({
        url: '/bimmo',
        type: 'post',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('.tombol-login').prop('disabled', false);
            $('.tombol-login').html('Masuk');
            if (response.success) {
                showToast(response.message, 'success'); // Pesan dari controller
                window.location.href = '/dashboard';
            } else {
                showToast(response.message, 'danger'); // Pesan dari controller
            }
        },
    });
});

// Fungsi untuk menampilkan toast notification
function showToast(message, type) {
    // Buat container toast jika belum ada
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    // Buat ID unik untuk toast
    const toastId = 'toast-' + Date.now();

    // Buat elemen toast
    const colors = {
        success: '#012970',  // Biru
        danger: '#dc3545',   // Merah
        warning: '#ffc107',  // Kuning
        info: '#17a2b8',     // Biru muda
        primary: '#007bff',  // Biru
    };

    const bgColor = colors[type] || '#6c757d'; // Default ke abu-abu jika tipe tidak ada

    const toastHtml = `
    <div id="${toastId}" class="toast align-items-center text-white border-0" style="background-color: ${bgColor};" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
`;

    // Tambahkan toast ke container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    // Inisialisasi dan tampilkan toast
    const toastElement = document.getElementById(toastId);

    // Cek apakah Bootstrap 5 tersedia
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
    } else {
        // Fallback jika Bootstrap Toast tidak tersedia
        toastElement.classList.add('show');
        setTimeout(() => {
            toastElement.classList.remove('show');
            setTimeout(() => {
                toastElement.remove();
            }, 300);
        }, 5000);
    }

    // Hapus toast dari DOM setelah dihide
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}
