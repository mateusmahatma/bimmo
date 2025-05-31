$(document).ready(function () {
    $('#pemasukanTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        language: {
            processing: '<div class="loader-container"><div class="loader"></div></div>'
        },
        ajax: {
            url: '/pemasukan',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama', className: 'text-center' },
            { data: 'created_at', render: data => moment(data).format('D MMMM YYYY HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('D MMMM YYYY HH:mm:ss'), className: 'text-center' },
            {
                data: 'aksi', rderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return data;
                },
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
    function simpanPemasukan(id = '') {
        var url = id ? '/pemasukan/' + id : '/pemasukan';
        var type = id ? 'PUT' : 'POST';

        isRequesting = true;

        $('#pemasukanModal').on('shown.bs.modal', function () {
            $('#pemasukanModal').off('click', '.tombol-simpan-pemasukan').on('click', '.tombol-simpan-pemasukan', function () {
                var formData = {
                    nama: $('#nama').val().trim()
                };

                if (formData.nama === '') {
                    showToast('Nama Harus Diisi!', 'danger');
                    return;
                }

                $('.tombol-simpan-pemasukan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function () {
                        showToast('Data Berhasil disimpan', 'success');
                        $('#pemasukanModal').modal('hide');
                        $('#pemasukanTable').DataTable().ajax.reload();
                    },
                    complete: function () {
                        $('.tombol-simpan-pemasukan').prop('disabled', false).html('Simpan');
                    }
                });
            });

            $('#pemasukanModal').on('hidden.bs.modal', function () {
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

    // Handle Create Pemasukan
    $('body').on('click', '.tombol-tambah-pemasukan', function (e) {
        e.preventDefault();
        $('#pemasukanModal').modal('show');
        simpanPemasukan();
    });

    // Handle Edit Pemasukan
    $('body').on('click', '.tombol-edit-pemasukan', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/pemasukan/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pemasukanModal').modal('show');
                $('#nama').val(response.result.nama);
                simpanPemasukan(id);
            }
        });
    });

    // Handle Delete Pemasukan
    $('body').on('click', '.tombol-del-pemasukan', function (e) {
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
                    url: '/pemasukan/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data Berhasil dihapus', 'success');
                        $('#pemasukanTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Delete failed, data in cash flow', 'danger');
                        $('#pemasukanTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
