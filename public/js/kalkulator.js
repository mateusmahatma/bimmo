$(document).ready(function () {
    const formKalkulator = $('#formKalkulator');
    const btnProses = $('#btnProses');
    const btnSpinner = $('#btnProsesSpinner');
    const daterangeSpan = $('#daterange span');
    const tanggalMulaiInput = $('#tanggal_mulai');
    const tanggalSelesaiInput = $('#tanggal_selesai');
    const btnReset = $('#btnReset');
    let tomSelectIncome = null;
    let hasilAnggaranTable = null;

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

    function initDateRangePicker() {
        if ($('#daterange').length === 0) return;
        if (typeof moment === 'undefined') {
            console.error('Moment.js is required for DateRangePicker but is not loaded.');
            return;
        }

        const today = moment();
        const start = today.clone().startOf('month');
        const end = today.clone().endOf('month');

        function cb(start, end) {
            daterangeSpan.text(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
            tanggalMulaiInput.val(start.format('YYYY-MM-DD'));
            tanggalSelesaiInput.val(end.format('YYYY-MM-DD'));
        }

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
            },
            locale: { format: 'YYYY-MM-DD' }
        }, cb);

        cb(start, end);
    }

    function initTomSelect() {
        const el = document.querySelector('#id_pemasukan');
        if (el) {
            tomSelectIncome = new TomSelect(el, {
                plugins: ['remove_button', 'dropdown_input'],
                maxItems: null,
                hideSelected: true,
                closeAfterSelect: false,
                persist: false,
                create: false,
                render: {
                    no_results: function (data, escape) {
                        return '<div class="no-results">No results found for "' + escape(data.input) + '"</div>';
                    },
                }
            });
        }
    }

    function fetchData(url = window.location.href, containerSelector = '#history-table-container') {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $(containerSelector).html(response);
                updateBulkDeleteUI();
            },
            error: function (err) {
                console.error('Error fetching data:', err);
                showToast('Gagal memuat data history', 'danger');
            }
        });
    }

    // Detail fetchData
    function fetchDetailData(url = window.location.href) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#detail-table-container').html(response);
            },
            error: function (err) {
                console.error('Error fetching detail data:', err);
                showToast('Gagal memuat data transaksi', 'danger');
            }
        });
    }

    // Standard Search Logic
    let searchTimer;
    $('#historySearch').on('keyup', function () {
        clearTimeout(searchTimer);
        const search = $(this).val();
        searchTimer = setTimeout(function () {
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('page', 1);
            fetchData(url.toString());
        }, 500);
    });

    $('#detailSearch').on('keyup', function () {
        clearTimeout(searchTimer);
        const search = $(this).val();
        searchTimer = setTimeout(function () {
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('page', 1);
            fetchDetailData(url.toString());
        }, 500);
    });

    // Pagination Click
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const isDetail = $(this).closest('#detail-table-container').length > 0;
        if (isDetail) {
            fetchDetailData(url);
        } else {
            fetchData(url);
        }
    });

    // Sorting Click
    $(document).on('click', '.sort-link', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const isDetail = $(this).closest('#detail-table-container').length > 0;
        if (isDetail) {
            fetchDetailData(url);
        } else {
            fetchData(url);
        }
    });

    function initDataTable() {
        // DataTables removed in favor of manual AJAX
        if ($('#history-table-container').length > 0) fetchData();
    }

    // Bulk Delete Logic
    function updateBulkDeleteUI() {
        const checked = $('.check-item:checked');
        const count = checked.length;
        const btnBulkDelete = $('#btnBulkDelete');
        const btnBulkSync = $('#btnBulkSync');
        const countSelected = $('#countSelected');
        const checkAll = $('#checkAll');

        if (countSelected.length > 0) countSelected.text(count);

        if (btnBulkDelete.length > 0) {
            if (count > 0) {
                btnBulkDelete.removeClass('d-none');
            } else {
                btnBulkDelete.addClass('d-none');
            }
        }

        if (btnBulkSync.length > 0) {
            if (count > 0) {
                btnBulkSync.removeClass('d-none');
            } else {
                btnBulkSync.addClass('d-none');
            }
        }

        // Update checkAll state
        const allItems = $('.check-item');
        if (checkAll.length > 0 && allItems.length > 0) {
            checkAll.prop('checked', checked.length === allItems.length);
            checkAll.prop('indeterminate', checked.length > 0 && checked.length < allItems.length);
        }
    }

    $(document).on('change', '#checkAll', function () {
        const isChecked = this.checked;
        $('.check-item').prop('checked', isChecked);
        updateBulkDeleteUI();
    });

    $(document).on('change', '.check-item', function () {
        updateBulkDeleteUI();
    });

    if (btnBulkDelete) {
        // ... (existing btnBulkDelete.addEventListener logic is already inside $(document).on pattern? wait)
        // Actually the code uses btnBulkDelete.addEventListener and also $(document).on for checkAll.
        // Let's stick to $(document).on for bulk buttons if possible, or follow existing pattern.
    }
    
    $(document).on('click', '#btnBulkDelete', function() {
        const checked = $('.check-item:checked');
        const ids = checked.map((i, el) => $(el).val()).get();
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Hapus Terpilih?',
            text: `Apakah Anda yakin ingin menghapus ${ids.length} item?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...').prop('disabled', true);

                $.ajax({
                    url: "/kalkulator/bulk-delete",
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { ids: ids },
                    success: (response) => {
                        showToast(response.message || 'Data deleted successfully', 'success');
                        fetchData();
                        btn.addClass('d-none');
                    },
                    error: (err) => {
                        console.error(err);
                        showToast('Failed to delete data', 'danger');
                    },
                    complete: () => {
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });

    $(document).on('click', '#btnBulkSync', function() {
        const checked = $('.check-item:checked');
        const ids = checked.map((i, el) => $(el).val()).get();
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Sinkronisasi Terpilih?',
            text: `Apakah Anda yakin ingin menyinkronkan ${ids.length} data terpilih?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, sinkronkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sinkronisasi...').prop('disabled', true);

                $.ajax({
                    url: "/kalkulator/bulk-sync",
                    type: 'PUT',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { ids: ids },
                    success: (response) => {
                        showToast(response.message || 'Data synced successfully', 'success');
                        fetchData(); // Simplest way is to reload the whole table
                    },
                    error: (err) => {
                        console.error(err);
                        showToast('Failed to sync data', 'danger');
                    },
                    complete: () => {
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });

    function parseNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        let s = String(val).trim();
        s = s.replace(/[^\d\-,.]/g, '');
        s = s.replace(/\./g, '').replace(',', '.');
        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    }

    function validateForm() {
        const startDate = tanggalMulaiInput.val();
        const endDate = tanggalSelesaiInput.val();
        const incomeTypes = $('#id_pemasukan').val();

        if (!incomeTypes || (Array.isArray(incomeTypes) && incomeTypes.length === 0)) {
            showToast('Silakan pilih jenis pemasukan!', 'danger');
            if (tomSelectIncome) tomSelectIncome.focus();
            return false;
        }

        if (!startDate || !endDate) {
            showToast('Silakan pilih rentang tanggal!', 'danger');
            return false;
        }

        if (new Date(startDate) > new Date(endDate)) {
            showToast('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!', 'danger');
            return false;
        }

        return true;
    }

    function submitForm() {
        if (!validateForm()) return;

        btnSpinner.removeClass('d-none');
        btnProses.prop('disabled', true);

        // Remove numeric formatting from currency inputs before submit
        let formData = new FormData(formKalkulator[0]);
        // Note: In this simple version we assume user inputs numbers. 
        // If we added auto-formatting to inputs, we'd need to strip it here.
        // For now, let's just submit.

        fetch(formKalkulator.attr('action'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Budget processed successfully!', 'success');
                    fetchData(); // Manual reload
                    // Clear Tom Select
                    if (tomSelectIncome) tomSelectIncome.clear();
                } else {
                    showToast(data.message || 'Failed to process budget', 'danger');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('An error occurred: ' + err.message, 'danger');
            })
            .finally(() => {
                btnSpinner.addClass('d-none');
                btnProses.prop('disabled', false);
            });
    }

    btnProses.on('click', function (e) {
        e.preventDefault();
        submitForm();
    });

    // Reset Button
    btnReset.on('click', function () {
        if (tomSelectIncome) tomSelectIncome.clear();
        initDateRangePicker(); // Reset date
    });

    $('body').on('click', '.tombol-del-proses-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Riwayat?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/kalkulator/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: () => {
                        showToast('Data deleted successfully', 'success');
                        fetchData(); // Manual reload
                    },
                    error: () => {
                        showToast('Gagal menghapus data', 'danger');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-sync-anggaran', function (e) {
        e.preventDefault();
        const btn = $(this);
        const id = btn.data('id');
        const icon = btn.find('i');

        // Add spinning animation
        btn.addClass('disabled');
        icon.addClass('bi-spin');

        $.ajax({
            url: `/kalkulator/${id}`,
            type: 'PUT',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                showToast('Data berhasil disinkronisasi', 'success');
                
                // Find the row and update cell values
                const row = btn.closest('tr');
                
                // Columns (assuming fixed order from _table_list.blade.php)
                // 7th: nominal_anggaran, 8th: anggaran_yang_digunakan, 9th: sisa_anggaran
                
                if (response.persentase_anggaran) {
                    row.find('td:nth-child(6)').html(`<span class="badge bg-light text-dark border-0 shadow-none" style="font-size: 0.75rem; padding: 4px 8px;">${response.persentase_anggaran}%</span>`);
                }
                
                if (response.nominal_anggaran_terkini) {
                    row.find('td:nth-child(7)').html(`<span class="fw-semibold text-dark">Rp ${response.nominal_anggaran_terkini}</span>`);
                }
                
                if (response.anggaran_digunakan_terkini) {
                    row.find('td:nth-child(8)').html(`<span class="text-danger fw-medium">Rp ${response.anggaran_digunakan_terkini}</span>`);
                }
                
                if (response.sisa_anggaran) {
                    const sisaCell = row.find('td:nth-child(9)');
                    const sisaStr = response.sisa_anggaran.replace(/\./g, '').replace(',', '.');
                    const sisaVal = parseFloat(sisaStr);
                    const sisaClass = sisaVal < 0 ? 'text-danger' : 'text-success';
                    const badgeClass = sisaVal < 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
                    const badgeText = sisaVal < 0 ? 'Melebihi Anggaran' : 'Dalam Anggaran';
                    
                    sisaCell.html(`
                        <span class="fw-bold ${sisaClass}">Rp ${response.sisa_anggaran}</span><br>
                        <span class="badge ${badgeClass}" style="font-size: 10px;">${badgeText}</span>
                    `);
                }
            },
            error: (err) => {
                console.error(err);
                showToast('Gagal menyinkronkan data', 'danger');
            },
            complete: () => {
                btn.removeClass('disabled');
                icon.removeClass('bi-spin');
            }
        });
    });

    // Numeric Input Only
    $('input[type="text"]').on('input', function () {
        // Obsolete handles legacy monthly_income or additional_income if still exist
        if (this.id === 'monthly_income' || this.id === 'additional_income') {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

    function initDetailTable() {
        const kalkulatorId = $('#kalkulator-id').val();
        if (!kalkulatorId) return;
        // Detail data initially rendered by Blade @include in show.blade.php
    }

    // Init
    initDateRangePicker();
    initTomSelect();
    initDataTable();
    initDetailTable();
});