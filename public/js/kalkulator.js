$(document).ready(function () {
    // Theme Handler
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const skin = window.userSkin || 'auto';
    const updateSkinUrl = window.updateSkinUrl;

    function applyTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', mode);
        } else {
            document.documentElement.removeAttribute('data-bs-theme'); // auto
        }
        document.dispatchEvent(new Event("themeChanged"));
    }

    function highlightActiveSkin(mode) {
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') === `setTheme('${mode}')`) {
                el.classList.add('active');
            }
        });
    }

    function setTheme(mode) {
        applyTheme(mode);
        highlightActiveSkin(mode);

        fetch(updateSkinUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ skin: mode })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert("Gagal menyimpan tema.");
            })
            .catch(err => console.error("Gagal update tema:", err));
    }

    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    const formKalkulator = $('#formKalkulator');
    const btnProses = $('#btnProses');
    const btnSpinner = $('#btnProsesSpinner');
    const daterangeSpan = $('#daterange span');
    const tanggalMulaiInput = $('#tanggal_mulai');
    const tanggalSelesaiInput = $('#tanggal_selesai');
    let hasilAnggaranTable = null;

    function initDateRangePicker() {
        const today = moment();
        const start = today.clone().startOf('day');
        const end = today.clone().endOf('day');

        daterangeSpan.text(`${start.format('YYYY-MM-DD')} - ${end.format('YYYY-MM-DD')}`);
        tanggalMulaiInput.val(start.format('YYYY-MM-DD'));
        tanggalSelesaiInput.val(end.format('YYYY-MM-DD'));

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            locale: { format: 'YYYY-MM-DD' }
        }, function (start, end) {
            daterangeSpan.text(`${start.format('YYYY-MM-DD')} - ${end.format('YYYY-MM-DD')}`);
            tanggalMulaiInput.val(start.format('YYYY-MM-DD'));
            tanggalSelesaiInput.val(end.format('YYYY-MM-DD'));
        });
    }

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

        $(toastContainer).append(toastHtml);
        const toastElement = document.getElementById(toastId);
        new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 }).show();
        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
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
                { data: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
                { data: "tanggal_mulai", className: "text-center", render: d => moment(d).format("dddd, D MMMM YYYY") },
                { data: "tanggal_selesai", className: "text-center", render: d => moment(d).format("dddd, D MMMM YYYY") },
                { data: "nama_anggaran", className: "text-center", render: d => d || "-" },
                {
                    data: 'nama_jenis_pengeluaran', className: 'text-left', defaultContent: '-',
                    render: function (data, type, row) {
                        if (type !== "display" || !data || typeof data !== "string") return data || "-";
                        const lines = data.split(",");
                        const showLimit = 3;
                        const visible = lines.slice(0, showLimit);
                        const hidden = lines.slice(showLimit);
                        const tableId = `detail-table-${row.id || Math.random().toString(36).substring(7)}`;

                        let table = `<table style="width:100%;border-collapse:collapse;" id="${tableId}"><colgroup><col style="width:30px;"><col></colgroup>`;
                        visible.forEach((line, i) => {
                            table += `<tr><td style="border:1px solid #ddd;padding:4px;text-align:center;">${i + 1}</td><td style="border:1px solid #ddd;padding:4px;">${line.trim()}</td></tr>`;
                        });
                        hidden.forEach((line, i) => {
                            table += `<tr class="hidden-row" style="display:none;"><td style="border:1px solid #ddd;padding:4px;text-align:center;">${showLimit + i + 1}</td><td style="border:1px solid #ddd;padding:4px;">${line.trim()}</td></tr>`;
                        });
                        table += `</table>`;
                        if (hidden.length) {
                            table += `<button type="button" class="btn btn-link toggle-btn" data-target="${tableId}">Show more</button>`;
                        }
                        return table;
                    }
                },
                { data: "persentase_anggaran", className: "text-center", render: d => d ? `${d}%` : "0%" },
                { data: "nominal_anggaran", className: "text-center", render: d => parseFloat(d).toLocaleString("id-ID") || "0" },
                { data: "anggaran_yang_digunakan", className: "text-center", render: d => parseFloat(d).toLocaleString("id-ID") || "0" },
                { data: "sisa_anggaran", className: "text-center" },
                { data: "aksi", orderable: false, searchable: false, className: "text-center" },
            ],
        });
    }

    function validateForm() {
        if (!daterangeSpan.text().includes(' - ')) {
            showToast('Silakan pilih rentang tanggal terlebih dahulu!', 'danger');
            return false;
        }
        return true;
    }

    function submitForm() {
        if (!validateForm()) return;

        const [start, end] = daterangeSpan.text().split(' - ');
        tanggalMulaiInput.val(start.trim());
        tanggalSelesaiInput.val(end.trim());

        btnSpinner.removeClass('d-none');
        btnProses.prop('disabled', true);

        const formData = new FormData(formKalkulator[0]);

        fetch(formKalkulator.attr('action'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Anggaran berhasil diproses!', 'success');
                    hasilAnggaranTable?.ajax.reload(null, false);
                    formKalkulator[0].reset();
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

    $('body').on('click', '.toggle-btn', function () {
        const $table = $('#' + $(this).data('target'));
        const $hiddenRows = $table.find('.hidden-row');
        const isVisible = $hiddenRows.is(':visible');
        $hiddenRows.toggle(!isVisible);
        $(this).text(isVisible ? 'Show more' : 'Show less');
    });

    $('body').on('click', '.tombol-update-proses-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.ajax({
            url: `/kalkulator/${id}`,
            type: 'POST',
            data: {
                _token: csrfToken,
                _method: 'PUT'
            },
            success: function () {
                showToast('Data Berhasil diperbarui', 'success');
                hasilAnggaranTable?.ajax.reload();
            },
            error: function () {
                showToast('Gagal memperbarui data', 'danger');
                hasilAnggaranTable?.ajax.reload();
            }
        });
    });

    $('body').on('click', '.tombol-del-proses-anggaran', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            html: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'dark-mode' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/kalkulator/${id}`,
                    type: 'DELETE',
                    success: () => {
                        showToast('Data Berhasil dihapus', 'success');
                        hasilAnggaranTable?.ajax.reload();
                    },
                    error: () => {
                        showToast('Data Gagal dihapus', 'danger');
                        hasilAnggaranTable?.ajax.reload();
                    }
                });
            }
        });
    });

    $('input[type="number"]').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Inisialisasi
    initDateRangePicker();
    initDataTable();
});

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleButton');
    const hiddenItems = document.querySelectorAll('.hidden-item');
    let isExpanded = false;

    toggleBtn.addEventListener('click', function () {
        isExpanded = !isExpanded;

        hiddenItems.forEach(item => {
            item.style.display = isExpanded ? 'list-item' : 'none';
        });

        toggleBtn.textContent = isExpanded ? 'Show less' : 'Show more';
    });
});

document.addEventListener("DOMContentLoaded", function () {

    const kalkulatorId = document.getElementById("kalkulator-id").value;

    $("#detailAnggaran").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        ajax: {
            url: "/kalkulator/" + kalkulatorId,
            type: "GET",
            headers: { "X-Requested-With": "XMLHttpRequest" }
        },
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            {
                data: "tgl_transaksi",
                render: function (data) {
                    var date = new Date(data);
                    return date.toLocaleDateString("id-ID", {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    });
                },
            },
            { data: "nama" },
            {
                data: "nominal",
                render: data => "Rp " + Number(data).toLocaleString("id-ID")
            },
            {
                data: "keterangan",
                render: (data, type) => {
                    if (type !== "display") return data || "-";
                    if (!data?.trim()) return "-";

                    const rows = data
                        .split("\n")
                        .map((line, i) => `<tr><td>${i + 1}</td><td>${line}</td></tr>`)
                        .join("");

                    return `<table class="table-static">${rows}</table>`;
                }
            },
        ],
    });

});
