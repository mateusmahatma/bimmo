$(document).ready(function () {
    $('#pengeluaranTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: '/pengeluaran',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama', className: 'text-center' },
            { data: 'created_at', className: 'text-center' },
            { data: 'updated_at', className: 'text-center' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                className: "text-center",
            }

        ]
    });

    // Fungsi untuk menampilkan toast notification
    function showToast(message, type) {
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
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

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Function to handle Save & Update
    function simpanPengeluaran(id = '') {
        var url = id ? '/pengeluaran/' + id : '/pengeluaran';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#pengeluaranModal').on('shown.bs.modal', function () {
            $('#pengeluaranModal').off('click', '.tombol-simpan-pengeluaran').on('click', '.tombol-simpan-pengeluaran', function () {
                var formData = {
                    nama: $('#nama').val().trim()
                };

                if (formData.nama === '') {
                    showToast('Nama Harus Diisi!', 'danger');
                    return;
                }

                $('.tombol-simpan-pengeluaran').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function () {
                        showToast('Data Berhasil disimpan', 'success');
                        $('#pengeluaranModal').modal('hide');
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-pengeluaran').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#pengeluaranModal').on('hidden.bs.modal', function () {
                $('#nama').val('');
            });
        });
    }

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let isRequesting = false;

    // Handle Create Pengeluaran
    $('body').on('click', '.tombol-tambah-pengeluaran', function (e) {
        e.preventDefault();
        $('#pengeluaranModal').modal('show');
        simpanPengeluaran();
    });

    // Handle Edit Pengeluaran
    $('body').on('click', '.tombol-edit-pengeluaran', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/pengeluaran/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pengeluaranModal').modal('show');
                $('#nama').val(response.result.nama);
                simpanPengeluaran(id);
            }
        });
    });

    // Handle Delete Pengeluaran
    $('body').on('click', '.tombol-del-pengeluaran', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'dark-mode'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/pengeluaran/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data Berhasil dihapus', 'success');
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data Gagal dihapus', 'danger');
                        $('#pengeluaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const darkModeDropdown = document.getElementById('darkModeDropdown');

    const storedMode = localStorage.getItem('darkMode');
    const isDarkMode = storedMode === 'enabled';

    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.style.color = 'white';
        darkModeDropdown.value = 'dark';
    }

    darkModeDropdown.addEventListener('change', function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === 'dark') {
            enableDarkMode();
            darkModeDropdown.style.color = 'white';
            localStorage.setItem('darkMode', 'enabled');
        } else {
            disableDarkMode();
            darkModeDropdown.style.color = '';
            localStorage.setItem('darkMode', null);
        }
    });

    function enableDarkMode() {
        document.body.classList.add('dark-mode');
    }

    function disableDarkMode() {
        document.body.classList.remove('dark-mode');
    }
});
