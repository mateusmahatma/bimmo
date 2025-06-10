$(document).ready(function () {
    // Fungsi Toast
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
        <div id="${toastId}" class="toast align-items-center text-white border-0" style="background-color: ${bgColor};" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Inisialisasi
    const barangTable = $('#barangTable').DataTable({
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
            url: '/barang',
            type: 'GET',
            data: function (d) {
                d.status = $('select[name="status"]').val();
            },
            dataSrc: function (json) {
                if (!json.totalBarang) {
                    console.warn("totalBarang tidak ditemukan!");
                    $("#totalAset").text("Rp 0");
                    return json.data;
                }

                let total = parseInt(json.totalBarang.replace(/[^\d]/g, '')) || 0;
                $("#totalAset").text(total.toLocaleString("id-ID"));
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_barang', className: 'text-center' },
            { data: 'nama_toko', className: 'text-center' },
            {
                data: 'harga',
                className: 'text-center',
                render: function (data, type, row) {
                    const num = parseFloat(data);
                    const color = (row.status == '1') ? 'green' : 'red';
                    return !isNaN(num)
                        ? `<span style="color: ${color};">${num.toLocaleString("id-ID")}</span>`
                        : '<span style="color: red;">0</span>';
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    const badge = data.toString().trim() === '1'
                        ? { class: 'badge-success', text: 'Assets owned' }
                        : { class: 'badge-danger', text: 'Mortgaged Assets' };
                    return `<span class="badge ${badge.class}">${badge.text}</span>`;
                }
            },
            { data: 'created_at', render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi' }
        ]
    });

    // Filter
    $('select[name="status"]').on('change', function () {
        barangTable.ajax.reload();
    });

    // Utility Function
    function ambilFormDataBarang() {
        return {
            nama_barang: $('#nama_barang').val().trim(),
            status: $('#status_barang').val(),
            nama_toko: $('#nama_toko').val().trim(),
            harga: parseFloat($('#harga').val())
        };
    }

    function validasiFormBarang(data) {
        if (data.nama_barang === '') {
            showToast('Nama Barang Harus Diisi!', 'warning');
        }
        if (isNaN(data.harga) || data.harga <= 0) {
            showToast('Harga harus lebih dari 0!', 'warning');
        }
        return true;
    }

    function resetFormBarang() {
        $('#nama_barang').val('');
        $('#status_barang').val('');
        $('#nama_toko').val('');
        $('#harga').val('');
        $('#barangModal').removeData('id');
        $('#barangModalLabel').text('Add Asset');
        $('.tombol-simpan-barang').html('Save');
    }

    function spinnerButton() {
        return '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...';
    }

    function resetTombolSimpanBarang() {
        $('.tombol-simpan-barang').prop('disabled', false).html('Save');
    }

    function onSuccessSimpanBarang() {
        showToast('Data berhasil disimpan', 'success');
        $('#barangModal').modal('hide');
        $('#barangTable').DataTable().ajax.reload();
    }

    // Simpan atau Update
    function simpanBarangBaru() {
        const data = ambilFormDataBarang();
        if (!validasiFormBarang(data)) return;

        $('.tombol-simpan-barang').prop('disabled', true).html(spinnerButton());

        $.post('/barang', data)
            .done(onSuccessSimpanBarang)
            .always(resetTombolSimpanBarang);
    }

    function updateBarang(id) {
        const data = ambilFormDataBarang();
        if (!validasiFormBarang(data)) return;

        $('.tombol-simpan-barang').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/barang/' + id,
            type: 'PUT',
            data,
            success: onSuccessSimpanBarang,
            complete: resetTombolSimpanBarang
        });
    }

    // Event Handlers
    $('body').on('click', '.tombol-simpan-barang', function (e) {
        e.preventDefault();
        const id = $('#barangModal').data('id');
        id ? updateBarang(id) : simpanBarangBaru();
    });

    // Edit
    $('body').on('click', '.tombol-edit-barang', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.get('/barang/' + id + '/edit', function (res) {
            const barang = res.result;
            $('#barangModal').modal('show');
            $('#nama_barang').val(barang.nama_barang);
            $('#status_barang').val(barang.status);
            $('#nama_toko').val(barang.nama_toko);
            $('#harga').val(parseFloat(barang.harga));
            $('#barangModal').data('id', id);
            $('#barangModalLabel').text('Edit Asset');
            $('.tombol-simpan-barang').html('Update');
        });
    });

    $('#barangModal').on('hidden.bs.modal', resetFormBarang);

    // Delete
    $('body').on('click', '.tombol-del-barang', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure you want to delete this data?',
            html: 'Deleted data cannot be recovered!',
            showCancelButton: true,
            confirmButtonColor: '#012970',
            cancelButtonColor: '#DB504A',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'dark-mode'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/barang/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data berhasil dihapus', 'success');
                        $('#barangTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data gagal dihapus', 'danger');
                        $('#barangTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    // Ajax Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});