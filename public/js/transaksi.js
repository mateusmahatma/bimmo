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
        scrollX: true,
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
                className: "text-left",
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
                className: "text-left",
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



            {
                data: "created_at",
                render: function (data) {
                    var formattedTimestamp = moment(data).format(
                        "YYYY-MM-DD HH:mm:ss"
                    );
                    return (
                        "<span class>" + formattedTimestamp + "<br>" + "</span>"
                    );
                },
                className: "text-center",
            },

            {
                data: "updated_at",
                render: function (data) {
                    var formattedTimestamp = moment(data).format(
                        "YYYY-MM-DD HH:mm:ss"
                    );
                    return (
                        "<span class>" + formattedTimestamp + "<br>" + "</span>"
                    );
                },
                className: "text-center",
            },

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

// Create
document.addEventListener("DOMContentLoaded", function () {
    const btnSimpan = document.getElementById("btnSimpan");
    const form = document.getElementById("formTransaksi");
    const btnSpinner = document.getElementById("btnSpinner");
    const btnText = document.getElementById("btnText");
    const modalElement = document.getElementById("transaksiModal");

    if (!btnSimpan || !form || !btnSpinner || !btnText || !modalElement) {
        console.error("Elemen yang diperlukan tidak ditemukan");
        return;
    }

    const modal =
        bootstrap.Modal.getInstance(modalElement) ||
        new bootstrap.Modal(modalElement);

    btnSimpan.addEventListener("click", async function () {
        let formData = new FormData(form);
        btnSpinner.classList.remove("d-none");
        btnText.innerHTML = "Proses...";
        btnSimpan.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            if (!response.ok) throw new Error("Network response was not ok");

            const data = await response.json();

            if (data.success) {
                form.reset();
                modal.hide();
                $("#transaksiTable").DataTable().ajax.reload();

                Swal.fire({
                    icon: "success",
                    title: "Data berhasil disimpan",
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
            } else if (data.errors) {
                const errorMessages = Object.values(data.errors)
                    .flat()
                    .join("\n");
                Swal.fire({
                    icon: "error",
                    title: "Validasi Gagal",
                    text: errorMessages,
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: data.message || "Gagal menyimpan data.",
                });
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Cek data Anda kembali",
            });
        } finally {
            btnSpinner.classList.add("d-none");
            btnText.innerHTML = '<i class="fa fa-paper-plane"></i> Simpan';
            btnSimpan.disabled = false;
        }
    });
});

// Edit
$("body").on("click", ".tombol-edit-transaksi", function (e) {
    var id = $(this).data("id");

    $.ajax({
        url: "transaksi/" + id + "/edit",
        type: "GET",
        success: function (response) {
            // Menampilkan modal
            $("#editTransaksiModal").modal("show");

            // Mengisi data ke dalam modal
            var tgl_transaksi = response.result.tgl_transaksi;
            if (tgl_transaksi) {
                var tanggal = new Date(tgl_transaksi);
                var year = tanggal.getFullYear();
                var month = (tanggal.getMonth() + 1)
                    .toString()
                    .padStart(2, "0");
                var day = tanggal.getDate().toString().padStart(2, "0");
                tgl_transaksi = `${year}-${month}-${day}`;
            }
            $("#edit_tgl_transaksi").val(tgl_transaksi);
            $("#edit_pemasukan").val(response.result.pemasukan);
            $("#edit_nominal_pemasukan").val(response.result.nominal_pemasukan);
            $("#edit_pengeluaran").val(response.result.pengeluaran);
            $("#edit_nominal").val(response.result.nominal);
            $("#edit_keterangan").val(response.result.keterangan);

            // Menyesuaikan action form untuk update
            var form = $("#editTransaksiForm");
            form.attr("action", "transaksi/" + id); // Pastikan URL sudah sesuai
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Gagal memuat data untuk edit",
            });
        },
    });
});

// Update
$("body").on("submit", "#editTransaksiForm", function (e) {
    e.preventDefault(); // Mencegah submit default form

    var form = $(this);
    var url = form.attr("action"); // URL update
    var formData = form.serialize(); // Mengambil data dari form
    var submitButton = form.find("button[type='submit']"); // Tombol submit

    // Ubah tombol menjadi spinner dan teks "Proses..."
    submitButton
        .html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'
        )
        .prop("disabled", true);

    $.ajax({
        url: url,
        type: "PUT", // Method untuk update
        data: formData,
        success: function (response) {
            // Tutup modal
            $("#editTransaksiModal").modal("hide");

            // Tampilkan SweetAlert setelah modal ditutup
            setTimeout(() => {
                Swal.fire({
                    icon: "success",
                    title: "Data berhasil diupdate",
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

                // Reload tabel data
                $("#transaksiTable").DataTable().ajax.reload(null, false);
            }, 300); // Beri jeda agar animasi modal selesai
        },
        error: function (xhr, status, error) {
            console.error("Error updating data:", error);

            // Tampilkan pesan error
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Gagal memperbarui data transaksi",
            });
        },
        complete: function () {
            // Kembalikan tombol ke keadaan semula
            submitButton.html("Update").prop("disabled", false);
        },
    });
});

// Delete
$("body").on("click", ".tombol-del-transaksi", function (e) {
    e.preventDefault();
    var toastMixin = Swal.mixin({
        toast: true,
        icon: "success",
        title: "General Title",
        animation: false,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

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
                    toastMixin.fire({
                        animation: true,
                        title: "Data Berhasil dihapus",
                        customClass: {
                            title: "swal2-title-create",
                            popup: "swal2-popup-create",
                        },
                        iconColor: "#ffffff",
                    });
                    $("#transaksiTable").DataTable().ajax.reload();
                },
                error: function () {
                    $("#toastTransaksi").text("Data Gagal dihapus");
                    $(".toast").addClass("bg-danger");
                    $(".toast").toast("show");
                    $("#transaksiTable").DataTable().ajax.reload();
                },
            });
        }
    });
});

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
            const uploadStatus = document.getElementById("uploadStatus");

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

                    Swal.fire({
                        icon: "success",
                        title: "Data berhasil diimport",
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

                    $("#transaksiTable").DataTable().ajax.reload();
                    uploadStatus.innerHTML = `<div class="alert alert-success">Data berhasil diimpor!</div>`;
                } else {
                    const message =
                        data.message ||
                        "Terjadi kesalahan saat mengimport file.";
                    Swal.fire({
                        title: "Error!",
                        text: message,
                        icon: "error",
                        confirmButtonColor: "#d33",
                    });
                    uploadStatus.innerHTML = `<div class="alert alert-danger">Error: ${message}</div>`;
                }
            };

            xhr.onerror = xhr.onloadend = function () {
                if (xhr.status < 200 || xhr.status >= 300) {
                    Swal.fire({
                        title: "Error!",
                        text: "Terjadi kesalahan pada server.",
                        icon: "error",
                        confirmButtonColor: "#d33",
                    });
                    uploadStatus.innerHTML = `<div class="alert alert-danger">Error uploading file!</div>`;
                }
                importBtn.disabled = false;
                importBtn.innerHTML = '<i class="fa fa-upload"></i> Import';
            };

            xhr.send(formData);
        }
    });
});
