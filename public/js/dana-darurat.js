let isDanaDaruratInitialized = false;

window.initDanaDarurat = function () {
    if (!$('#danaDaruratTable').length) return;

    if (isDanaDaruratInitialized) return;
    isDanaDaruratInitialized = true;

    // reset the flag after a short delay so that future SPA navigations can trigger it
    setTimeout(() => { isDanaDaruratInitialized = false; }, 500);

    // Check if DataTable is already initialized to avoid re-init error
    if ($.fn.DataTable.isDataTable('#danaDaruratTable')) {
        $('#danaDaruratTable').DataTable().destroy();
    }

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        ajax: {
            url: '/dana-darurat',
            type: 'GET',
            dataSrc: function (json) {
                // Debug log
                console.log('Emergency Fund AJAX Response:', json);

                // Update Total Balance
                const total = parseFloat(json.totalDanaDarurat);
                if (!isNaN(total)) {
                    $("#totalDanaDarurat").text(
                        total.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0
                        })
                    );
                }

                // Update Percentage Text
                const percentage = parseFloat(json.percentage);
                if (!isNaN(percentage)) {
                    $("#targetPercentage").text(percentage);
                    console.log('Updated percentage text to:', percentage);
                }

                // Update Progress Bar
                if (!isNaN(percentage)) {
                    const progressBar = $("#progressBar");
                    if (progressBar.length) {
                        progressBar.css("width", percentage + "%");
                        progressBar.attr("aria-valuenow", percentage);
                        console.log('Updated progressBar width to:', percentage + "%");
                    } else {
                        console.warn('Element #progressBar not found!');
                    }
                }

                // Update Target Amount
                const target = parseFloat(json.targetDanaDarurat);
                if (!isNaN(target)) {
                    $("#targetDanaDaruratAmount").text(
                        target.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0
                        })
                    );
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
                        return '<span class="badge bg-success"><i class="bi bi-arrow-down-left me-1"></i>Simpanan</span>';
                    } else if (data === 'Keluar') {
                        return '<span class="badge bg-danger"><i class="bi bi-arrow-up-right me-1"></i>Penarikan</span>';
                    }
                    return data;
                }
            },
            {
                data: 'nominal_dana_darurat',
                className: 'align-middle text-center fw-bold text-dark',
                render: function (data) {
                    return parseFloat(data).toLocaleString("id-ID", { style: "currency", currency: "IDR" });
                }
            },
            { 
                data: 'keterangan', 
                className: 'align-middle text-start',
                render: function (data) {
                    if (!data || data.trim() === '') return '-';
                    // Render HTML wrapped in a div to control its sizing, useful for rich text
                    return `<div class="keterangan-content ck-content">${data}</div>`;
                }
            },
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
            showToast('Tanggal transaksi harus diisi!', 'warning');
            return false;
        }
        if (isNaN(data.nominal_dana_darurat) || data.nominal_dana_darurat === '' || parseFloat(data.nominal_dana_darurat) <= 0) {
            showToast('Nominal harus diisi dan lebih besar dari 0!', 'warning');
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
        $('#danaDaruratModalLabel').text('Tambah Dana Darurat');
        $('.tombol-simpan-dana-darurat').html('Simpan');
    }

    function spinnerButton() {
        return '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses ...';
    }

    function resetTombolSimpanDanaDarurat() {
        $('.tombol-simpan-dana-darurat').prop('disabled', false).html('Simpan');
    }

    function onSuccessSimpanDanaDarurat() {
        showToast('Data berhasil disimpan', 'success');
        $('#danaDaruratModal').modal('hide');
        table.ajax.reload();
    }

    // Simpan atau Update
    function simpanDanaDaruratBaru() {
        const data = ambilFormDataDanaDarurat();
        if (!validasiFormDanaDarurat(data)) return;

        $('.tombol-simpan-dana-darurat').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/dana-darurat',
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: onSuccessSimpanDanaDarurat,
            complete: resetTombolSimpanDanaDarurat
        });
    }

    function updateDanaDarurat(id) {
        const data = ambilFormDataDanaDarurat();
        if (!validasiFormDanaDarurat(data)) return;

        $('.tombol-simpan-dana-darurat').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/dana-darurat/' + id,
            type: 'PUT',
            data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: onSuccessSimpanDanaDarurat,
            complete: resetTombolSimpanDanaDarurat
        });
    }

    // Event Handlers
    $('.tombol-simpan-dana-darurat').off('click').on('click', function (e) {
        e.preventDefault();
        const id = $('#danaDaruratModal').data('id');
        id ? updateDanaDarurat(id) : simpanDanaDaruratBaru();
    });

    // Edit
    $('body').off('click', '.tombol-edit-dana-darurat').on('click', '.tombol-edit-dana-darurat', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.ajax({
            url: '/dana-darurat/' + id + '/edit',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                const danaDarurat = res.result;
                $('#danaDaruratModal').modal('show');
                $('#tgl_transaksi_dana_darurat').val(danaDarurat.tgl_transaksi_dana_darurat);
                $('#jenis_transaksi_dana_darurat').val(danaDarurat.jenis_transaksi_dana_darurat);
                $('#nominal_dana_darurat').val(parseFloat(danaDarurat.nominal_dana_darurat));
                $('#keterangan').val(danaDarurat.keterangan);
                $('#danaDaruratModal').data('id', id);
                $('#danaDaruratModalLabel').text('Edit Dana Darurat');
                $('.tombol-simpan-dana-darurat').html('Perbarui');
            }
        });
    });

    $('#danaDaruratModal').off('hidden.bs.modal').on('hidden.bs.modal', resetFormDanaDarurat);

    // Delete
    $('body').off('click', '.tombol-del-dana-darurat').on('click', '.tombol-del-dana-darurat', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        window.confirmAction({
            title: 'Are you sure?',
            text: 'Deleted data cannot be recovered!',
            onConfirm: async () => {
                try {
                    await $.ajax({
                        url: '/dana-darurat/' + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });
                    showToast('Data processed successfully', 'success');
                    table.ajax.reload();
                } catch (e) {
                    showToast('Failed to delete data', 'danger');
                    table.ajax.reload();
                }
            }
        });
    });

    // ----------------------------------------------------------------
    // BULK DELETE LOGIC
    // ----------------------------------------------------------------

    // Check All
    $(document).off('change', '#checkAll').on('change', '#checkAll', function () {
        const isChecked = $(this).is(':checked');
        $('.check-item').prop('checked', isChecked);
        updateBulkButton();
    });

    // Check Item
    $(document).off('change', '.check-item').on('change', '.check-item', function () {
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
    $('#btnBulkDelete').off('click').on('click', function () {
        const ids = [];
        $('.check-item:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        window.confirmAction({
            title: `Delete ${ids.length} items?`,
            text: 'Deleted data cannot be recovered!',
            onConfirm: async () => {
                const OriginalBtnText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm"></span> Deleting...').prop('disabled', true);

                try {
                    const response = await $.ajax({
                        url: '/dana-darurat/bulk-delete',
                        type: 'DELETE',
                        data: { ids: ids },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });
                    showToast(response.message || 'Data processed successfully', 'success');
                    table.ajax.reload();
                    $('#checkAll').prop('checked', false);
                    $('#btnBulkDelete').addClass('d-none').prop('disabled', false).html(OriginalBtnText);
                } catch (xhr) {
                    showToast('Failed to delete selected data.', 'danger');
                    $('#btnBulkDelete').prop('disabled', false).html(OriginalBtnText);
                }
            }
        });
    });

    // Reset check all on page change
    table.on('draw', function () {
        $('#checkAll').prop('checked', false);
        $('#checkAll').prop('indeterminate', false);
        updateBulkButton();
    });
}

// Initial initialization
$(document).ready(function () {
    window.initDanaDarurat();
});

document.addEventListener('livewire:navigated', function () {
    window.initDanaDarurat();
});

