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

    const table = $('#danaDaruratTable').DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
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
            {
                data: 'id_dana_darurat',
                name: 'id_dana_darurat',
                orderable: false,
                searchable: false,
                className: 'align-middle text-center',
                render: function (data, type, row) {
                    return `<div class="form-check d-flex justify-content-center"><input class="form-check-input check-item" type="checkbox" value="${data}" style="cursor: pointer;"></div>`;
                }
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'align-middle text-center text-secondary fw-medium' },
            {
                data: 'tgl_transaksi_dana_darurat',
                className: 'align-middle text-center',
                render: function (data) {
                    return moment(data).format('YYYY-MM-DD');
                }
            },
            {
                data: 'jenis_transaksi_dana_darurat',
                className: 'align-middle text-center',
                name: 'jenis_transaksi_dana_darurat',
                render: function (data, type, row) {
                    if (data === 'Masuk') {
                        return '<span class="badge bg-success"><i class="bi bi-arrow-down-left me-1"></i>' + data + '</span>';
                    } else {
                        return '<span class="badge bg-danger"><i class="bi bi-arrow-up-right me-1"></i>' + data + '</span>';
                    }
                }
            },
            {
                data: 'nominal_dana_darurat',
                className: 'align-middle text-center fw-bold text-dark',
                render: function (data) {
                    return parseFloat(data).toLocaleString("id-ID", { style: "currency", currency: "IDR" });
                }
            },
            { data: 'keterangan', className: 'align-middle text-center' },
            {
                data: 'created_at',
                className: 'align-middle text-center text-muted small',
                render: function (data) {
                    return `<span style="font-family: 'Consolas', monospace;">${moment(data).format('YYYY-MM-DD HH:mm:ss')}</span>`;
                }
            },
            {
                data: 'updated_at',
                className: 'align-middle text-center text-muted small',
                render: function (data) {
                    return `<span style="font-family: 'Consolas', monospace;">${moment(data).format('YYYY-MM-DD HH:mm:ss')}</span>`;
                }
            },
            { data: 'aksi', className: 'align-middle text-center' }
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
            return false;
        }
        if (isNaN(data.nominal_dana_darurat) || data.nominal_dana_darurat === '' || parseFloat(data.nominal_dana_darurat) <= 0) {
            showToast('Nominal must be filled in and greater than 0!', 'warning');
            return false;
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
        table.ajax.reload();
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
                        table.ajax.reload();
                    },
                    error: function () {
                        showToast('Data gagal dihapus', 'danger');
                        table.ajax.reload();
                    }
                });
            }
        });
    });

    // ----------------------------------------------------------------
    // BULK DELETE LOGIC
    // ----------------------------------------------------------------

    // Check All
    $(document).on('change', '#checkAll', function () {
        const isChecked = $(this).is(':checked');
        $('.check-item').prop('checked', isChecked);
        updateBulkButton();
    });

    // Check Item
    $(document).on('change', '.check-item', function () {
        var total = $('.check-item').length;
        var checked = $('.check-item:checked').length;

        $('#checkAll').prop('checked', total === checked);
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        updateBulkButton();
    });

    // Update Button Visibility
    function updateBulkButton() {
        const checkedCount = $('.check-item:checked').length;
        $('#countSelected').text(checkedCount);
        if (checkedCount > 0) {
            $('#btnBulkDelete').removeClass('d-none');
        } else {
            $('#btnBulkDelete').addClass('d-none');
        }
    }

    // Handle Bulk Delete Click
    $('#btnBulkDelete').on('click', function () {
        const ids = [];
        $('.check-item:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} items?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete selected!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading on button
                const OriginalBtnText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm"></span> Deleting...').prop('disabled', true);

                $.ajax({
                    url: '/dana-darurat/bulk-delete',
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message, 'success');
                        table.ajax.reload();
                        $('#checkAll').prop('checked', false);
                        $('#btnBulkDelete').addClass('d-none').prop('disabled', false).html(OriginalBtnText);
                    },
                    error: function (xhr) {
                        showToast('Failed to delete selected data.', 'danger');
                        $('#btnBulkDelete').prop('disabled', false).html(OriginalBtnText);
                    }
                });
            }
        });
    });

    // Reset check all on page change
    table.on('draw', function () {
        $('#checkAll').prop('checked', false);
        $('#checkAll').prop('indeterminate', false);
        updateBulkButton();
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});