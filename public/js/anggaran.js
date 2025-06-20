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
                    <div class="toast-body">${message}</div>
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

    // Inisialisasi Select
    new TomSelect("#id_pengeluaran", {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        maxItems: null,
        placeholder: 'Select Expense Type',
    });

    // DataTable
    const anggaranTable = $('#anggaranTable').DataTable({
        paging: true,
        responsive: true,
        serverSide: true,
        processing: true,
        lengthChange: true,
        autoWidth: false,
        language: {
            processing: '<div class="loader-container"><div class="loader"></div></div>'
        },
        ajax: {
            url: '/anggaran',
            type: 'GET',
            dataSrc: function (json) {
                $('#totalPersentase').text(json.totalPersentase.toLocaleString('id-ID'));
                if (json.exceedMessage) {
                    $('#exceedMessage').text(json.exceedMessage).show();
                } else {
                    $('#exceedMessage').hide();
                }
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
            { data: 'nama_anggaran', className: 'text-center', render: d => d || '-' },
            { data: 'persentase_anggaran', className: 'text-center' },
            {
                data: 'nama_pengeluaran',
                className: 'text-left',
                defaultContent: '-',
                render: function (data, type, row) {
                    if (type !== "display" || !data || typeof data !== "string") {
                        return data ? data : "-";
                    }

                    var lines = data.split(",");
                    var showLimit = 3;
                    var hasMore = lines.length > showLimit;
                    var visibleLines = lines.slice(0, showLimit);
                    var hiddenLines = lines.slice(showLimit);

                    var tableId = `detail-table-${row.id || Math.random().toString(36).substring(7)}`;

                    var table = `
            <table style="width: 100%; border-collapse: collapse;" id="${tableId}">
                <colgroup>
                    <col style="width: 30px;">
                    <col>
                </colgroup>
                ${visibleLines.map((line, index) => `
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${index + 1}</td>
                        <td style="border: 1px solid #ddd; padding: 4px;">${line.trim()}</td>
                    </tr>
                `).join('')}
        `;

                    if (hasMore) {
                        table += hiddenLines.map((line, index) => `
                <tr class="hidden-row" style="display: none;">
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${showLimit + index + 1}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">${line.trim()}</td>
                </tr>
            `).join('');
                    }

                    table += `</table>`;

                    if (hasMore) {
                        table += `
                <button type="button" class="btn btn-link toggle-btn" data-target="${tableId}">
                    More Details
                </button>
            `;
                    }
                    return table;
                }
            },
            { data: 'created_at', render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: d => moment(d).format('YYYY-MM-DD HH:mm:ss'), className: 'text-center' },
            { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '.toggle-btn', function () {
        const tableId = $(this).data('target');
        const $table = $('#' + tableId);
        const $hiddenRows = $table.find('.hidden-row');

        if ($hiddenRows.is(':visible')) {
            $hiddenRows.hide();
            $(this).text('More Details');
        } else {
            $hiddenRows.show();
            $(this).text('Show Less');
        }
    });

    // Utility Functions
    function ambilFormDataAnggaran() {
        return {
            nama_anggaran: $('#nama_anggaran').val().trim(),
            persentase_anggaran: $('#persentase_anggaran').val().trim(),
            id_pengeluaran: $('#id_pengeluaran').val()
        };
    }

    function validasiFormAnggaran(data) {
        if (!data.nama_anggaran) {
            showToast('The budget name must be filled in!', 'danger');
            return false;
        }
        if (!data.persentase_anggaran) {
            showToast('The percentage must be filled in!', 'danger');
            return false;
        }
        return true;
    }

    function resetFormAnggaran() {
        $('#nama_anggaran').val('');
        $('#persentase_anggaran').val('');
        $('#id_pengeluaran')[0].tomselect.clear();
        $('#anggaranModal').removeData('id');
        $('#anggaranModalLabel').text('Add Budget');
        $('.tombol-simpan-anggaran').html('Save');
    }

    function spinnerButton() {
        return '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Process ...';
    }

    function resetTombolSimpanAnggaran() {
        $('.tombol-simpan-anggaran').prop('disabled', false).html('Save');
    }

    function onSuccessSimpanAnggaran() {
        showToast('Data saved successfully', 'success');
        $('#anggaranModal').modal('hide');
        $('#anggaranTable').DataTable().ajax.reload();
    }

    function simpanAnggaranBaru() {
        const data = ambilFormDataAnggaran();
        if (!validasiFormAnggaran(data)) return;

        $('.tombol-simpan-anggaran').prop('disabled', true).html(spinnerButton());

        $.post('/anggaran', data)
            .done(onSuccessSimpanAnggaran)
            .always(resetTombolSimpanAnggaran);
    }

    function updateAnggaran(id) {
        const data = ambilFormDataAnggaran();
        if (!validasiFormAnggaran(data)) return;

        $('.tombol-simpan-anggaran').prop('disabled', true).html(spinnerButton());

        $.ajax({
            url: '/anggaran/' + id,
            type: 'PUT',
            data,
            success: onSuccessSimpanAnggaran,
            complete: resetTombolSimpanAnggaran
        });
    }

    // Event Handlers
    $('body').on('click', '.tombol-tambah-anggaran', function (e) {
        e.preventDefault();
        $('#anggaranModal').modal('show');
    });

    $('body').on('click', '.tombol-edit-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.get('/anggaran/' + id + '/edit', function (res) {
            const anggaran = res.result;
            $('#anggaranModal').modal('show');
            $('#nama_anggaran').val(anggaran.nama_anggaran);
            $('#persentase_anggaran').val(anggaran.persentase_anggaran);
            const selectInstance = $('#id_pengeluaran')[0].tomselect;
            selectInstance.clear(); // Kosongkan dulu agar tidak stack
            if (Array.isArray(anggaran.id_pengeluaran)) {
                anggaran.id_pengeluaran.forEach(val => selectInstance.addItem(val));
            } else if (anggaran.id_pengeluaran) {
                selectInstance.addItem(anggaran.id_pengeluaran);
            }
            $('#anggaranModal').data('id', id);
            $('#anggaranModalLabel').text('Edit Budget');
            $('.tombol-simpan-anggaran').html('Update');
        });
    });

    $('body').on('click', '.tombol-simpan-anggaran', function (e) {
        e.preventDefault();
        const id = $('#anggaranModal').data('id');
        id ? updateAnggaran(id) : simpanAnggaranBaru();
    });

    $('#anggaranModal').on('hidden.bs.modal', resetFormAnggaran);

    $('body').on('click', '.tombol-del-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure you want to delete this data?',
            html: 'Deleted data cannot be recovered!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#012970',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'dark-mode'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/anggaran/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data successfully deleted', 'success');
                        $('#anggaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data failed to be deleted', 'danger');
                        $('#anggaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    // Setup CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
