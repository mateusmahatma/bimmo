$(document).ready(function () {
    const formKalkulator = $('#formKalkulator');
    const btnProses = $('#btnProses');
    const btnSpinner = $('#btnProsesSpinner');
    const daterangeSpan = $('#daterange span');
    const tanggalMulaiInput = $('#tanggal_mulai');
    const tanggalSelesaiInput = $('#tanggal_selesai');
    const btnReset = $('#btnReset');
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
        const countSelected = $('#countSelected');
        const checkAll = $('#checkAll');

        if (countSelected.length > 0) countSelected.text(count);

        if (btnBulkDelete.length > 0) {
            if (count > 0) {
                btnBulkDelete.removeClass('d-none');
            } else {
                btnBulkDelete.classList ? btnBulkDelete[0].classList.add('d-none') : btnBulkDelete.addClass('d-none');
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
        btnBulkDelete.addEventListener('click', function () {
            const checked = document.querySelectorAll('.check-item:checked');
            const ids = Array.from(checked).map(cb => cb.value);

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
                    // Show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
                    this.disabled = true;

                    $.ajax({
                        url: "/kalkulator/bulk-delete",
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { ids: ids },
                        success: (response) => {
                            showToast(response.message || 'Data deleted successfully', 'success');
                            fetchData(); // Manual reload

                            // Reset button
                            this.innerHTML = originalText;
                            this.disabled = false;
                            this.classList.add('d-none');
                            $('#checkAll').prop('checked', false);
                        },
                        error: (err) => {
                            console.error(err);
                            showToast('Failed to delete data', 'danger');
                            // Reset button
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }
                    });
                }
            });
        });
    }

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
        const income = $('#monthly_income').val();

        if (!income) {
            showToast('Pemasukan bulanan harus diisi!', 'danger');
            $('#monthly_income').focus();
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
                    // Don't reset date range, keep it for user convenience or reset partial
                    $('#monthly_income').val('');
                    $('#additional_income').val('');
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
        $('#monthly_income').val('');
        $('#additional_income').val('');
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

    // Numeric Input Only
    $('input[type="text"]').on('input', function () {
        // Allow numbers only for income inputs. 
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
    initDataTable();
    initDetailTable();
});