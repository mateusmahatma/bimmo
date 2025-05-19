// Fungsi untuk menampilkan toast notification
function showToast(message, type) {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const colors = {
        success: '#012970',  // Biru
        danger: '#dc3545',   // Merah
        warning: '#ffc107',  // Kuning
        info: '#17a2b8',     // Biru muda
        primary: '#007bff',  // Biru
    };

    const bgColor = colors[type] || '#6c757d'; // Default ke abu-abu jika tipe tidak ada

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

document.addEventListener('DOMContentLoaded', function () {
    // Setup CSRF token header for fetch if needed
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Elements
    const formKalkulator = document.getElementById('formKalkulator');
    const btnProses = document.getElementById('btnProses');
    const btnSpinner = document.getElementById('btnProsesSpinner');
    const btnText = document.getElementById('btnProsesText');

    // Date Range picker initialization (assuming daterangepicker & moment loaded)
    const today = moment();
    const start_date = today.clone().startOf('day');
    const end_date = today.clone().endOf('day');

    // Set initial date range text & hidden inputs
    const daterangeSpan = document.querySelector('#daterange span');
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    const tanggalSelesaiInput = document.getElementById('tanggal_selesai');

    if (daterangeSpan && tanggalMulaiInput && tanggalSelesaiInput) {
        daterangeSpan.textContent = start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD');
        tanggalMulaiInput.value = start_date.format('YYYY-MM-DD');
        tanggalSelesaiInput.value = end_date.format('YYYY-MM-DD');

        $('#daterange').daterangepicker({
            startDate: start_date,
            endDate: end_date,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function (start, end) {
            daterangeSpan.textContent = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
            tanggalMulaiInput.value = start.format('YYYY-MM-DD');
            tanggalSelesaiInput.value = end.format('YYYY-MM-DD');
        });
    }

    // Inisialisasi DataTable (jika pakai DataTable)
    let hasilAnggaranTable = null;
    if ($('#hasilAnggaranTable').length) {
        hasilAnggaranTable = $('#hasilAnggaranTable').DataTable({
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            serverSide: true,
            processing: true,
            language: {
                processing: '<div class="loader-container"><div class="loader"></div></div>',
            },
            ajax: {
                url: '/kalkulator',
                type: 'GET',
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
                { data: "tanggal_mulai", name: "tanggal_mulai", className: "text-center", render: d => moment(d).format("dddd, D MMMM YYYY") },
                { data: "tanggal_selesai", name: "tanggal_selesai", className: "text-center", render: d => moment(d).format("dddd, D MMMM YYYY") },
                { data: "nama_anggaran", name: "nama_anggaran", className: "text-center", render: d => d || "-" },
                { data: 'nama_jenis_pengeluaran', name: 'jenis_pengeluaran', className: 'text-center', defaultContent: '-' },
                { data: "persentase_anggaran", name: "persentase_anggaran", className: "text-center", render: d => d ? d + "%" : "0%" },
                { data: "nominal_anggaran", name: "nominal_anggaran", className: "text-center", render: d => parseFloat(d).toLocaleString("id-ID") || "0" },
                { data: "anggaran_yang_digunakan", name: "anggaran_yang_digunakan", className: "text-center", render: d => parseFloat(d).toLocaleString("id-ID") || "0" },
                { data: "sisa_anggaran", name: "sisa_anggaran", className: "text-center" },
                { data: "aksi", orderable: false, searchable: false, className: "text-center" },
            ],
        });
    }

    // Validasi form sederhana
    function validateForm() {
        const dateRangeText = daterangeSpan ? daterangeSpan.textContent : '';
        if (!dateRangeText.includes(' - ')) {
            showToast('Silakan pilih rentang tanggal terlebih dahulu!', 'danger');
            return false;
        }
        return true;
    }

    // Handler tombol Proses
    btnProses.addEventListener('click', function (e) {
        e.preventDefault();

        if (!validateForm()) return;

        // Ambil tanggal dari date range picker
        const [start, end] = daterangeSpan.textContent.split(' - ');
        tanggalMulaiInput.value = start.trim();
        tanggalSelesaiInput.value = end.trim();

        // Disable button & show spinner
        btnSpinner.classList.remove('d-none');
        btnText.textContent = ' Memproses...';
        btnProses.disabled = true;

        // Kirim data form via fetch POST
        const formData = new FormData(formKalkulator);

        fetch(formKalkulator.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Terjadi kesalahan saat memproses anggaran');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Anggaran berhasil diproses!', 'success');
                    // Reload datatable kalau ada
                    if (hasilAnggaranTable) {
                        hasilAnggaranTable.ajax.reload(null, false);
                    }
                    formKalkulator.reset();
                } else {
                    showToast(data.message || 'Gagal memproses anggaran', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan: ' + error.message, 'danger');
            })
            .finally(() => {
                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fa fa-sync-alt"></i> Proses';
                btnProses.disabled = false;
            });
    });

    // Global Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle Proses Update
    $('body').on('click', '.tombol-update-proses-anggaran', function (e) {
        e.preventDefault();

        // Logic for handling update process goes here
        const id = $(this).data('id');
        $.ajax({
            url: '/kalkulator/' + id,
            type: 'PUT',
            success: function () {
                showToast('Data Berhasil diperbarui', 'success');
                $('#hasilAnggaranTable').DataTable().ajax.reload();
            },
            error: function () {
                showToast('Gagal memperbarui data', 'danger');
                $('#hasilAnggaranTable').DataTable().ajax.reload();
            }
        });
    });

    // Handle Proses Delete
    $('body').on('click', '.tombol-del-proses-anggaran', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'dark-mode'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/kalkulator/' + id,
                    type: 'DELETE',
                    success: function () {
                        showToast('Data Berhasil dihapus', 'success');
                        $('#hasilAnggaranTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        showToast('Data Gagal dihapus', 'danger');
                        $('#hasilAnggaranTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});

// Batasi input hanya digit 0-9 secara realtime
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});