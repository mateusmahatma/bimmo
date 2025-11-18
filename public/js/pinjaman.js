$(document).ready(function () {
    let table = $("#pinjamanTable").DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: "/pinjaman",
            type: "GET",
            dataSrc: function (json) {
                return json.data;
            },
            dataSrc: function (json) {
                $("#totalPinjaman").text(
                    json.totalPinjaman.toLocaleString("id-ID")
                );
                return json.data;
            },
            data: function (d) {
                // kirim filter ke controller
                d.filter_status = $('#filter_status').val();
            }
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
            {
                data: "nama_pinjaman",
                name: "nama_pinjaman",
            },
            {
                data: "jumlah_pinjaman",
                name: "jumlah_pinjaman",
            },
            {
                data: "status",
                name: "status",
                className: "text-center",
                render: function (data) {
                    if (data === "belum_lunas") {
                        return `
                        <span class="d-inline-flex align-items-center px-2 py-1 rounded small" style="background-color:#f8d7da; color:#721c24;">
                            <i class="bi bi-x-circle me-1"></i> Belum Lunas
                        </span>
                    `;
                    } else if (data === "lunas") {
                        return `
                        <span class="d-inline-flex align-items-center px-2 py-1 rounded small" style="background-color:#d4edda; color:#155724;">
                            <i class="bi bi-check-circle me-1"></i> Lunas
                        </span>
                    `;
                    }
                    return data ? data : '<span class="text-muted">-</span>';
                }
            },
            {
                data: "aksi",
                orderable: false,
                searchable: false,
            },
        ],
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

    // reload saat filter berubah
    $('#filter_status').on('change', function () {
        table.ajax.reload();
    });

    // Handle tombol edit & proses update
    $('body').on('click', '.tombol-edit-pinjaman', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/pinjaman/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                console.log(response); // Debugging
                $('#pinjamanModal').modal('show'); // Tampilkan modal

                // Isi form dengan data yang diambil
                $('#nama_pinjaman').val(response.result.nama_pinjaman);
                $('#jumlah_pinjaman').val(response.result.jumlah_pinjaman);
                $('#jangka_waktu').val(response.result.jangka_waktu);
                $('#start_date').val(response.result.start_date);
                $('#end_date').val(response.result.end_date);
                $('#status').val(response.result.status);

                // Simpan ID dalam modal untuk digunakan saat menyimpan
                $('#pinjamanModal').data('id', id);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    // Handle tombol simpan pinjaman
    $('body').on('click', '.tombol-simpan-pinjaman', function () {
        var id = $('#pinjamanModal').data('id'); // Ambil ID dari modal
        var url = id ? '/pinjaman/' + id : '/pinjaman'; // Jika ada ID -> Edit, jika tidak -> Tambah
        var type = id ? 'PUT' : 'POST';
        var toastMixin = Swal.mixin({
            toast: true,
            icon: 'success',
            title: 'General Title',
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
                icon: 'swal2-icon-success'
            }
        });

        var formData = {
            nama_pinjaman: $('#nama_pinjaman').val().trim(),
            jumlah_pinjaman: $('#jumlah_pinjaman').val().trim(),
            jangka_waktu: $('#jangka_waktu').val().trim(),
            start_date: $('#start_date').val().trim(),
            end_date: $('#end_date').val().trim(),
            status: $('#status').val().trim()
        };

        if (formData.nama_pinjaman === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Nama Pinjaman Harus Diisi!'
            });
            return;
        }

        // Tombol loading
        $('.tombol-simpan-pinjaman').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...');

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function () {
                toastMixin.fire({
                    animation: true,
                    title: 'Data Berhasil disimpan',
                    iconColor: '#012970',
                    customClass: {
                        title: 'swal2-title-create',
                        icon: 'swal2-icon-success',
                    }
                });

                $('#pinjamanModal').modal('hide'); // Tutup modal
                $('#pinjamanTable').DataTable().ajax.reload(); // Reload tabel
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan, coba lagi!'
                });
            },
            complete: function () {
                $('.tombol-simpan-pinjaman').prop('disabled', false).html('Simpan');
            }
        });
    });

    // Reset modal saat ditutup agar data lama tidak tertinggal
    $('#pinjamanModal').on('hidden.bs.modal', function () {
        $(this).removeData('id'); // Hapus ID agar tidak terbawa ke input berikutnya
        $('#nama_pinjaman, #jumlah_pinjaman, #jangka_waktu, #start_date, #end_date, #status').val('');
    });

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let isRequesting = false;

    // Handle Detail
    $(document).ready(function () {
        $("#bayarModal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            var pinjamanId = button.data("pinjaman-id");
            var modal = $(this);
            var form = modal.find("#bayarForm");
            form.attr("action", "/pinjaman/" + pinjamanId + "/bayar");
            modal.find("#pinjamanId").val(pinjamanId);
        });
    });

    // Handle Delete
    $("body").on("click", ".tombol-del-pinjaman", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Yakin mau hapus data ini?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            showCancelButton: true,
            confirmButtonColor: "var(--bs-danger)",
            cancelButtonColor: "var(--bs-primary)",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data("id");

                $.ajax({
                    url: "/pinjaman/" + id,
                    type: "DELETE",
                    success: function () {
                        showToast("Data berhasil dihapus", "success");
                        $("#pinjamanTable").DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast("Data gagal dihapus", "danger");
                        $("#pinjamanTable").DataTable().ajax.reload();
                    },
                });
            }
        });
    });
});