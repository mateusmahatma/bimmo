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

    $('#danaDaruratTable').DataTable({
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
            url: '/dana-darurat',
            type: 'GET',
            dataSrc: function (json) {
                const total = parseFloat(json.totalDanaDarurat);
                if (!isNaN(total)) {
                    $("#totalDanaDarurat").text(
                        total.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 2
                        })
                    );
                } else {
                    $("#totalDanaDarurat").text("Rp 0,00");
                }

                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'tgl_transaksi_dana_darurat', className: 'text-center', render: data => moment(data).format('YYYY-MM-DD') },
            {
                data: 'jenis_transaksi_dana_darurat', className: 'text-center', name: 'jenis_transaksi_dana_darurat', render: function (data, type, row) {
                    if (data === 'Masuk') {
                        return '<span style="color:green; font-weight:bold;">' + data + '</span>';
                    } else {
                        return '<span style="color:red; font-weight:bold;">' + data + '</span>';
                    }
                }
            },
            { data: 'nominal_dana_darurat', className: 'text-center', render: data => parseFloat(data).toLocaleString("id-ID") },
            { data: 'keterangan', className: 'text-center' },
            { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi', className: 'text-center' }
        ]
    });

    // Utility Function
    function ambilFormDataDanaDarurat() {
        const nominalRaw = $('#nominal_dana_darurat').val().trim();
        const nominalFormatted = nominalRaw === '' ? '0.00' : parseFloat(nominalRaw).toFixed(2);

        return {
            tgl_transaksi_dana_darurat: $('#tgl_transaksi_dana_darurat').val().trim(),
            jenis_transaksi_dana_darurat: $('#jenis_transaksi_dana_darurat').val().trim(),
            nominal_dana_darurat: nominalFormatted,
            keterangan: $('#keterangan').val().trim(),
        };
    }

    function validasiFormDanaDarurat(data) {
        if (data.tgl_transaksi_dana_darurat === '') {
            showToast('Transaction Date Must Be Filled In!', 'warning');
        }
        if (isNaN(data.nominal_dana_darurat) || data.nominal_dana_darurat === '') {
            showToast('Nominal must be filled in!', 'warning');
        }
        return true;
    }

    function resetFormDanaDarurat() {
        $('#tgl_transaksi_dana_darurat').val('');
        $('#jenis_transaksi_dana_darurat').val('');
        $('#nominal_dana_darurat').val('');
        $('#keterangan').val('');
        $('#danaDaruratModal').removeData('id');
        $('#danaDaruratModalLabel').text('Add Emergency Fund');
        $('.tombol-simpan-dana-darurat').html('Save');
    }

    function spinnerButton() {
        return '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Process ...';
    }

    function resetTombolSimpanDanaDarurat() {
        $('.tombol-simpan-dana-darurat').prop('disabled', false).html('Save');
    }

    function onSuccessSimpanDanaDarurat() {
        showToast('Data saved successfully', 'success');
        $('#danaDaruratModal').modal('hide');
        $('#danaDaruratTable').DataTable().ajax.reload();
    }

    // Simpan atau Update
    function simpanDanaDaruratBaru() {
        const data = ambilFormDataDanaDarurat();
        if (!validasiFormDanaDarurat(data)) return;

        $('.tombol-simpan-dana-darurat').prop('disabled', true).html(spinnerButton());

        $.post('/dana-darurat', data)
            .done(onSuccessSimpanDanaDarurat)
            .always(resetTombolSimpanDanaDarurat);
    }

    function updateDanaDarurat(id) {
        const data = ambilFormDataDanaDarurat();
        if (!validasiFormDanaDarurat(data)) return;

        $('.tombol-simpan-dana-darurat').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/dana-darurat/' + id,
            type: 'PUT',
            data,
            success: onSuccessSimpanDanaDarurat,
            complete: resetTombolSimpanDanaDarurat
        });
    }

    // Event Handlers
    $('body').on('click', '.tombol-simpan-dana-darurat', function (e) {
        e.preventDefault();
        const id = $('#danaDaruratModal').data('id');
        id ? updateDanaDarurat(id) : simpanDanaDaruratBaru();
    });

    // Edit
    $('body').on('click', '.tombol-edit-dana-darurat', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.get('/dana-darurat/' + id + '/edit', function (res) {
            const danaDarurat = res.result;
            $('#danaDaruratModal').modal('show');
            $('#tgl_transaksi_dana_darurat').val(danaDarurat.tgl_transaksi_dana_darurat);
            $('#jenis_transaksi_dana_darurat').val(danaDarurat.jenis_transaksi_dana_darurat);
            $('#nominal_dana_darurat').val(parseFloat(danaDarurat.nominal_dana_darurat));
            $('#keterangan').val(danaDarurat.keterangan);
            $('#danaDaruratModal').data('id', id);
            $('#danaDaruratModalLabel').text('Edit Emergency Fund');
            $('.tombol-simpan-dana-darurat').html('Update');
        });
    });

    $('#danaDaruratModal').on('hidden.bs.modal', resetFormDanaDarurat);

    // Delete
    $('body').on('click', '.tombol-del-dana-darurat', function (e) {
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
                    url: '/dana-darurat/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data berhasil dihapus', 'success');
                        $('#danaDaruratTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data gagal dihapus', 'danger');
                        $('#danaDaruratTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});