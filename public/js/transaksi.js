$(document).ready(function () {
    // Theme Handler
    const skin = window.userSkin || 'auto';
    const updateSkinUrl = window.updateSkinUrl;
    const csrfToken = window.csrfToken;

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

    // Eksekusi awal tema
    applyTheme(skin);
    highlightActiveSkin(skin);
    window.setTheme = setTheme;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    var today = moment();
    var start_date = today.startOf("day");
    var end_date = today.endOf("day");

    $("#daterange span").html(
        start_date.format("YYYY-MM-DD") + " - " + end_date.format("YYYY-MM-DD")
    );

    $("#daterange").daterangepicker(
        {
            startDate: start_date,
            endDate: end_date,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                "This Year": [moment().startOf("year"), moment().endOf("year")],
                "Last Year": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year"),
                ],
            },
            containerClass: document.documentElement.getAttribute("data-bs-theme") === "dark" ? "dark-mode" : ""
        },
        function (chosen_start_date, chosen_end_date) {
            if (
                chosen_start_date.isSame(moment(), "day") &&
                chosen_end_date.isSame(moment(), "day")
            ) {
                $("#tgl_transaksi").val(moment().format("yyyy-mm-dd"));
            }

            $("#daterange span").html(
                chosen_start_date.format("YYYY-MM-DD") +
                " - " +
                chosen_end_date.format("YYYY-MM-DD")
            );
            table.draw();
        }
    );

    const table = $("#transaksiTable").DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: "/transaksi",
            type: "GET",
            data: function (d) {
                d.start_date = $("#daterange")
                    .data("daterangepicker")
                    .startDate.format("YYYY-MM-DD");
                d.end_date = $("#daterange")
                    .data("daterangepicker")
                    .endDate.format("YYYY-MM-DD");
                d.filter_pemasukan = $('select[name="filter_pemasukan"]').val();
                d.filter_pengeluaran = $('select[name="filter_pengeluaran"]').val();
            },
            dataSrc: function (json) {
                $("#totalPemasukan").text(
                    json.totalPemasukan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                );
                $("#totalPengeluaran").text(
                    json.totalPengeluaran.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                );
                $("#netIncome").text(
                    json.netIncome.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                );
                return json.data;
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
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

            { data: 'pemasukan_nama', name: 'pemasukan_nama' },
            {
                data: "nominal_pemasukan",
                render: data => {
                    const value = parseFloat(data);
                    return `<span>${isNaN(value) ? 0 : value.toLocaleString("id-ID")}</span>`;
                }
            },
            { data: 'pengeluaran_nama', name: 'pengeluaran_nama' },
            {
                data: "nominal",
                render: data => {
                    const value = parseFloat(data);
                    return `<span>${isNaN(value) ? 0 : value.toLocaleString("id-ID")}</span>`;
                }
            },
            {
                data: "keterangan",
                render: (data, type) => {
                    if (type !== "display") return data || "-";

                    if (typeof data !== "string" || !data.trim()) return "-";

                    const rows = data.split("\n").map(
                        (line, i) => `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${i + 1}</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">${line}</td>
                            </tr>`
                    ).join("");

                    return `
                        <table style="width: 100%; border-collapse: collapse;">
                            <colgroup>
                                <col style="width: 30px;">
                                <col>
                            </colgroup>
                            ${rows}
                        </table>`;
                }
            },
            { data: 'created_at' },
            { data: 'updated_at' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
            },
        ],
        createdRow: function (row, data) {
            if (data.status == 0) {
                $(row).addClass('table-danger');
            } else if (data.status == 2) {
                $(row).addClass('table-success');
            }
        }
    });
    $('select[name="filter_pemasukan"]').on("change", function () {
        table.ajax.reload();
    });

    $('select[name="filter_pengeluaran"]').on("change", function () {
        table.ajax.reload();
    });
});

// Filter Tomselect
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('#pemasukan', '#filter_pemasukan', {
        allowEmptyOption: true,
        placeholder: '- Pilih -',
        create: false,
        onInitialize: function () {
            // Jika tidak ada nilai terpilih, pakai placeholder
            if (!this.getValue()) {
                this.setTextboxValue('');
            }
        }
    });
});

// Filter Tomselect
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('#pengeluaran', '#filter_pengeluaran', {
        allowEmptyOption: true,
        placeholder: '- Pilih -',
        create: false,
        onInitialize: function () {
            // Jika tidak ada nilai terpilih, pakai placeholder
            if (!this.getValue()) {
                this.setTextboxValue('');
            }
        }
    });
});

// JS
document.addEventListener('DOMContentLoaded', function () {
    // TomSelect untuk filter pemasukan
    new TomSelect('#filter_pemasukan', {
        allowEmptyOption: true,
        placeholder: '- Pilih -',
        create: false,
        onInitialize: function () {
            if (!this.getValue()) {
                this.setTextboxValue('');
            }
        }
    });

    // TomSelect untuk filter pengeluaran
    new TomSelect('#filter_pengeluaran', {
        allowEmptyOption: true,
        placeholder: '- Pilih -',
        create: false,
        onInitialize: function () {
            if (!this.getValue()) {
                this.setTextboxValue('');
            }
        }
    });
});


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
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

    // Tambahkan toast ke container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Create Transaksi
document.addEventListener('DOMContentLoaded', function () {
    // Mengambil elemen form dan button
    const formTransaksi = document.getElementById('formTransaksi');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnText');
    const transaksiModal = document.getElementById('transaksiModal');

    // Fungsi untuk validasi form
    function validateForm() {
        const tglTransaksi = document.getElementById('tgl_transaksi').value;
        let pemasukan = document.getElementById('pemasukan').value;
        let pengeluaran = document.getElementById('pengeluaran').value;
        let nominalPemasukan = document.getElementById('nominal_pemasukan').value;
        let nominal = document.getElementById('nominal').value;

        // Validasi tanggal transaksi
        if (!tglTransaksi) {
            showToast('Tanggal transaksi harus diisi!', 'danger');
            return false;
        }

        // Validasi setidaknya satu dari pemasukan atau pengeluaran harus diisi
        if (!pemasukan && !pengeluaran) {
            showToast('Anda harus mengisi minimal satu dari pemasukan atau pengeluaran!', 'danger');
            return false;
        }

        // Jika pemasukan dipilih, nominal pemasukan harus diisi
        if (pemasukan && !nominalPemasukan) {
            showToast('Nominal pemasukan harus diisi!', 'danger');
            return false;
        }

        // Jika pengeluaran dipilih, nominal pengeluaran harus diisi
        if (pengeluaran && !nominal) {
            showToast('Nominal pengeluaran harus diisi!', 'danger');
            return false;
        }

        return true;
    }

    // Fungsi untuk me-refresh tabel data

    // Event listener untuk button simpan
    btnSimpan.addEventListener('click', function (event) {
        event.preventDefault();

        // Validasi form
        if (!validateForm()) {
            return;
        }

        // Menampilkan spinner dan menonaktifkan button
        btnSpinner.classList.remove('d-none');
        btnSimpan.disabled = true;

        // Mengambil data form
        const formData = new FormData(formTransaksi);

        // Mengirim data dengan AJAX
        fetch(formTransaksi.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Terjadi kesalahan pada server');
                }
                return response.json();
            })
            .then(data => {
                // Jika proses berhasil
                if (data.success) {
                    // Menutup modal menggunakan jQuery (cara yang lebih kompatibel)
                    try {
                        // Coba tutup dengan Bootstrap 5
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            const modalElement = document.getElementById('transaksiModal');
                            const modalObj = bootstrap.Modal.getInstance(modalElement);
                            if (modalObj) {
                                modalObj.hide();
                            } else {
                                // Fallback untuk Bootstrap 5 jika getInstance tidak bekerja
                                $('#transaksiModal').modal('hide');
                            }
                        } else {
                            // Fallback untuk Bootstrap 4 atau jQuery
                            $('#transaksiModal').modal('hide');
                        }
                    } catch (err) {
                        // Fallback jika terjadi error
                        console.error('Error closing modal:', err);
                        // Cobalah dengan DOM API
                        const closeButton = transaksiModal.querySelector('.btn-close') || transaksiModal.querySelector('[data-bs-dismiss="modal"]');
                        if (closeButton) {
                            closeButton.click();
                        }
                    }

                    // Menampilkan toast sukses
                    showToast(data.message || 'Data transaksi berhasil disimpan!', 'success');

                    // Reset form
                    formTransaksi.reset();

                    // Refresh DataTable
                    $("#transaksiTable").DataTable().ajax.reload(null, false);
                } else {
                    // Menampilkan toast error
                    showToast(data.message || 'Gagal menyimpan data transaksi!', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan: ' + error.message, 'danger');
            })
            .finally(() => {
                // Mengembalikan button ke kondisi awal
                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fa fa-paper-plane"></i> Simpan';
                btnSimpan.disabled = false;
            });
    });

    // Event listener untuk modal saat ditampilkan
    if (transaksiModal) {
        transaksiModal.addEventListener('shown.bs.modal', function () {
            // Set default tanggal ke hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tgl_transaksi').value = today;

            // Reset form
            formTransaksi.reset();
            document.getElementById('tgl_transaksi').value = today;

            // Reset state tombol
            btnSpinner.classList.add('d-none');
            btnText.innerHTML = '<i class="fa fa-paper-plane"></i> Simpan';
            btnSimpan.disabled = false;
        });
    }

    // Event listener untuk select pemasukan dan pengeluaran
    const pemasukanSelect = document.getElementById('pemasukan');
    const pengeluaranSelect = document.getElementById('pengeluaran');

    if (pemasukanSelect) {
        pemasukanSelect.addEventListener('change', function () {
            const pengeluaranValue = pengeluaranSelect.value;
            if (this.value && pengeluaranValue) {
                showToast('Disarankan mengisi salah satu saja: pemasukan atau pengeluaran', 'warning');
            }
        });
    }

    if (pengeluaranSelect) {
        pengeluaranSelect.addEventListener('change', function () {
            const pemasukanValue = pemasukanSelect.value;
            if (this.value && pemasukanValue) {
                showToast('Disarankan mengisi salah satu saja: pemasukan atau pengeluaran', 'warning');
            }
        });
    }
});

// Edit Transaksi
$("body").on("click", ".tombol-edit-transaksi", function () {
    var id = $(this).data("id");

    $.ajax({
        url: "transaksi/" + id + "/edit",
        type: "GET",
        success: function (response) {
            $("#editTransaksiModal").modal("show");

            var tgl_transaksi = response.result.tgl_transaksi;
            if (tgl_transaksi) {
                var tanggal = new Date(tgl_transaksi);
                var year = tanggal.getFullYear();
                var month = (tanggal.getMonth() + 1).toString().padStart(2, "0");
                var day = tanggal.getDate().toString().padStart(2, "0");
                tgl_transaksi = `${year}-${month}-${day}`;
            }

            $("#edit_tgl_transaksi").val(tgl_transaksi);
            $("#edit_pemasukan").val(response.result.pemasukan);
            $("#edit_nominal_pemasukan").val(response.result.nominal_pemasukan);
            $("#edit_pengeluaran").val(response.result.pengeluaran);
            $("#edit_nominal").val(response.result.nominal);
            $("#edit_keterangan").val(response.result.keterangan);

            var form = $("#editTransaksiForm");
            form.attr("action", "transaksi/" + id);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
            showToast("Gagal memuat data untuk edit", "danger");
        },
    });
});

// Update Transaksi
$("body").on("submit", "#editTransaksiForm", function (e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr("action");
    var formData = form.serialize();
    var submitButton = form.find("button[type='submit']");

    submitButton.html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'
    ).prop("disabled", true);

    $.ajax({
        url: url,
        type: "PUT",
        data: formData,
        success: function () {
            $("#editTransaksiModal").modal("hide");

            setTimeout(() => {
                showToast("Data berhasil diupdate", "success");
                $("#transaksiTable").DataTable().ajax.reload(null, false);
            }, 300);
        },
        error: function (xhr, status, error) {
            console.error("Error updating data:", error);
            showToast("Gagal memperbarui data transaksi", "danger");
        },
        complete: function () {
            submitButton.html("Update").prop("disabled", false);
        },
    });
});

// Delete Transaksi
$("body").on("click", ".tombol-del-transaksi", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Yakin mau hapus data ini?",
        html: `Data yang dihapus tidak dapat dikembalikan!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            var id = $(this).data("id");

            $.ajax({
                url: "/transaksi/" + id,
                type: "DELETE",
                success: function () {
                    showToast("Data berhasil dihapus", "success");
                    $("#transaksiTable").DataTable().ajax.reload();
                },
                error: function () {
                    showToast("Data gagal dihapus", "danger");
                    $("#transaksiTable").DataTable().ajax.reload();
                },
            });
        }
    });
});

// Handle Download PDF
function downloadPDFTransaksi() {
    var start_date = $("#daterange")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
    var end_date = $("#daterange")
        .data("daterangepicker")
        .endDate.format("YYYY-MM-DD");

    Swal.fire({
        title: "Sedang mendownload...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    $.ajax({
        url: "/transaksi/cetak_pdf",
        type: "GET",
        data: { start_date: start_date, end_date: end_date },
        xhrFields: {
            responseType: "blob",
        },
        success: function (data) {
            var a = document.createElement("a");
            var url = window.URL.createObjectURL(data);

            a.href = url;
            a.download = "Transaksi.pdf";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            Swal.fire({
                icon: "success",
                title: "File berhasil di download!",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        error: function (xhr, status, error) {
            console.error("Error downloading file:", error);
            Swal.fire({
                icon: "error",
                title: "Terjadi kesalahan!",
                text: "Kesalahan saat mendownload file.",
            });
        },
    });
}

// Handle Download Excel
function downloadExcel() {
    var start_date = $("#daterange")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
    var end_date = $("#daterange")
        .data("daterangepicker")
        .endDate.format("YYYY-MM-DD");

    Swal.fire({
        title: "Sedang mendownload...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    $.ajax({
        url: "/transaksi/download-excel",
        type: "GET",
        data: { start_date: start_date, end_date: end_date },
        xhrFields: {
            responseType: "blob",
        },
        success: function (data) {
            var a = document.createElement("a");
            var url = window.URL.createObjectURL(data);

            a.href = url;
            a.download = "Transaksi.xls";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            Swal.fire({
                icon: "success",
                title: "File berhasil di download!",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        error: function (xhr, status, error) {
            console.error("Error downloading file:", error);
            Swal.fire({
                icon: "error",
                title: "Terjadi kesalahan!",
                text: "Kesalahan saat mendownload file.",
            });
        },
    });
}

function validasiInput(inputElement) {
    inputElement.value = inputElement.value.replace(/[^0-9]/g, "");
}

// Handle Upload File
$(document).ready(function () {
    $("body").on("click", ".tombol-upload-file", function () {
        let transaksiId = $(this).data("id");
        $("#transaksiId").val(transaksiId);
        $("#uploadModal").modal("show");
    });

    $("#uploadForm").submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Yakin ingin upload file?",
            text: "Pastikan file yang Anda unggah sudah sesuai.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, upload!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData(this);
                var submitButton = $(this).find('button[type="submit"]');

                submitButton
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses Upload...'
                    )
                    .prop("disabled", true);

                $.ajax({
                    url: "/transaksi/upload",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $("#uploadModal").modal("hide");
                            $("#transaksiTable").DataTable().ajax.reload();

                            Swal.fire({
                                icon: "success",
                                title: "Data Berhasil di Upload",
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: "top",
                                customClass: {
                                    title: "swal2-title-create",
                                    popup: "swal2-popup-create",
                                },
                                iconColor: "#ffffff",
                            });

                            $("#uploadForm")[0].reset();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message || "Upload gagal.",
                                confirmButtonColor: "#d33",
                            });
                        }
                    },
                    error: function (xhr) {
                        var errorMessage =
                            xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : "Terjadi kesalahan saat upload.";
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: errorMessage,
                            confirmButtonColor: "#d33",
                        });
                    },
                    complete: function () {
                        submitButton
                            .prop("disabled", false)
                            .html('<i class="fa fa-upload"></i> Upload');
                    },
                });
            }
        });
    });
});

// Handle toggle status
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#transaksiTable').on('click', '.tombol-toggle-status', function () {
        let button = $(this);
        let id = button.data('id');
        let row = button.closest('tr');

        $.ajax({
            url: '/transaksi/' + id + '/toggle-status',
            method: 'POST',
            success: function (response) {
                let newStatus = response.new_status;

                // Update tampilan tombol
                button.text(newStatus == 1 ? 'Non Aktif' : 'Aktifkan');

                // Update warna baris
                if (newStatus == 0) {
                    row.addClass('table-danger');
                } else {
                    row.removeClass('table-danger');
                }
            },
            error: function () {
                alert('Gagal mengubah status.');
            }
        });
    });
});

// Handle Import Excel
document.getElementById("importForm").addEventListener("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Yakin ingin mengimpor file?",
        html: `Pastikan data sudah sesuai sebelum melakukan import.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, import!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            const form = e.target;
            const formData = new FormData(form);
            const importBtn = document.getElementById("importBtn");

            importBtn.disabled = true;
            importBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses Import...';

            const xhr = new XMLHttpRequest();
            xhr.open("POST", form.action, true);
            xhr.setRequestHeader(
                "X-CSRF-TOKEN",
                document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content")
            );

            xhr.onload = function () {
                const data = JSON.parse(xhr.responseText);
                if (xhr.status >= 200 && xhr.status < 300 && data.success) {
                    form.reset();
                    $("#importExcelModal").modal("hide");

                    showToast("Data berhasil diimpor!", "success");

                    $("#transaksiTable").DataTable().ajax.reload();
                } else {
                    const message =
                        data.message || "Terjadi kesalahan saat mengimport file.";
                    Swal.fire({
                        title: "Error!",
                        text: message,
                        icon: "error",
                        confirmButtonColor: "#d33",
                    });
                    showToast("Error: " + message, "danger");
                }
            };

            xhr.onerror = function () {
                Swal.fire({
                    title: "Error!",
                    text: "Gagal terhubung ke server.",
                    icon: "error",
                    confirmButtonColor: "#d33",
                });
                showToast("Gagal terhubung ke server.", "danger");
            };

            xhr.onloadend = function () {
                importBtn.disabled = false;
                importBtn.innerHTML = '<i class="fa fa-upload"></i> Import';
            };

            xhr.send(formData);
        }
    });
});

$(document).ready(function () {
    $('#transaksiModal').on('shown.bs.modal', function () {
        // Bind ulang setiap kali modal dibuka
        $('#checkAssetList').off('change').on('change', function () {
            if ($(this).is(':checked')) {
                $('#selectBarangContainer').slideDown();

                // Load data hanya jika belum ada
                if ($('#barang_id option').length === 1) {
                    $.ajax({
                        url: '/api/barang',
                        method: 'GET',
                        success: function (data) {
                            data.forEach(function (item) {
                                $('#barang_id').append(`<option value="${item.id}">${item.nama_barang}</option>`);
                            });
                        },
                        error: function () {
                            alert('Gagal memuat data barang.');
                        }
                    });
                }

            } else {
                $('#selectBarangContainer').slideUp();
                $('#barang_id').val('');
            }
        });
    });

    // Reset saat modal ditutup (opsional)
    $('#transaksiModal').on('hidden.bs.modal', function () {
        $('#checkAssetList').prop('checked', false);
        $('#selectBarangContainer').hide();
        $('#barang_id').empty().append('<option value="">-- Pilih Barang --</option>');
    });
});

document.getElementById('btn-download-template').addEventListener('click', function () {
    const spinner = document.getElementById('spinner');
    const url = this.getAttribute('data-url');

    spinner.classList.remove('d-none');

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengunduh file');
            return response.blob();
        })
        .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = 'template_arus_kas_bimmo.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(downloadUrl);
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan saat mengunduh template.');
        })
        .finally(() => {
            spinner.classList.add('d-none');
        });
});