$(document).ready(function () {
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
            applyClass: "dark-mode",
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
            transaksiTable.draw();
        }
    );

    var transaksiTable = $("#transaksiTable").DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        serverSide: true,
        processing: true,
        language: {
            processing:
                '<div class="loader-container"><div class="loader"></div></div>',
        },
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
                d.pemasukan = $('select[name="filter_pemasukan"]').val();
                d.pengeluaran = $('select[name="filter_pengeluaran"]').val();
            },
            dataSrc: function (json) {
                $("#totalPemasukan").text(
                    json.totalPemasukan.toLocaleString("id-ID")
                );
                $("#totalPengeluaran").text(
                    json.totalPengeluaran.toLocaleString("id-ID")
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
                className: "text-center",
                render: function (data, type, row) {
                    var date = new Date(data);
                    return date.toLocaleDateString("id-ID", {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    });
                },
            },
            {
                data: "pemasukan",
                className: "text-center",
                render: function (data, type, row) {
                    return data ? data : "-";
                },
            },
            {
                data: "nominal_pemasukan",
                className: "text-center",
                render: function (data, type, row) {
                    var numericValue = parseFloat(data);
                    if (!isNaN(numericValue)) {
                        var formattedNominal =
                            numericValue.toLocaleString("id-ID");
                        return (
                            '<span style="color: green;">' +
                            formattedNominal +
                            "</span>"
                        );
                    } else {
                        return '<span style="color: green;">0</span>';
                    }
                },
            },
            {
                data: "pengeluaran",
                className: "text-center",
                render: function (data, type, row) {
                    return data ? data : "-";
                },
            },

            {
                data: "nominal",
                className: "text-center",
                render: function (data, type, row) {
                    var numericValue = parseFloat(data);

                    if (!isNaN(numericValue)) {
                        var formattedNominal =
                            numericValue.toLocaleString("id-ID");
                        return (
                            '<span style="color: red;">' +
                            formattedNominal +
                            "</span>"
                        );
                    } else {
                        return '<span style="color: red;">0</span>';
                    }
                },
            },

            {
                data: "keterangan",
                className: "text-left",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (data && typeof data === "string") {
                            var lines = data.split("\n");
                            var table = `
                    <table style="width: 100%; border-collapse: collapse;">
                        <colgroup>
                            <col style="width: 30px;">
                            <col>
                        </colgroup>`;

                            lines.forEach(function (line, index) {
                                table += `
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${index + 1}</td>
                            <td style="border: 1px solid #ddd; padding: 4px;">${line}</td>
                        </tr>`;
                            });

                            table += "</table>";
                            return table;
                        } else {
                            return "-";
                        }
                    }
                    return data ? data : "-";
                },
            },
            { data: 'created_at', render: data => moment(data).format('D MMMM YYYY HH:mm:ss'), className: 'text-center' },
            { data: 'updated_at', render: data => moment(data).format('D MMMM YYYY HH:mm:ss'), className: 'text-center' },

            {
                data: "aksi",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
        ],
    });
    $('select[name="filter_pemasukan"]').on("change", function () {
        transaksiTable.ajax.reload();
    });

    $('select[name="filter_pengeluaran"]').on("change", function () {
        transaksiTable.ajax.reload();
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
    function refreshTable() {
        // Tambahkan loading spinner ke tabel
        const tableContainer = document.querySelector('.table-responsive');
        if (tableContainer) {
            // Tambahkan overlay loading jika belum ada
            let loadingOverlay = tableContainer.querySelector('.loading-overlay');
            if (!loadingOverlay) {
                loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay';
                loadingOverlay.innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;
                tableContainer.style.position = 'relative';
                loadingOverlay.style.position = 'absolute';
                loadingOverlay.style.top = '0';
                loadingOverlay.style.left = '0';
                loadingOverlay.style.width = '100%';
                loadingOverlay.style.height = '100%';
                loadingOverlay.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
                loadingOverlay.style.display = 'flex';
                loadingOverlay.style.alignItems = 'center';
                loadingOverlay.style.zIndex = '10';
                tableContainer.appendChild(loadingOverlay);
            } else {
                loadingOverlay.style.display = 'flex';
            }

            // Fetch data terbaru dan update tabel
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Extract tabel dari response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.table-responsive table');

                    // Update tabel dengan data baru
                    if (newTable) {
                        const currentTable = tableContainer.querySelector('table');
                        if (currentTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                        }
                    }

                    // Sembunyikan loading overlay
                    loadingOverlay.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error refreshing table:', error);
                    loadingOverlay.style.display = 'none';
                    showToast('Gagal memperbarui tabel: ' + error.message, 'danger');
                });
        }
    }

    // Event listener untuk button simpan
    btnSimpan.addEventListener('click', function (event) {
        event.preventDefault();

        // Validasi form
        if (!validateForm()) {
            return;
        }

        // Menampilkan spinner dan menonaktifkan button
        btnSpinner.classList.remove('d-none');
        btnText.textContent = ' Menyimpan...';
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
$("body").on("click", ".tombol-edit-transaksi", function (e) {
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
        success: function (response) {
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

document.addEventListener("DOMContentLoaded", function () {
    const darkModeDropdown = document.getElementById("darkModeDropdown");

    const storedMode = localStorage.getItem("darkMode");
    const isDarkMode = storedMode === "enabled";

    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.value = "dark";
    }

    darkModeDropdown.addEventListener("change", function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === "dark") {
            enableDarkMode();
            localStorage.setItem("darkMode", "enabled");
        } else {
            disableDarkMode();
            localStorage.setItem("darkMode", null);
        }
    });

    function enableDarkMode() {
        document.body.classList.add("dark-mode");
    }

    function disableDarkMode() {
        document.body.classList.remove("dark-mode");
    }
});

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
                    url: "/upload",
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

