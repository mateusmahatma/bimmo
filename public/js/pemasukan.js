$(document).ready(function () {
    // Theme Handler
    const skin = window.userSkin || 'auto';
    const updateSkinUrl = window.updateSkinUrl;
    const csrfToken = window.csrfToken;

    function applyTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', mode);
        } else {
            document.documentElement.removeAttribute('data-bs-theme'); // auto
        }
        document.dispatchEvent(new Event("themeChanged"));
    }

    function highlightActiveSkin(mode) {
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') === `setTheme('${mode}')`) {
                el.classList.add('active');
            }
        });
    }

    function setTheme(mode) {
        applyTheme(mode);
        highlightActiveSkin(mode);

        fetch(updateSkinUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ skin: mode })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert("Gagal menyimpan tema.");
            })
            .catch(err => console.error("Gagal update tema:", err));
    }

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    const table = $('#pemasukanTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: '/pemasukan',
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
                render: function (data) {
                    return data;
                },
            }
        ]
    });

    // Toast Notification
    function showToast(message, type) {
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const colors = {
            success: '#012970',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8',
            primary: '#007bff',
        };
        const bgColor = colors[type] || '#6c757d';

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white border-0" style="background-color: ${bgColor};" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();
        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }

    // Submit Form
    function simpanPemasukan(id = '') {
        const url = id ? `/pemasukan/${id}` : '/pemasukan';
        const method = id ? 'PUT' : 'POST';
        const nama = $('#nama').val().trim();

        if (nama === '') {
            showToast('Nama Harus Diisi!', 'danger');
            return;
        }

        $('.tombol-simpan-pemasukan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Proses ...');

        $.ajax({
            url,
            type: method,
            data: { nama },
            success: () => {
                showToast('Data Berhasil disimpan', 'success');
                $('#pemasukanModal').modal('hide');
                table.ajax.reload();
            },
            complete: () => {
                $('.tombol-simpan-pemasukan').prop('disabled', false).html('Simpan');
            }
        });
    }

    // Tambah
    $('body').on('click', '.tombol-tambah-pemasukan', function (e) {
        e.preventDefault();
        $('#nama').val('');
        $('#pemasukanModal').modal('show');

        $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan')
            .on('click', '.tombol-simpan-pemasukan', () => simpanPemasukan());
    });

    // Edit
    $('body').on('click', '.tombol-edit-pemasukan', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.get(`/pemasukan/${id}/edit`, function (response) {
            $('#nama').val(response.result.nama);
            $('#pemasukanModal').modal('show');

            $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan')
                .on('click', '.tombol-simpan-pemasukan', () => simpanPemasukan(id));
        });
    });

    // Hapus
    $('body').on('click', '.tombol-del-pemasukan', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'dark-mode' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/pemasukan/${id}`,
                    type: 'DELETE',
                    success: () => {
                        showToast('Data Berhasil dihapus', 'success');
                        table.ajax.reload();
                    },
                    error: () => {
                        showToast('Delete failed, data in cash flow', 'danger');
                        table.ajax.reload();
                    }
                });
            }
        });
    });

    // CSRF Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});