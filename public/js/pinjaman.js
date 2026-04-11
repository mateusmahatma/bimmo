$(document).ready(function () {
    // Theme Handler Removed - Managed Globally
    const csrfToken = window.csrfToken;

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let debounceTimer;
    const searchInput = $('#searchPinjaman');
    const tableContainer = $('#pinjaman-table-container');
    const filterStatus = $('#filter_status');

    // Utility functions for formatting
    const formatRupiah = (value) => {
        if (!value && value !== 0) return '';
        let valStr = value.toString();

        // Normalize: if it looks like a raw number with dot decimal, convert to comma
        if (valStr.includes('.') && !valStr.includes(',') && valStr.match(/\.\d{1,2}$/)) {
            valStr = valStr.replace('.', ',');
        }

        let numberString = valStr.replace(/[^,\d]/g, '');
        let split = numberString.split(',');
        split[0] = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        return split.length > 1 ? split[0] + ',' + split[1] : split[0];
    };

    const parseRupiah = (value) => {
        if (!value) return 0;
        return parseFloat(value.toString().replace(/\./g, '').replace(',', '.'));
    };

    // Apply formatting to modal inputs
    $('#pinjamanModal #jumlah_pinjaman, #pinjamanModal #nominal_angsuran').on('keyup', function () {
        $(this).val(formatRupiah($(this).val()));
    });

    // Basic calculation for modal
    $('#pinjamanModal #jumlah_pinjaman, #pinjamanModal #jangka_waktu').on('change input', function() {
        const amount = parseRupiah($('#pinjamanModal #jumlah_pinjaman').val());
        const duration = parseInt($('#pinjamanModal #jangka_waktu').val());
        if (amount > 0 && duration > 0) {
            const installment = amount / duration;
            $('#pinjamanModal #nominal_angsuran').val(formatRupiah(Math.round(installment)));
        }
    });

    $('#pinjamanModal #start_date, #pinjamanModal #jangka_waktu').on('change input', function() {
        const startDateStr = $('#pinjamanModal #start_date').val();
        const duration = parseInt($('#pinjamanModal #jangka_waktu').val());
        if (startDateStr && duration > 0) {
            const startDate = new Date(startDateStr);
            startDate.setMonth(startDate.getMonth() + duration);
            const year = startDate.getFullYear();
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const day = String(startDate.getDate()).padStart(2, '0');
            $('#pinjamanModal #end_date').val(`${year}-${month}-${day}`);
        }
    });

    let modalEditor;
    ClassicEditor
        .create(document.querySelector('#pinjamanModal #keterangan'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote'],
        })
        .then(editor => {
            modalEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Main Fetch Function
    function fetchPinjaman(url = window.location.href) {
        const urlObj = new URL(url, window.location.origin);

        const searchQuery = searchInput.val();
        if (searchQuery) urlObj.searchParams.set('search', searchQuery);

        const statusValue = filterStatus.val();
        if (statusValue) urlObj.searchParams.set('filter_status', statusValue);

        tableContainer.css('opacity', '0.5');

        fetch(urlObj.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.json())
            .then(data => {
                tableContainer.css('opacity', '1');
                tableContainer.html(data.html);

                // Update Summary Cards
                if (data.totalPinjaman) $("#totalPinjaman").text(data.totalPinjaman);
                if (data.totalPaid) $("#statTotalPaid").text(data.totalPaid);
                if (data.totalOriginal) $("#statTotalOriginal").text(data.totalOriginal);

                initBulkDelete();
            })
            .catch(error => {
                console.error('Error:', error);
                tableContainer.css('opacity', '1');
            });
    }

    // Search Input Listener
    searchInput.on('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchPinjaman();
        }, 500);
    });

    // Filter Status Listener
    filterStatus.on('change', function () {
        fetchPinjaman();

        // Update export Excel link
        const statusVal = $(this).val();
        let exportUrl = "/pinjaman/export/excel";
        if (statusVal) {
            exportUrl += "?filter_status=" + statusVal;
        }
        $('#btnExportExcel').attr('href', exportUrl);
    });

    // Pagination & Sorting Delegation
    tableContainer.on('click', '.pagination a, .sort-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const url = $(this).attr('href');
        if (url) fetchPinjaman(url);
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
            jumlah_pinjaman: parseRupiah($('#jumlah_pinjaman').val().trim()),
            jangka_waktu: $('#jangka_waktu').val().trim(),
            nominal_angsuran: $('#nominal_angsuran').val() ? parseRupiah($('#nominal_angsuran').val().trim()) : null,
            start_date: $('#start_date').val().trim(),
            end_date: $('#end_date').val().trim(),
            status: $('#status').val().trim(),
            keterangan: modalEditor ? modalEditor.getData() : $('#keterangan').val().trim()
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
                fetchPinjaman();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (const key in errors) {
                        errorMessage += errors[key][0] + '\n';
                    }
                    showToast(errorMessage || 'Gagal menyimpan data!', 'danger');
                } else {
                    console.error(xhr.responseText);
                    showToast('Terjadi kesalahan pada server!', 'danger');
                }
            },
            complete: function () {
                $('.tombol-simpan-pinjaman').prop('disabled', false).html('Simpan');
            }
        });
    }

    // Handle Add Button
    $('body').on('click', '.tombol-tambah-pinjaman', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#pinjamanModal').find('form')[0].reset();
        if (modalEditor) modalEditor.setData('');
        $('#pinjamanModal').modal('show');

        $('#pinjamanModal').off('click', '.tombol-simpan-pinjaman')
            .on('click', '.tombol-simpan-pinjaman', () => simpanPinjaman());
    });

    // Handle Edit Button
    tableContainer.on('click', '.tombol-edit-pinjaman', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');

        $.ajax({
            url: '/pinjaman/' + id + '/edit',
            type: 'GET',
            success: function (response) {
                $('#pinjamanModal').modal('show');

                // Populate Form
                $('#nama_pinjaman').val(response.result.nama_pinjaman);
                $('#jumlah_pinjaman').val(formatRupiah(response.result.jumlah_pinjaman));
                $('#jangka_waktu').val(response.result.jangka_waktu);
                $('#nominal_angsuran').val(formatRupiah(response.result.nominal_angsuran));
                $('#start_date').val(response.result.start_date);
                $('#end_date').val(response.result.end_date);
                $('#status').val(response.result.status);
                if (modalEditor) {
                    modalEditor.setData(response.result.keterangan || '');
                } else {
                    $('#keterangan').val(response.result.keterangan);
                }

                $('#pinjamanModal').off('click', '.tombol-simpan-pinjaman')
                    .on('click', '.tombol-simpan-pinjaman', () => simpanPinjaman(id));
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    // Handle Single Delete Button
    tableContainer.on('click', '.tombol-del-pinjaman', function (e) {
        e.preventDefault();
        e.stopPropagation();
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
                        fetchPinjaman();
                    },
                    error: function () {
                        showToast('Gagal menghapus data', 'danger');
                    }
                });
            }
        });
    });

    // ----------------------------------------------------------------
    // BULK DELETE LOGIC
    // ----------------------------------------------------------------
    function initBulkDelete() {
        const checkAll = $('#checkAll');
        const btnBulkDelete = $('#btnBulkDelete');
        const countSelected = $('#countSelected');

        function updateUI() {
            const checked = $('.check-item:checked');
            const count = checked.length;
            countSelected.text(count);
            if (count > 0) btnBulkDelete.removeClass('d-none');
            else btnBulkDelete.addClass('d-none');

            const allItems = $('.check-item');
            if (checkAll.length && allItems.length) {
                checkAll.prop('checked', checked.length === allItems.length && allItems.length > 0);
                checkAll.prop('indeterminate', checked.length > 0 && checked.length < allItems.length);
            }
        }

        checkAll.off('change').on('change', function () {
            $('.check-item').prop('checked', this.checked);
            updateUI();
        });

        $('.check-item').off('change').on('change', function () {
            updateUI();
        });

        updateUI();
    }

    // Handle Bulk Delete Click
    $('#btnBulkDelete').on('click', function () {
        const ids = $('.check-item:checked').map(function () { return $(this).val(); }).get();
        if (ids.length === 0) return;

        Swal.fire({
            title: `Hapus ${ids.length} pinjaman?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus terpilih!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');

                $.ajax({
                    url: '/pinjaman/bulk-delete',
                    type: 'DELETE',
                    data: { ids: ids },
                    success: function (response) {
                        showToast(response.message, 'success');
                        fetchPinjaman();
                        btn.addClass('d-none').prop('disabled', false).html(originalHtml);
                    },
                    error: function (xhr) {
                        showToast('Gagal menghapus pinjaman terpilih.', 'danger');
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });

    // Initial load
    initBulkDelete();

    // Handle Payment Modal (Create)
    $(document).on("click", "[data-bs-target='#bayarModal']", function (e) {
        e.stopPropagation();
        var button = $(this);
        var pinjamanId = button.data("pinjaman-id");
        var nominal = button.data("nominal");
        var tanggal = button.data("tanggal");

        var modal = $("#bayarModal");
        var form = modal.find("#bayarForm");

        // Reset modal state to "Create"
        modal.find("#bayarModalLabel").text("Bayar Pinjaman");
        modal.find(".btn-color").html('<i class="fa fa-check"></i> Bayar');
        modal.find("#current_file_container").addClass("d-none");
        form[0].reset();

        form.attr("action", "/pinjaman/" + pinjamanId + "/bayar");
        modal.find("#pinjamanId").val(pinjamanId);

        if (nominal !== undefined) {
            modal.find("#jumlah_bayar").val(nominal);
        }
        if (tanggal !== undefined) {
            modal.find("#tgl_bayar").val(tanggal);
        }

        // Remove method spoofing if it exists
        form.find('input[name="_method"]').remove();
    });

    // Handle Payment Edit Button
    $(document).on('click', '.edit-bayar', function (e) {
        e.stopPropagation();
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