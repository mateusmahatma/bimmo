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
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            locale: { format: 'YYYY-MM-DD' }
        }, cb);

        cb(start, end);
    }

    function initDataTable() {
        hasilAnggaranTable = $('#hasilAnggaranTable').DataTable({
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            serverSide: true,
            processing: true,
            ajax: { url: '/kalkulator', type: 'GET' },
            columns: [
                {
                    data: 'hash',
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    responsivePriority: 1,
                    render: function (data, type, row) {
                        return `<div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input check-item" type="checkbox" value="${data}">
                                </div>`;
                    }
                },
                { data: "DT_RowIndex", orderable: false, searchable: false, className: "text-center", responsivePriority: 1 },
                {
                    data: "tanggal_mulai",
                    responsivePriority: 2,
                    render: function (data, type, row) {
                        const start = new Date(row.tanggal_mulai).toLocaleDateString("id-ID", { day: 'numeric', month: 'short', year: 'numeric' });
                        const end = new Date(row.tanggal_selesai).toLocaleDateString("id-ID", { day: 'numeric', month: 'short', year: 'numeric' });
                        return `<span class="fw-medium">${start}</span> <br> <span class="text-muted small">s/d ${end}</span>`;
                    },
                },
                { data: "nama_anggaran", className: "text-center fw-bold", render: d => d || "-", responsivePriority: 1 },
                {
                    data: 'nama_jenis_pengeluaran',
                    name: 'nama_jenis_pengeluaran',
                    className: 'text-left',
                    responsivePriority: 10,
                    render: function (data, type, row) {
                        if (type !== "display" || !Array.isArray(data) || data.length === 0) return "-";

                        // Simple list implementation
                        let limit = 3;
                        let output = '<ul class="list-unstyled mb-0 small">';
                        data.slice(0, limit).forEach((name, i) => {
                            output += `<li><i class="bi bi-dot"></i> ${name}</li>`;
                        });
                        if (data.length > limit) {
                            output += `<li class="text-muted ms-3">+${data.length - limit} more</li>`;
                        }
                        output += '</ul>';
                        return output;
                    }
                },
                { data: "persentase_anggaran", className: "text-center", render: d => `<span class="badge bg-light text-dark border">${d}%</span>`, responsivePriority: 4 },
                { data: "nominal_anggaran", className: "text-end", render: d => 'Rp ' + parseFloat(d).toLocaleString("id-ID"), responsivePriority: 5 },
                { data: "anggaran_yang_digunakan", className: "text-end", render: d => 'Rp ' + parseFloat(d).toLocaleString("id-ID"), responsivePriority: 6 },
                {
                    data: "sisa_anggaran",
                    className: "text-end",
                    responsivePriority: 3,
                    render: function (data, type, row) {
                        const raw = (data ?? row.sisa_anggaran);
                        const numeric = parseNumber(raw);
                        const formatted = 'Rp ' + numeric.toLocaleString("id-ID");

                        if (numeric < 0) {
                            return `<span class="text-danger fw-bold">${formatted}</span><br><span class="badge bg-danger-subtle text-danger" style="font-size:10px;">Over Budget</span>`;
                        } else {
                            return `<span class="text-success fw-bold">${formatted}</span><br><span class="badge bg-success-subtle text-success" style="font-size:10px;">On Track</span>`;
                        }
                    }
                },
                { data: "aksi", orderable: false, searchable: false, className: "text-center", responsivePriority: 1 },
            ],
            language: {
                emptyTable: "Belum ada data proses anggaran."
            },
            drawCallback: function () {
                $('#checkAll').prop('checked', false);
                updateBulkDeleteUI();
            }
        });
    }

    // Bulk Delete Logic
    const checkAll = document.getElementById('checkAll');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const countSelected = document.getElementById('countSelected');

    function updateBulkDeleteUI() {
        const checked = document.querySelectorAll('.check-item:checked');
        const count = checked.length;

        if (countSelected) countSelected.textContent = count;

        if (btnBulkDelete) {
            if (count > 0) {
                btnBulkDelete.classList.remove('d-none');
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        // Update checkAll state
        const allItems = document.querySelectorAll('.check-item');
        if (checkAll && allItems.length > 0) {
            checkAll.checked = checked.length === allItems.length;
            checkAll.indeterminate = checked.length > 0 && checked.length < allItems.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            const isChecked = this.checked;
            document.querySelectorAll('.check-item').forEach(item => {
                item.checked = isChecked;
            });
            updateBulkDeleteUI();
        });
    }

    $('body').on('change', '.check-item', function () {
        updateBulkDeleteUI();
    });

    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function () {
            const checked = document.querySelectorAll('.check-item:checked');
            const ids = Array.from(checked).map(cb => cb.value);

            if (ids.length === 0) return;

            Swal.fire({
                title: 'Hapus Terpilih?',
                text: `Apakah Anda yakin ingin menghapus ${ids.length} data?`,
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
                            showToast(response.message || 'Data berhasil dihapus', 'success');
                            hasilAnggaranTable?.ajax.reload(null, false);

                            // Reset button
                            this.innerHTML = originalText;
                            this.disabled = false;
                            this.classList.add('d-none');
                            $('#checkAll').prop('checked', false);
                        },
                        error: (err) => {
                            console.error(err);
                            showToast('Gagal menghapus data', 'danger');
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
            showToast('Pendapatan bulanan wajib diisi!', 'danger');
            $('#monthly_income').focus();
            return false;
        }

        if (!startDate || !endDate) {
            showToast('Silakan pilih rentang tanggal!', 'danger');
            return false;
        }

        if (new Date(startDate) > new Date(endDate)) {
            showToast('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!', 'danger');
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
                    showToast(data.message || 'Anggaran berhasil diproses!', 'success');
                    if (hasilAnggaranTable) hasilAnggaranTable.ajax.reload(null, false);
                    // Don't reset date range, keep it for user convenience or reset partial
                    $('#monthly_income').val('');
                    $('#additional_income').val('');
                } else {
                    showToast(data.message || 'Gagal memproses anggaran', 'danger');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Terjadi kesalahan: ' + err.message, 'danger');
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
                        showToast('Data berhasil dihapus', 'success');
                        hasilAnggaranTable?.ajax.reload(null, false);
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

        $('#detailAnggaran').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: "/kalkulator/" + kalkulatorId,
                type: "GET",
                headers: { "X-Requested-With": "XMLHttpRequest" },
                error: function (xhr, error, thrown) {
                    console.error('DataTables Error:', error, thrown);
                    console.log('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
                {
                    data: "tgl_transaksi",
                    className: "text-center",
                    render: function (data) {
                        return new Date(data).toLocaleDateString("id-ID", {
                            day: "numeric", month: "short", year: "numeric"
                        });
                    },
                },
                { data: "nama" }, // Nama Pengeluaran
                {
                    data: "nominal",
                    className: "text-end",
                    render: data => "Rp " + parseFloat(data).toLocaleString("id-ID")
                },
                {
                    data: "keterangan",
                    render: (data, type, row) => {
                        if (type !== "display") return data || "-";
                        if (!data) return "-";

                        // Split by newlines and create an ordered list
                        const items = data.split('\n').filter(item => item.trim() !== '');
                        if (items.length > 1) {
                            let list = '<ol class="mb-0 ps-3 small text-muted">';
                            items.forEach(item => {
                                list += `<li>${item}</li>`;
                            });
                            list += '</ol>';
                            return list;
                        } else {
                            // If there's only one item, adding a number is optional but might be cleaner if user wants it
                            // But usually single item doesn't need numbering inside description.
                            // Let's assume they want it "tidy", maybe just a span
                            return `<span class="text-muted small">${items[0]}</span>`;
                        }
                    }
                },
            ],
            language: {
                emptyTable: "Belum ada transaksi pada periode ini."
            }
        });
    }

    // Init
    initDateRangePicker();
    initDataTable();
    initDetailTable();
});