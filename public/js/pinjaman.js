$(document).ready(function () {
    // Theme Handler Removed - Managed Globally
    const csrfToken = window.csrfToken;

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // DataTable Initialization
    let table;
    if ($("#pinjamanTable").length > 0) {
        table = $("#pinjamanTable").DataTable({
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            serverSide: true,
            processing: true,
            ajax: {
                url: "/pinjaman",
                type: "GET",
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                dataSrc: function (json) {
                    // Update Total Summary
                    if (json.totalPinjaman) {
                        $("#totalPinjaman").text(json.totalPinjaman);
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<input class="form-check-input check-item" type="checkbox" value="${row.hash}">`;
                    }
                },
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
                { data: "nama_pinjaman", name: "nama_pinjaman" },
                { data: "keterangan", name: "keterangan", defaultContent: "-" },
                { data: "total_loan", name: "total_loan", className: "text-end" },
                { data: "paid_amount", name: "paid_amount", className: "text-end" },
                { data: "jumlah_pinjaman", name: "jumlah_pinjaman", className: "text-end" },
                {
                    data: "status",
                    name: "status",
                    className: "text-center",
                    render: function (data) {
                        if (data === "belum_lunas") {
                            return `<span class="badge bg-danger-light text-danger"><i class="bi bi-x-circle me-1"></i> Unpaid</span>`;
                        } else if (data === "lunas") {
                            return `<span class="badge bg-success-light text-success"><i class="bi bi-check-circle me-1"></i> Paid</span>`;
                        }
                        return data ? data : '-';
                    }
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
            ],
        });
    }

    // Reload Table on Filter Change
    $('#filter_status').on('change', function () {
        if (table) table.ajax.reload();

        // Update export Excel link
        const filterStatus = $(this).val();
        let exportUrl = "/pinjaman/export/excel";
        if (filterStatus) {
            exportUrl += "?filter_status=" + filterStatus;
        }
        $('#btnExportExcel').attr('href', exportUrl);
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

    // Function to handle Save
    function simpanPinjaman(id = '') {
        const url = id ? '/pinjaman/' + id : '/pinjaman';
        const type = id ? 'PUT' : 'POST';

        const formData = {
            nama_pinjaman: $('#nama_pinjaman').val().trim(),
            jumlah_pinjaman: $('#jumlah_pinjaman').val().trim(),
            jangka_waktu: $('#jangka_waktu').val().trim(),
            start_date: $('#start_date').val().trim(),
            end_date: $('#end_date').val().trim(),
            status: $('#status').val().trim(),
            keterangan: $('#keterangan').val().trim()
        };

        if (formData.nama_pinjaman === '') {
            showToast('Nama Pinjaman Harus Diisi!', 'danger');
            return;
        }

        $('.tombol-simpan-pinjaman').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Proses ...');

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function () {
                showToast('Data Berhasil disimpan', 'success');
                $('#pinjamanModal').modal('hide');
                if (table) table.ajax.reload();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                showToast('Gagal menyimpan data!', 'danger');
            },
            complete: function () {
                $('.tombol-simpan-pinjaman').prop('disabled', false).html('Simpan');
            }
        });
    }

    // Handle Add Button
    $('body').on('click', '.tombol-tambah-pinjaman', function (e) {
        e.preventDefault();
        $('#pinjamanModal').find('form')[0].reset(); // Reset form
        $('#pinjamanModal').modal('show');

        // Remove previous handlers to avoid duplicates
        $('#pinjamanModal').off('click', '.tombol-simpan-pinjaman')
            .on('click', '.tombol-simpan-pinjaman', () => simpanPinjaman());
    });

    // Handle Edit Button
    $('body').on('click', '.tombol-edit-pinjaman', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.ajax({
            url: '/pinjaman/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pinjamanModal').modal('show');

                // Populate Form
                $('#nama_pinjaman').val(response.result.nama_pinjaman);
                $('#jumlah_pinjaman').val(response.result.jumlah_pinjaman);
                $('#jangka_waktu').val(response.result.jangka_waktu);
                $('#start_date').val(response.result.start_date);
                $('#end_date').val(response.result.end_date);
                $('#status').val(response.result.status);
                $('#keterangan').val(response.result.keterangan);

                $('#pinjamanModal').off('click', '.tombol-simpan-pinjaman')
                    .on('click', '.tombol-simpan-pinjaman', () => simpanPinjaman(id));
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    // Handle Single Delete Button
    $('body').on('click', '.tombol-del-pinjaman', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pinjaman/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data Berhasil dihapus', 'success');
                        if (table) table.ajax.reload();
                    },
                    error: function () {
                        showToast('Gagal menghapus data', 'danger');
                        if (table) table.ajax.reload();
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
            title: `Delete ${ids.length} loans?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete selected!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const OriginalBtnText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm"></span> Deleting...').prop('disabled', true);

                $.ajax({
                    url: '/pinjaman/bulk-delete',
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message, 'success');
                        if (table) table.ajax.reload();
                        $('#checkAll').prop('checked', false);
                        $('#btnBulkDelete').addClass('d-none').prop('disabled', false).html(OriginalBtnText);
                    },
                    error: function (xhr) {
                        showToast('Failed to delete selected loans.', 'danger');
                        $('#btnBulkDelete').prop('disabled', false).html(OriginalBtnText);
                    }
                });
            }
        });
    });

    // Reset check all on page change
    if (table) {
        table.on('draw', function () {
            $('#checkAll').prop('checked', false);
            $('#checkAll').prop('indeterminate', false);
            updateBulkButton();
        });
    }

    // Handle Payment Modal (Create)
    $('body').on("click", "[data-bs-target='#bayarModal']", function () {
        var button = $(this);
        var pinjamanId = button.data("pinjaman-id");
        var modal = $("#bayarModal");
        var form = modal.find("#bayarForm");

        // Reset modal state to "Create"
        modal.find("#bayarModalLabel").text("Bayar Pinjaman");
        modal.find(".btn-color").html('<i class="fa fa-check"></i> Bayar');
        modal.find("#current_file_container").addClass("d-none");
        form[0].reset();

        form.attr("action", "/pinjaman/" + pinjamanId + "/bayar");
        modal.find("#pinjamanId").val(pinjamanId);

        // Remove method spoofing if it exists
        form.find('input[name="_method"]').remove();
    });

    // Handle Payment Edit Button
    $('body').on('click', '.edit-bayar', function () {
        var id = $(this).data('id');
        var modal = $("#bayarModal");
        var form = modal.find("#bayarForm");

        $.ajax({
            url: '/bayar-pinjaman/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    modal.find("#bayarModalLabel").text("Edit Pembayaran");
                    modal.find(".btn-color").html('<i class="fa fa-save"></i> Perbarui');

                    form.attr("action", "/bayar-pinjaman/" + id);
                    modal.find("#jumlah_bayar").val(response.data.jumlah_bayar);
                    modal.find("#tgl_bayar").val(response.data.tgl_bayar);

                    if (response.data.bukti_bayar) {
                        modal.find("#current_file_link").attr("href", response.data.bukti_bayar);
                        modal.find("#current_file_container").removeClass("d-none");
                    } else {
                        modal.find("#current_file_container").addClass("d-none");
                    }

                    // Add method PUT for spoofing
                    if (form.find('input[name="_method"]').length === 0) {
                        form.append('<input type="hidden" name="_method" value="PUT">');
                    } else {
                        form.find('input[name="_method"]').val("PUT");
                    }

                    modal.modal('show');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                showToast('Gagal memuat data pembayaran', 'danger');
            }
        });
    });

    // Reset modal on close
    $('#pinjamanModal, #bayarModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        if ($(this).attr('id') === 'bayarModal') {
            $(this).find("#current_file_container").addClass("d-none");
            $(this).find('input[name="_method"]').remove();
        }
    });

});