/**
 * =====================================================
 * TRANSAKSI.JS - Refactored Version
 * =====================================================
 * File ini mengelola semua fungsi terkait halaman transaksi
 * Menggunakan struktur modular dan best practices
 */

// =====================================================
// CONFIGURATION & INITIALIZATION
// =====================================================
const TransaksiConfig = {
    urls: {
        base: '/transaksi',
        create: '/transaksi',
        edit: (id) => `/transaksi/${id}/edit`,
        update: (id) => `/transaksi/${id}`,
        delete: (id) => `/transaksi/${id}`,
        toggleStatus: (id) => `/transaksi/${id}/toggle-status`,
        upload: '/transaksi/upload',
        import: '/transaksi/import',
        downloadPdf: '/transaksi/cetak_pdf',
        downloadExcel: '/transaksi/download-excel',
        downloadTemplate: '/transaksi/download-template',
        apiBarang: '/api/barang'
    },
    selectors: {
        table: '#transaksiTable',
        daterange: '#daterange',
        filterPemasukan: 'select[name="filter_pemasukan"]',
        filterPengeluaran: 'select[name="filter_pengeluaran"]'
    }
};

// =====================================================
// THEME HANDLER
// =====================================================
const ThemeHandler = {
    init() {
        const skin = window.userSkin || 'auto';
        const updateSkinUrl = window.updateSkinUrl;
        const csrfToken = window.csrfToken;

        this.applyTheme(skin);
        this.highlightActiveSkin(skin);
        window.setTheme = this.setTheme.bind(this);
    },

    applyTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', mode);
        } else {
            document.documentElement.removeAttribute('data-bs-theme');
        }
        document.dispatchEvent(new Event("themeChanged"));
    },

    highlightActiveSkin(mode) {
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.classList.remove('active');
            if (el.getAttribute('onclick') === `setTheme('${mode}')`) {
                el.classList.add('active');
            }
        });
    },

    setTheme(mode) {
        this.applyTheme(mode);
        this.highlightActiveSkin(mode);

        fetch(window.updateSkinUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": window.csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ skin: mode })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert("Gagal menyimpan tema.");
                }
            })
            .catch(err => console.error("Gagal update tema:", err));
    }
};

// =====================================================
// DATERANGE PICKER HANDLER
// =====================================================
const DateRangeHandler = {
    startDate: null,
    endDate: null,

    init() {
        const today = moment();
        this.startDate = today.clone().startOf("day");
        this.endDate = today.clone().endOf("day");

        this.updateDisplay();
        this.initPicker();
    },

    updateDisplay() {
        $("#daterange span").html(
            this.startDate.format("YYYY-MM-DD") + " - " + this.endDate.format("YYYY-MM-DD")
        );
    },

    initPicker() {
        const self = this;
        $("#daterange").daterangepicker(
            {
                startDate: this.startDate,
                endDate: this.endDate,
                ranges: {
                    "Hari Ini": [moment(), moment()],
                    "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Bulan Ini": [moment().startOf("month"), moment().endOf("month")],
                    "Bulan Lalu": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month")
                    ],
                    "Tahun Ini": [moment().startOf("year"), moment().endOf("year")],
                    "Tahun Lalu": [
                        moment().subtract(1, "year").startOf("year"),
                        moment().subtract(1, "year").endOf("year")
                    ],
                    "Semua Data": [moment("2000-01-01"), moment()]
                },
                containerClass: document.documentElement.getAttribute("data-bs-theme") === "dark" ? "dark-mode" : ""
            },
            function (chosenStartDate, chosenEndDate) {
                self.startDate = chosenStartDate;
                self.endDate = chosenEndDate;

                if (chosenStartDate.isSame(moment(), "day") && chosenEndDate.isSame(moment(), "day")) {
                    $("#tgl_transaksi").val(moment().format("yyyy-mm-dd"));
                }

                self.updateDisplay();
                DataTableHandler.table.draw();
            }
        );
    },

    getDateRange() {
        return {
            start_date: this.startDate.format("YYYY-MM-DD"),
            end_date: this.endDate.format("YYYY-MM-DD")
        };
    }
};

// =====================================================
// DATATABLE HANDLER
// =====================================================
const DataTableHandler = {
    table: null,

    init() {
        this.table = $(TransaksiConfig.selectors.table).DataTable({
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            serverSide: true,
            processing: true,
            ajax: {
                url: TransaksiConfig.urls.base,
                type: "GET",
                data: (d) => {
                    const dateRange = DateRangeHandler.getDateRange();
                    d.start_date = dateRange.start_date;
                    d.end_date = dateRange.end_date;
                    d.filter_pemasukan = $(TransaksiConfig.selectors.filterPemasukan).val();
                    d.filter_pengeluaran = $(TransaksiConfig.selectors.filterPengeluaran).val();
                },
                dataSrc: (json) => {
                    this.updateSummary(json);
                    return json.data;
                }
            },
            columns: this.getColumns(),
            createdRow: (row, data) => {
                if (data.status == 0) {
                    $(row).addClass('table-danger');
                } else if (data.status == 2) {
                    $(row).addClass('table-success');
                }
            }
        });

        this.initFilterListeners();
    },

    getColumns() {
        return [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "text-center"
            },
            {
                data: "tgl_transaksi",
                render: (data) => {
                    const date = new Date(data);
                    return date.toLocaleDateString("id-ID", {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric"
                    });
                }
            },
            { data: 'pemasukan_nama', name: 'pemasukan_nama' },
            {
                data: "nominal_pemasukan",
                render: (data) => {
                    const value = parseFloat(data);
                    return `<span>${isNaN(value) ? 0 : value.toLocaleString("id-ID")}</span>`;
                }
            },
            { data: 'pengeluaran_nama', name: 'pengeluaran_nama' },
            {
                data: "nominal",
                render: (data) => {
                    const value = parseFloat(data);
                    return `<span>${isNaN(value) ? 0 : value.toLocaleString("id-ID")}</span>`;
                }
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
            { data: 'created_at' },
            { data: 'updated_at' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false
            }
        ];
    },

    updateSummary(json) {
        $("#totalPemasukan").text(
            json.totalPemasukan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        );
        $("#totalPengeluaran").text(
            json.totalPengeluaran.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        );
        $("#netIncome").text(
            json.netIncome.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        );
    },

    initFilterListeners() {
        $(TransaksiConfig.selectors.filterPemasukan).on("change", () => {
            this.table.ajax.reload();
        });

        $(TransaksiConfig.selectors.filterPengeluaran).on("change", () => {
            this.table.ajax.reload();
        });
    },

    reload() {
        this.table.ajax.reload(null, false);
    }
};

// =====================================================
// TOAST NOTIFICATION HANDLER
// =====================================================
const ToastHandler = {
    colors: {
        success: '#012970',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8',
        primary: '#007bff'
    },

    show(message, type = 'info') {
        let toastContainer = document.querySelector('.toast-container');

        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const bgColor = this.colors[type] || '#6c757d';

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white border-0" 
                 style="background-color: ${bgColor};" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                            data-bs-dismiss="toast" aria-label="Close"></button>
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
};

// Backward compatibility
window.showToast = (message, type) => ToastHandler.show(message, type);

// =====================================================
// TOMSELECT HANDLER
// =====================================================
const TomSelectHandler = {
    instances: {},

    init(selector, options = {}) {
        const defaultOptions = {
            allowEmptyOption: true,
            placeholder: '- Pilih -',
            create: false,
            onInitialize: function () {
                if (!this.getValue()) {
                    this.setTextboxValue('');
                }
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };
        this.instances[selector] = new TomSelect(selector, mergedOptions);
        return this.instances[selector];
    },

    initAll() {
        this.init('#pemasukan');
        this.init('#pengeluaran');
        this.init('#barang_id');
        this.init('#filter_pemasukan');
        this.init('#filter_pengeluaran');
    },

    getInstance(selector) {
        return this.instances[selector];
    }
};

// =====================================================
// TRANSAKSI CRUD HANDLER
// =====================================================
const TransaksiCRUD = {
    // Create Transaksi
    initCreate() {
        const formTransaksi = document.getElementById('formTransaksi');
        const btnSimpan = document.getElementById('btnSimpan');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const transaksiModal = document.getElementById('transaksiModal');

        if (!formTransaksi || !btnSimpan) return;

        btnSimpan.addEventListener('click', (event) => {
            event.preventDefault();

            if (!this.validateForm(formTransaksi)) {
                return;
            }

            btnSpinner.classList.remove('d-none');
            btnSimpan.disabled = true;

            const formData = new FormData(formTransaksi);

            fetch(formTransaksi.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Terjadi kesalahan pada server');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.closeModal('transaksiModal');
                        ToastHandler.show(data.message || 'Data transaksi berhasil disimpan!', 'success');
                        formTransaksi.reset();
                        DataTableHandler.reload();
                    } else {
                        ToastHandler.show(data.message || 'Gagal menyimpan data transaksi!', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ToastHandler.show('Terjadi kesalahan: ' + error.message, 'danger');
                })
                .finally(() => {
                    btnSpinner.classList.add('d-none');
                    btnText.innerHTML = '<i class="fa fa-paper-plane"></i> Simpan';
                    btnSimpan.disabled = false;
                });
        });

        // Modal shown event
        if (transaksiModal) {
            transaksiModal.addEventListener('shown.bs.modal', () => {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('tgl_transaksi').value = today;
                formTransaksi.reset();
                document.getElementById('tgl_transaksi').value = today;

                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fa fa-paper-plane"></i> Simpan';
                btnSimpan.disabled = false;
            });
        }

        // Cross validation between pemasukan and pengeluaran
        this.initCrossValidation();
    },

    validateForm(form) {
        const tglTransaksi = form.querySelector('#tgl_transaksi').value;
        const pemasukan = form.querySelector('#pemasukan').value;
        const pengeluaran = form.querySelector('#pengeluaran').value;
        const nominalPemasukan = form.querySelector('#nominal_pemasukan').value;
        const nominal = form.querySelector('#nominal').value;

        if (!tglTransaksi) {
            ToastHandler.show('Tanggal transaksi harus diisi!', 'danger');
            return false;
        }

        if (!pemasukan && !pengeluaran) {
            ToastHandler.show('Anda harus mengisi minimal satu dari pemasukan atau pengeluaran!', 'danger');
            return false;
        }

        if (pemasukan && !nominalPemasukan) {
            ToastHandler.show('Nominal pemasukan harus diisi!', 'danger');
            return false;
        }

        if (pengeluaran && !nominal) {
            ToastHandler.show('Nominal pengeluaran harus diisi!', 'danger');
            return false;
        }

        return true;
    },

    initCrossValidation() {
        const pemasukanSelect = document.getElementById('pemasukan');
        const pengeluaranSelect = document.getElementById('pengeluaran');

        if (pemasukanSelect) {
            pemasukanSelect.addEventListener('change', function () {
                if (this.value && pengeluaranSelect.value) {
                    ToastHandler.show('Disarankan mengisi salah satu saja: pemasukan atau pengeluaran', 'warning');
                }
            });
        }

        if (pengeluaranSelect) {
            pengeluaranSelect.addEventListener('change', function () {
                if (this.value && pemasukanSelect.value) {
                    ToastHandler.show('Disarankan mengisi salah satu saja: pemasukan atau pengeluaran', 'warning');
                }
            });
        }
    },

    // Edit Transaksi
    initEdit() {
        $("body").on("click", ".tombol-edit-transaksi", function () {
            const id = $(this).data("id");

            $.ajax({
                url: TransaksiConfig.urls.edit(id),
                type: "GET",
                success: function (response) {
                    $("#editTransaksiModal").modal("show");

                    let tgl_transaksi = response.result.tgl_transaksi;
                    if (tgl_transaksi) {
                        const tanggal = new Date(tgl_transaksi);
                        const year = tanggal.getFullYear();
                        const month = (tanggal.getMonth() + 1).toString().padStart(2, "0");
                        const day = tanggal.getDate().toString().padStart(2, "0");
                        tgl_transaksi = `${year}-${month}-${day}`;
                    }

                    $("#edit_tgl_transaksi").val(tgl_transaksi);
                    $("#edit_pemasukan").val(response.result.pemasukan);
                    $("#edit_nominal_pemasukan").val(response.result.nominal_pemasukan);
                    $("#edit_pengeluaran").val(response.result.pengeluaran);
                    $("#edit_nominal").val(response.result.nominal);
                    $("#edit_keterangan").val(response.result.keterangan);

                    const form = $("#editTransaksiForm");
                    form.attr("action", TransaksiConfig.urls.update(id));
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                    ToastHandler.show("Gagal memuat data untuk edit", "danger");
                }
            });
        });
    },

    // Update Transaksi
    initUpdate() {
        $("body").on("submit", "#editTransaksiForm", function (e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr("action");
            const formData = form.serialize();
            const submitButton = form.find("button[type='submit']");

            $.ajax({
                url: url,
                type: "PUT",
                data: formData,
                success: function () {
                    $("#editTransaksiModal").modal("hide");

                    setTimeout(() => {
                        ToastHandler.show("Data berhasil diupdate", "success");
                        DataTableHandler.reload();
                    }, 300);
                },
                error: function (xhr, status, error) {
                    console.error("Error updating data:", error);
                    ToastHandler.show("Gagal memperbarui data transaksi", "danger");
                },
                complete: function () {
                    submitButton.html("Simpan").prop("disabled", false);
                }
            });
        });
    },

    // Delete Transaksi
    initDelete() {
        $("body").on("click", ".tombol-del-transaksi", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Yakin mau hapus data ini?",
                html: `Data yang dihapus tidak dapat dikembalikan!`,
                showCancelButton: true,
                confirmButtonColor: "var(--bs-danger)",
                cancelButtonColor: "var(--bs-primary)",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const id = $(this).data("id");

                    $.ajax({
                        url: TransaksiConfig.urls.delete(id),
                        type: "DELETE",
                        success: function () {
                            ToastHandler.show("Data berhasil dihapus", "success");
                            DataTableHandler.reload();
                        },
                        error: function () {
                            ToastHandler.show("Data gagal dihapus", "danger");
                            DataTableHandler.reload();
                        }
                    });
                }
            });
        });
    },

    // Toggle Status
    initToggleStatus() {
        $('#transaksiTable').on('click', '.tombol-toggle-status', function () {
            const button = $(this);
            const id = button.data('id');
            const row = button.closest('tr');

            $.ajax({
                url: TransaksiConfig.urls.toggleStatus(id),
                method: 'POST',
                success: function (response) {
                    const newStatus = response.new_status;

                    button.text(newStatus == 1 ? 'Non Aktif' : 'Aktifkan');

                    if (newStatus == 0) {
                        row.addClass('table-danger');
                    } else {
                        row.removeClass('table-danger');
                    }
                },
                error: function () {
                    ToastHandler.show('Gagal mengubah status.', 'danger');
                }
            });
        });
    },

    closeModal(modalId) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalElement = document.getElementById(modalId);
                const modalObj = bootstrap.Modal.getInstance(modalElement);
                if (modalObj) {
                    modalObj.hide();
                } else {
                    $(`#${modalId}`).modal('hide');
                }
            } else {
                $(`#${modalId}`).modal('hide');
            }
        } catch (err) {
            console.error('Error closing modal:', err);
            const modalElement = document.getElementById(modalId);
            const closeButton = modalElement.querySelector('.btn-close') ||
                modalElement.querySelector('[data-bs-dismiss="modal"]');
            if (closeButton) closeButton.click();
        }
    }
};

// =====================================================
// DOWNLOAD HANDLER
// =====================================================
const DownloadHandler = {
    downloadPDF() {
        const dateRange = DateRangeHandler.getDateRange();

        Swal.fire({
            title: "Sedang mendownload...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: TransaksiConfig.urls.downloadPdf,
            type: "GET",
            data: dateRange,
            xhrFields: {
                responseType: "blob"
            },
            success: function (data) {
                const a = document.createElement("a");
                const url = window.URL.createObjectURL(data);

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
                    timer: 1500
                });
            },
            error: function (xhr, status, error) {
                console.error("Error downloading file:", error);
                Swal.fire({
                    icon: "error",
                    title: "Terjadi kesalahan!",
                    text: "Kesalahan saat mendownload file."
                });
            }
        });
    },

    downloadExcel() {
        const dateRange = DateRangeHandler.getDateRange();

        Swal.fire({
            title: "Sedang mendownload...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: TransaksiConfig.urls.downloadExcel,
            type: "GET",
            data: dateRange,
            xhrFields: {
                responseType: "blob"
            },
            success: function (data) {
                const a = document.createElement("a");
                const url = window.URL.createObjectURL(data);

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
                    timer: 1500
                });
            },
            error: function (xhr, status, error) {
                console.error("Error downloading file:", error);
                Swal.fire({
                    icon: "error",
                    title: "Terjadi kesalahan!",
                    text: "Kesalahan saat mendownload file."
                });
            }
        });
    },

    downloadTemplate() {
        const btn = document.getElementById('btn-download-template');
        const spinner = document.getElementById('spinner');
        const url = btn.getAttribute('data-url');

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
                ToastHandler.show('Terjadi kesalahan saat mengunduh template.', 'danger');
            })
            .finally(() => {
                spinner.classList.add('d-none');
            });
    }
};

// Expose to global
window.downloadPDFTransaksi = () => DownloadHandler.downloadPDF();
window.downloadExcel = () => DownloadHandler.downloadExcel();

// =====================================================
// UPLOAD HANDLER
// =====================================================
const UploadHandler = {
    init() {
        $("body").on("click", ".tombol-upload-file", function () {
            const transaksiId = $(this).data("id");
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
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    const submitButton = $(this).find('button[type="submit"]');

                    submitButton
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses Upload...')
                        .prop("disabled", true);

                    $.ajax({
                        url: TransaksiConfig.urls.upload,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                $("#uploadModal").modal("hide");
                                DataTableHandler.reload();

                                Swal.fire({
                                    icon: "success",
                                    title: "Data Berhasil di Upload",
                                    timer: 3000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: "top",
                                    customClass: {
                                        title: "swal2-title-create",
                                        popup: "swal2-popup-create"
                                    },
                                    iconColor: "#ffffff"
                                });

                                $("#uploadForm")[0].reset();
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error!",
                                    text: response.message || "Upload gagal.",
                                    confirmButtonColor: "#d33"
                                });
                            }
                        },
                        error: function (xhr) {
                            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : "Terjadi kesalahan saat upload.";
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: errorMessage,
                                confirmButtonColor: "#d33"
                            });
                        },
                        complete: function () {
                            submitButton
                                .prop("disabled", false)
                                .html('<i class="fa fa-upload"></i> Upload');
                        }
                    });
                }
            });
        });
    }
};

// =====================================================
// IMPORT HANDLER
// =====================================================
const ImportHandler = {
    init() {
        const importForm = document.getElementById("importForm");

        if (!importForm) return;

        importForm.addEventListener("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Yakin ingin mengimpor file?",
                html: `Pastikan data sudah sesuai sebelum melakukan import.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, import!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = e.target;
                    const formData = new FormData(form);
                    const importBtn = document.getElementById("importBtn");

                    importBtn.disabled = true;

                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", form.action, true);
                    xhr.setRequestHeader(
                        "X-CSRF-TOKEN",
                        document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    );

                    xhr.onload = function () {
                        const data = JSON.parse(xhr.responseText);
                        if (xhr.status >= 200 && xhr.status < 300 && data.success) {
                            form.reset();
                            $("#importExcelModal").modal("hide");

                            ToastHandler.show("Data berhasil diimpor!", "success");
                            DataTableHandler.reload();
                        } else {
                            const message = data.message || "Terjadi kesalahan saat mengimport file.";
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonColor: "#d33"
                            });
                            ToastHandler.show("Error: " + message, "danger");
                        }
                    };

                    xhr.onerror = function () {
                        Swal.fire({
                            title: "Error!",
                            text: "Gagal terhubung ke server.",
                            icon: "error",
                            confirmButtonColor: "#d33"
                        });
                        ToastHandler.show("Gagal terhubung ke server.", "danger");
                    };

                    xhr.onloadend = function () {
                        importBtn.disabled = false;
                        importBtn.innerHTML = '<i class="fa fa-upload"></i> Import';
                    };

                    xhr.send(formData);
                }
            });
        });
    }
};

// =====================================================
// ASSET LIST HANDLER
// =====================================================
const AssetListHandler = {
    init() {
        $('#transaksiModal').on('shown.bs.modal', function () {
            $('#checkAssetList').off('change').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#selectBarangContainer').slideDown();

                    if ($('#barang_id option').length === 1) {
                        $.ajax({
                            url: TransaksiConfig.urls.apiBarang,
                            method: 'GET',
                            success: function (data) {
                                data.forEach(function (item) {
                                    $('#barang_id').append(`<option value="${item.id}">${item.nama_barang}</option>`);
                                });
                            },
                            error: function () {
                                ToastHandler.show('Gagal memuat data barang.', 'danger');
                            }
                        });
                    }
                } else {
                    $('#selectBarangContainer').slideUp();
                    $('#barang_id').val('');
                }
            });
        });

        $('#transaksiModal').on('hidden.bs.modal', function () {
            $('#checkAssetList').prop('checked', false);
            $('#selectBarangContainer').hide();
            $('#barang_id').empty().append('<option value="">-- Pilih Barang --</option>');
        });
    }
};

// =====================================================
// LAPORAN TOGGLE HANDLER
// =====================================================
const LaporanToggleHandler = {
    init() {
        const toggleBtn = document.getElementById("toggleBtn");
        const toggleIcon = document.getElementById("toggleIcon");
        const laporanContent = document.getElementById("laporanContent");

        if (!toggleBtn || !laporanContent) return;

        toggleBtn.addEventListener("click", () => {
            const isHidden = laporanContent.style.display === "none";
            laporanContent.style.display = isHidden ? "block" : "none";
            toggleIcon.textContent = isHidden ? "−" : "+";
        });

        // Set initial state
        laporanContent.style.display = "none";
    }
};

// =====================================================
// UTILITY FUNCTIONS
// =====================================================
const Utils = {
    validasiInput(inputElement) {
        inputElement.value = inputElement.value.replace(/[^0-9]/g, "");
    }
};

// Expose to global
window.validasiInput = (inputElement) => Utils.validasiInput(inputElement);

// =====================================================
// MAIN INITIALIZATION
// =====================================================
$(document).ready(function () {
    // Setup AJAX CSRF Token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    // Initialize all modules
    ThemeHandler.init();
    DateRangeHandler.init();
    DataTableHandler.init();
    TransaksiCRUD.initCreate();
    TransaksiCRUD.initEdit();
    TransaksiCRUD.initUpdate();
    TransaksiCRUD.initDelete();
    TransaksiCRUD.initToggleStatus();
    UploadHandler.init();
    ImportHandler.init();
    AssetListHandler.init();
    LaporanToggleHandler.init();

    // Download template handler
    const downloadTemplateBtn = document.getElementById('btn-download-template');
    if (downloadTemplateBtn) {
        downloadTemplateBtn.addEventListener('click', () => DownloadHandler.downloadTemplate());
    }
});

// Initialize TomSelect after DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    TomSelectHandler.initAll();
});