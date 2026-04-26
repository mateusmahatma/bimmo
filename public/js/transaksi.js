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
        // Theme handled globally
        // const skin = window.userSkin || 'auto';
        // const updateSkinUrl = window.updateSkinUrl;
        // const csrfToken = window.csrfToken;

        // this.applyTheme(skin);
        // this.highlightActiveSkin(skin);
        // window.setTheme = this.setTheme.bind(this);
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
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
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
        if (this.instances[selector]) {
            this.instances[selector].destroy();
        }

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
        this.init('#edit_pemasukan');
        this.init('#edit_pengeluaran');
    },

    getInstance(selector) {
        return this.instances[selector];
    },

    setValue(selector, value) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.setValue(value);
        } else {
            $(selector).val(value);
        }
    }
};

// =====================================================
// TRANSAKSI CRUD HANDLER
// =====================================================
let createEditor;
let editEditor;

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
            if (createEditor) {
                formData.set('keterangan', createEditor.getData());
            }

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
                        if (createEditor) createEditor.setData('');
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
            if (document.querySelector('#keterangan') && !createEditor) {
                ClassicEditor.create(document.querySelector('#keterangan'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote']
                }).then(editor => { createEditor = editor; });
            }

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
                    TomSelectHandler.setValue("#edit_pemasukan", response.result.pemasukan);
                    $("#edit_nominal_pemasukan").val(response.result.nominal_pemasukan);
                    TomSelectHandler.setValue("#edit_pengeluaran", response.result.pengeluaran);
                    $("#edit_nominal").val(response.result.nominal);

                    if (document.querySelector('#edit_keterangan') && !editEditor) {
                        ClassicEditor.create(document.querySelector('#edit_keterangan'), {
                            toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote']
                        }).then(editor => {
                            editEditor = editor;
                            editEditor.setData(response.result.keterangan || '');
                        });
                    } else if (editEditor) {
                        editEditor.setData(response.result.keterangan || '');
                    } else {
                        $("#edit_keterangan").val(response.result.keterangan);
                    }

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
            let formData = form.serializeArray();

            if (editEditor) {
                // Replace keterangan in serialized array
                formData = formData.filter(item => item.name !== 'keterangan');
                formData.push({ name: 'keterangan', value: editEditor.getData() });
            }

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

            window.confirmAction({
                title: "Are you sure?",
                text: "Deleted data cannot be recovered!",
                onConfirm: async () => {
                    const id = $(this).data("id");

                    try {
                        await $.ajax({
                            url: TransaksiConfig.urls.delete(id),
                            type: "DELETE"
                        });
                        ToastHandler.show("Data successfully deleted", "success");
                        DataTableHandler.reload();
                    } catch (e) {
                        ToastHandler.show("Failed to delete data", "danger");
                        DataTableHandler.reload();
                    }
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

        // Simple loading toast
        ToastHandler.show("Downloading PDF...", "info");

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

                ToastHandler.show("File successfully downloaded!", "success");
            },
            error: function (xhr, status, error) {
                console.error("Error downloading file:", error);
                ToastHandler.show("Failed to download file.", "danger");
            }
        });
    },

    downloadExcel() {
        const dateRange = DateRangeHandler.getDateRange();

        ToastHandler.show("Downloading Excel...", "info");

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

                ToastHandler.show("File successfully downloaded!", "success");
            },
            error: function (xhr, status, error) {
                console.error("Error downloading file:", error);
                ToastHandler.show("Failed to download file.", "danger");
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

            window.confirmAction({
                title: "Upload file?",
                text: "Make sure the file is correct before uploading.",
                onConfirm: async () => {
                    const formData = new FormData(this);
                    const submitButton = $(this).find('button[type="submit"]');

                    submitButton
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
                        .prop("disabled", true);

                    try {
                        const response = await $.ajax({
                            url: TransaksiConfig.urls.upload,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false
                        });

                        if (response.success) {
                            $("#uploadModal").modal("hide");
                            DataTableHandler.reload();
                            ToastHandler.show("File uploaded successfully", "success");
                            $("#uploadForm")[0].reset();
                        } else {
                            ToastHandler.show(response.message || "Upload failed.", "danger");
                        }
                    } catch (xhr) {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : "Error during upload.";
                        ToastHandler.show(errorMessage, "danger");
                    } finally {
                        submitButton
                            .prop("disabled", false)
                            .html('<i class="fa fa-upload"></i> Unggah');
                    }
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

            window.confirmAction({
                title: "Import data?",
                text: "Make sure the file format is correct.",
                onConfirm: async () => {
                    const form = e.target;
                    const formData = new FormData(form);
                    const importBtn = document.getElementById("importBtn");

                    importBtn.disabled = true;
                    importBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                    try {
                        const response = await fetch(form.action, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                            }
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            form.reset();
                            $("#importExcelModal").modal("hide");
                            ToastHandler.show("Data successfully imported!", "success");
                            DataTableHandler.reload();
                        } else {
                            const message = data.message || "Failed to import file.";
                            ToastHandler.show(message, "danger");
                        }
                    } catch (error) {
                        ToastHandler.show("Failed to connect to server.", "danger");
                    } finally {
                        importBtn.disabled = false;
                        importBtn.innerHTML = '<i class="fa fa-upload"></i> Impor';
                    }
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
    // Fungsi ini untuk memvalidasi input angka (hanya mengizinkan 0-9).
    validasiInput(inputElement) {
        inputElement.value = inputElement.value.replace(/[^0-9]/g, "");
    }
};

// Expose to global
window.validasiInput = (inputElement) => Utils.validasiInput(inputElement);

// =====================================================
// TRANSAKSI CREATE PAGE HANDLER
// =====================================================
/**
 * Modul ini untuk halaman `transaksi.create`.
 * Menangani:
 * - Toggle tampilan Assets & Emergency Fund
 * - Segmented control tipe transaksi (income/expense/both)
 * - Inisialisasi CKEditor untuk field keterangan
 * - Validasi minimal 1 tipe transaksi terisi lengkap
 * - Submit form via AJAX + render alert sukses/gagal
 */
const TransaksiCreatePageHandler = (() => {
    // Flag ini untuk mencegah init ganda pada halaman create.
    let isInit = false;

    // Fungsi ini untuk membaca config yang dikirim dari blade lewat `#transaksi-create-config`.
    function getConfig() {
        const el = document.getElementById("transaksi-create-config");
        if (!el || !el.textContent) return {};
        try {
            return JSON.parse(el.textContent);
        } catch (e) {
            console.error("[transaksi.create] gagal parse transaksi-create-config:", e);
            return {};
        }
    }

    // Fungsi ini untuk set mode segmented control dan collapse income/expense.
    function setupTypeToggle() {
        const typeToggleEl = document.querySelector(".transaksi-type-toggle");
        const modeIncomeBtn = document.getElementById("modeIncome");
        const modeExpenseBtn = document.getElementById("modeExpense");
        const modeBothBtn = document.getElementById("modeBoth");
        const pemasukanSectionEl = document.getElementById("pemasukanSection");
        const pengeluaranSectionEl = document.getElementById("pengeluaranSection");

        if (!typeToggleEl || !pemasukanSectionEl || !pengeluaranSectionEl) return;
        if (typeof bootstrap === "undefined") return;

        // Fungsi ini untuk menandai tombol segmented yang aktif.
        const setActiveMode = (mode) => {
            const setActive = (btn, active) => {
                if (!btn) return;
                btn.classList.toggle("active", active);
                btn.setAttribute("aria-pressed", active ? "true" : "false");
            };
            setActive(modeIncomeBtn, mode === "income");
            setActive(modeExpenseBtn, mode === "expense");
            setActive(modeBothBtn, mode === "both");
        };

        // Fungsi ini untuk mengatur collapse + layout kolom sesuai mode yang dipilih.
        const setMode = (mode) => {
    const incomeCollapse = bootstrap.Collapse.getOrCreateInstance(pemasukanSectionEl, { toggle: false });
    const expenseCollapse = bootstrap.Collapse.getOrCreateInstance(pengeluaranSectionEl, { toggle: false });

    // Reset dulu ke col-md-6 sebelum set mode
    pemasukanSectionEl.classList.remove("col-md-6", "col-md-12");
    pengeluaranSectionEl.classList.remove("col-md-6", "col-md-12");

    if (mode === "both") {
        pemasukanSectionEl.classList.add("col-md-6");
        pengeluaranSectionEl.classList.add("col-md-6");
        incomeCollapse.show();
        expenseCollapse.show();
    } else if (mode === "income") {
        pemasukanSectionEl.classList.add("col-md-12");
        pengeluaranSectionEl.classList.add("col-md-12");
        incomeCollapse.show();
        expenseCollapse.hide();
    } else if (mode === "expense") {
        pemasukanSectionEl.classList.add("col-md-12");
        pengeluaranSectionEl.classList.add("col-md-12");
        incomeCollapse.hide();
        expenseCollapse.show();
    }

    setActiveMode(mode);
};

        if (modeIncomeBtn) modeIncomeBtn.addEventListener("click", () => setMode("income"));
        if (modeExpenseBtn) modeExpenseBtn.addEventListener("click", () => setMode("expense"));
        if (modeBothBtn) modeBothBtn.addEventListener("click", () => setMode("both"));

        const initialMode = typeToggleEl.dataset.initialMode || "income";
        setMode(initialMode);
    }

    // Fungsi ini untuk toggle tampilan container assets (pilih barang) berdasarkan checkbox.
    function setupAssetsToggle() {
        const checkAsset = document.getElementById("checkAssetList");
        const assetContainer = document.getElementById("selectBarangContainer");
        if (!checkAsset || !assetContainer) return;

        checkAsset.addEventListener("change", function () {
            assetContainer.style.display = this.checked ? "block" : "none";
        });
    }

    // Fungsi ini untuk toggle tampilan container dana darurat berdasarkan checkbox.
    function setupEmergencyToggle() {
        const checkEmergency = document.getElementById("checkEmergencyFund");
        const emergencyContainer = document.getElementById("danaDaruratContainer");
        if (!checkEmergency || !emergencyContainer) return;

        checkEmergency.addEventListener("change", function () {
            emergencyContainer.style.display = this.checked ? "block" : "none";
        });
    }

    // Fungsi ini untuk inisialisasi CKEditor pada textarea keterangan.
    async function initEditor() {
        if (typeof ClassicEditor === "undefined") return null;
        const el = document.querySelector("#keterangan");
        if (!el) return null;

        try {
            return await ClassicEditor.create(el, {
                toolbar: ["heading", "|", "bold", "italic", "bulletedList", "numberedList", "blockQuote"],
            });
        } catch (e) {
            console.error("[transaksi.create] CKEditor init error:", e);
            return null;
        }
    }

    // Fungsi ini untuk inisialisasi TomSelect untuk income/expense/asset.
    function initTomSelect(placeholders) {
        if (typeof TomSelect === "undefined") return { incSelect: null, expSelect: null };

        const incEl = document.getElementById("pemasukan");
        const expEl = document.getElementById("pengeluaran");
        const assetEl = document.getElementById("barang_id");

        const incSelect = incEl
            ? new TomSelect("#pemasukan", {
                allowEmptyOption: true,
                placeholder: placeholders?.income || "- Select Income -",
            })
            : null;

        const expSelect = expEl
            ? new TomSelect("#pengeluaran", {
                allowEmptyOption: true,
                placeholder: placeholders?.expense || "- Select Expense -",
            })
            : null;

        if (assetEl) {
            new TomSelect("#barang_id", {
                allowEmptyOption: true,
                placeholder: placeholders?.asset || "- Select Asset -",
            });
        }

        return { incSelect, expSelect };
    }

    // Fungsi ini untuk memasang listener submit AJAX + validasi.
    function setupAjaxSubmit(form, editorInstance, selects, cfg) {
        const messages = cfg?.messages || {};
        const incSelect = selects?.incSelect;
        const expSelect = selects?.expSelect;

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            // Fungsi ini untuk membaca nilai input biasa atau dari TomSelect.
            const getVal = (id, ts) => {
                const el = document.getElementById(id);
                if (!el) return "";
                return ts ? ts.getValue() : el.value;
            };

            const nominalPemasukan = document.getElementById("nominal_pemasukan");
            const nominalPengeluaran = document.getElementById("nominal");

            const incomeCat = getVal("pemasukan", incSelect);
            const incomeAmt = nominalPemasukan ? nominalPemasukan.value : "";
            const expenseCat = getVal("pengeluaran", expSelect);
            const expenseAmt = nominalPengeluaran ? nominalPengeluaran.value : "";

            const hasIncome = incomeCat && incomeAmt && parseFloat(incomeAmt) > 0;
            const hasExpense = expenseCat && expenseAmt && parseFloat(expenseAmt) > 0;

            const isIncomePartial = (incomeAmt && parseFloat(incomeAmt) > 0 && !incomeCat) || (!incomeAmt && incomeCat);
            const isExpensePartial =
                (expenseAmt && parseFloat(expenseAmt) > 0 && !expenseCat) || (!expenseAmt && expenseCat);

            // Validasi ini untuk memastikan minimal 1 tipe transaksi terisi lengkap.
            if (!hasIncome && !hasExpense) {
                alert(messages.fillAtLeastOne || "Please fill in at least one transaction type.");
                return;
            }
            if (isIncomePartial) {
                alert(messages.completeIncome || "Please complete the Income section.");
                return;
            }
            if (isExpensePartial) {
                alert(messages.completeExpense || "Please complete the Expense section.");
                return;
            }

            // Ajax submission: disable tombol submit dan tampilkan loading.
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn ? submitBtn.innerHTML : "";
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' +
                    (messages.saving || "Saving...");
            }

            const formData = new FormData(form);
            if (editorInstance) {
                formData.set("keterangan", editorInstance.getData());
            }
            const alertPlaceholder = document.getElementById("alertPlaceholder");

            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": (document.querySelector('input[name="_token"]') || {}).value,
                },
            })
                .then(async (response) => {
                    const contentType = response.headers.get("content-type");
                    const isJson = contentType && contentType.includes("application/json");
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        let errorMsg = (data && data.message) || messages.systemError || "Terjadi kesalahan pada sistem.";

                        // Validasi Laravel 422: gabungkan errors jadi list.
                        if (response.status === 422 && data && data.errors) {
                            const errorList = Object.values(data.errors).flat();
                            errorMsg = errorList.map((msg) => `<li>${msg}</li>`).join("");
                            errorMsg = `<ul class="mb-0 mt-2 ps-3 small text-start">${errorMsg}</ul>`;
                        }

                        throw new Error(errorMsg);
                    }
                    return data;
                })
                .then((data) => {
                    if (!alertPlaceholder) return;

                    // Render alert sukses (dengan tombol link menuju halaman index yang difilter server).
                    alertPlaceholder.innerHTML = `
                        <div class="alert alert-success border-0 p-4 mb-4 d-flex align-items-center justify-content-between text-start scale-in">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-success-light text-success me-3">
                                    <i class="bi bi-check2-circle fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">${(data && data.message) || (messages.successFallback || "Data Berhasil Disimpan!")}</h5>
                                    <p class="mb-0 text-muted small">${messages.processed || ""}</p>
                                </div>
                            </div>
                            <a href="${data.redirect_url}" class="btn btn-success px-4 py-2 fw-bold">
                                <i class="bi bi-eye me-2"></i> ${messages.viewData || "Lihat Data"} ${data.redirect_name || ""}
                            </a>
                        </div>
                    `;

                    // Reset form + komponen UI.
                    form.reset();
                    if (incSelect) incSelect.clear();
                    if (expSelect) expSelect.clear();
                    if (editorInstance) editorInstance.setData("");

                    window.scrollTo({ top: 0, behavior: "smooth" });
                })
                .catch((error) => {
                    console.error("Error:", error);
                    if (!alertPlaceholder) return;

                    alertPlaceholder.innerHTML = `
                        <div class="alert alert-danger border-0 p-4 mb-4 d-flex align-items-center text-start scale-in">
                            <div class="icon-box bg-danger-light text-danger me-3">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">${messages.failSaveTitle || "Gagal Menyimpan"}</h5>
                                <div class="mb-0 text-muted small">${error.message}</div>
                            </div>
                        </div>
                    `;

                    window.scrollTo({ top: 0, behavior: "smooth" });
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                    }
                });
        });
    }

    // Fungsi utama untuk init modul create.
    async function init() {
        if (isInit) return;
        isInit = true;
        setTimeout(() => {
            isInit = false;
        }, 500);

        const form = document.getElementById("transactionForm");
        if (!form) return;

        const cfg = getConfig();

        setupAssetsToggle();
        setupEmergencyToggle();
        setupTypeToggle();

        const selects = initTomSelect(cfg.placeholders);
        const editorInstance = await initEditor();
        setupAjaxSubmit(form, editorInstance, selects, cfg);
    }

    return { init };
})();

window.initTransaksiCreatePage = () => TransaksiCreatePageHandler.init();

// =====================================================
// TRANSAKSI INDEX PAGE HANDLER
// =====================================================
/**
 * Modul ini untuk halaman `transaksi.index` (date cards + filter + AJAX refresh).
 * Sebelumnya berada di `public/js/transaksi-index.js` dan sekarang digabung ke file ini.
 */
const TransaksiIndexPageHandler = (() => {
    // Flag ini untuk mencegah init dobel (mis. saat Livewire navigated).
    let isInit = false;

    // Fungsi ini untuk membaca config halaman yang dikirim dari blade lewat `#transaksi-page-config`.
    function getConfig() {
        const el = document.getElementById("transaksi-page-config");
        if (!el || !el.textContent) {
            throw new Error("transaksi-page-config missing");
        }
        return JSON.parse(el.textContent);
    }

    // Fungsi ini untuk inisialisasi semua interaksi pada halaman index transaksi.
    function init() {
        if (isInit) return;
        isInit = true;
        setTimeout(() => {
            isInit = false;
        }, 500);

        let cfg;
        try {
            cfg = getConfig();
        } catch (_e) {
            return;
        }

        const routes = cfg.routes || {};
        const csrfToken = cfg.csrfToken;

        let debounceTimer;
        const searchInput = document.getElementById("searchTransaksi");
        const startDateInput = document.getElementById("transaksi_start_date");
        const endDateInput = document.getElementById("transaksi_end_date");
        const tableContainer = document.getElementById("transaction-table-container");
        const btnResetFilter = document.getElementById("btnResetFilter");
        const btnBulkDelete = document.getElementById("btnBulkDelete");

        // Guard: hanya jalan di halaman index yang memang punya container + TomSelect.
        if (!tableContainer || typeof TomSelect === "undefined") {
            return;
        }

        // Fungsi ini untuk sync hidden input filter (di form collapse) agar ikut ter-submit bila dibutuhkan.
        function syncFilterFormDates() {
            const fs = document.getElementById("transaksi_filter_form_start");
            const fe = document.getElementById("transaksi_filter_form_end");
            if (fs && startDateInput) fs.value = startDateInput.value;
            if (fe && endDateInput) fe.value = endDateInput.value;
        }

        // Fungsi ini untuk menyamakan UI daterangepicker dari nilai hidden input (fallback ke bulan ini).
        function syncDaterangePickerFromInputs() {
            if (typeof jQuery === "undefined" || typeof moment === "undefined") return;
            const $dr = jQuery("#transaksiDaterange");
            if (!$dr.length) return;
            const picker = $dr.data("daterangepicker");
            if (!picker) return;
            const sv = startDateInput && startDateInput.value;
            const ev = endDateInput && endDateInput.value;
            if (sv && ev) {
                picker.setStartDate(moment(sv, "YYYY-MM-DD"));
                picker.setEndDate(moment(ev, "YYYY-MM-DD"));
            } else {
                picker.setStartDate(moment().startOf("month"));
                picker.setEndDate(moment().endOf("month"));
            }
        }

        const tomSettings = {
            // TomSelect: plugin ini untuk tombol remove pada pilihan multi-select.
            plugins: ["remove_button"],
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
            persist: false,
            create: false,
        };

        // TomSelect ini untuk filter pemasukan/pengeluaran di halaman index.
        const tomPemasukan = new TomSelect("#filter-pemasukan", tomSettings);
        const tomPengeluaran = new TomSelect("#filter-pengeluaran", tomSettings);

        // Fungsi ini untuk melakukan fetch data transaksi (AJAX) dan update date cards + summary + export link.
        function fetchTransactions(url) {
            const baseUrl = url || routes.index;
            if (!baseUrl) return;

            const urlObj = new URL(baseUrl);

            const searchQuery = searchInput ? searchInput.value : "";
            if (searchQuery) urlObj.searchParams.set("search", searchQuery);

            if (startDateInput && startDateInput.value) {
                urlObj.searchParams.set("start_date", startDateInput.value);
            }
            if (endDateInput && endDateInput.value) {
                urlObj.searchParams.set("end_date", endDateInput.value);
            }

            urlObj.searchParams.delete("pemasukan[]");
            tomPemasukan.getValue().forEach((val) => {
                if (val) urlObj.searchParams.append("pemasukan[]", val);
            });

            urlObj.searchParams.delete("pengeluaran[]");
            tomPengeluaran.getValue().forEach((val) => {
                if (val) urlObj.searchParams.append("pengeluaran[]", val);
            });

            tableContainer.style.opacity = "0.5";

            fetch(urlObj.toString(), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    tableContainer.style.opacity = "1";
                    tableContainer.innerHTML = data.html;

                    if (data.stats) {
                        updateSummaryCards(data.stats);
                    }

                    if (data.modal_pemasukan) {
                        const incomeBody = document.getElementById("income-modal-body");
                        if (incomeBody) incomeBody.innerHTML = data.modal_pemasukan;
                    }
                    if (data.modal_pengeluaran) {
                        const expenseBody = document.getElementById("expense-modal-body");
                        if (expenseBody) expenseBody.innerHTML = data.modal_pengeluaran;
                    }

                    updateExportLinks();
                })
                .catch((error) => {
                    console.error("Error fetching transactions:", error);
                    tableContainer.style.opacity = "1";
                    alert("Failed to load data. Please try again.");
                });
        }

        // Fungsi ini untuk update angka pada summary cards (income/expense/net) berdasarkan response AJAX.
        function updateSummaryCards(stats) {
            const cardIncome = document.querySelectorAll(".card-dashboard")[0];
            if (cardIncome) {
                const h4 = cardIncome.querySelector("h4");
                if (h4) {
                    h4.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(stats.totalPemasukan);
                }
            }

            const cardExpense = document.querySelectorAll(".card-dashboard")[1];
            if (cardExpense) {
                const h4e = cardExpense.querySelector("h4");
                if (h4e) {
                    h4e.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(stats.totalPengeluaran);
                }
            }

            const cardNet = document.querySelectorAll(".card-dashboard")[2];
            if (cardNet) {
                const netH4 = cardNet.querySelector("h4");
                if (netH4) {
                    netH4.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(stats.netIncome);
                    netH4.classList.remove("text-success", "text-danger");
                    netH4.classList.add(stats.netIncome >= 0 ? "text-success" : "text-danger");
                }
            }

            const dailyEl = document.getElementById("avg-daily");
            if (dailyEl && stats.avgDailyPengeluaran !== undefined) {
                dailyEl.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(stats.avgDailyPengeluaran);
            }

            const dateRangeEl = document.getElementById("avg-date-range");
            if (dateRangeEl && stats.dateRange) {
                dateRangeEl.innerHTML = '<i class="bi bi-calendar3 me-1"></i> ' + stats.dateRange;
            }

            const drLabel = document.querySelector("#transaksiDaterange .transaksi-daterange-label");
            if (drLabel && stats.dateRange) {
                drLabel.textContent = stats.dateRange;
            }

            syncDaterangePickerFromInputs();
        }

        // Fungsi ini untuk update href tombol export (Excel/PDF/Email) berdasarkan filter aktif.
        function updateExportLinks() {
            const params = new URLSearchParams();

            if (searchInput && searchInput.value) params.append("search", searchInput.value);
            if (startDateInput && startDateInput.value) params.append("start_date", startDateInput.value);
            if (endDateInput && endDateInput.value) params.append("end_date", endDateInput.value);

            tomPemasukan.getValue().forEach((val) => {
                if (val) params.append("pemasukan[]", val);
            });
            tomPengeluaran.getValue().forEach((val) => {
                if (val) params.append("pengeluaran[]", val);
            });

            const btnExportExcel = document.getElementById("btnExportExcel");
            const btnExportPdf = document.getElementById("btnExportPdf");
            const btnExportExcelMobile = document.getElementById("btnExportExcelMobile");
            const btnExportPdfMobile = document.getElementById("btnExportPdfMobile");

            // Fungsi ini untuk menempelkan query params ke link yang memiliki `data-base-url` (atau href awal).
            function updateLink(link) {
                if (!link) return;
                const url = new URL(link.dataset.baseUrl || link.href);
                if (!link.dataset.baseUrl) link.dataset.baseUrl = link.href;
                link.href = url.origin + url.pathname + "?" + params.toString();
            }

            updateLink(btnExportExcel);
            updateLink(btnExportPdf);
            updateLink(btnExportExcelMobile);
            updateLink(btnExportPdfMobile);
        }

        // Tombol reset filter: mengosongkan semua input lalu fetch ulang.
        if (btnResetFilter) {
            btnResetFilter.addEventListener("click", (e) => {
                e.preventDefault();
                if (searchInput) searchInput.value = "";
                if (startDateInput) startDateInput.value = "";
                if (endDateInput) endDateInput.value = "";
                syncFilterFormDates();

                if (typeof jQuery !== "undefined") {
                    const $lbl = jQuery("#transaksiDaterange .transaksi-daterange-label");
                    if ($lbl.length) $lbl.text("—");
                    const picker = jQuery("#transaksiDaterange").data("daterangepicker");
                    if (picker && typeof moment !== "undefined") {
                        picker.setStartDate(moment().startOf("month"));
                        picker.setEndDate(moment().endOf("month"));
                    }
                }

                tomPemasukan.clear();
                tomPengeluaran.clear();
                fetchTransactions();
            });
        }

        // Tombol apply filter di collapse: trigger AJAX refresh.
        const applyFilterBtn = document.getElementById("btnApplyFilter");
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener("click", (e) => {
                e.preventDefault();
                fetchTransactions();
            });
        }

        // Search input: debounce supaya tidak terlalu sering request.
        if (searchInput) {
            searchInput.addEventListener("keyup", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchTransactions();
                }, 500);
            });
        }

        // Inisialisasi daterangepicker versi halaman index (format DD/MM/YYYY + callback fetchTransactions).
        if (typeof jQuery !== "undefined" && jQuery.fn.daterangepicker && typeof moment !== "undefined") {
            ((jQueryRef) => {
                const $dr = jQueryRef("#transaksiDaterange");
                if (!$dr.length) return;
                if ($dr.data("daterangepicker")) {
                    $dr.data("daterangepicker").remove();
                }

                // Fungsi ini untuk menampilkan label daterangepicker dari 2 moment.
                function setSpanFromMoments(startM, endM) {
                    $dr.find(".transaksi-daterange-label").text(
                        startM.format("DD/MM/YYYY") + " – " + endM.format("DD/MM/YYYY"),
                    );
                }

                const sv = startDateInput && startDateInput.value;
                const ev = endDateInput && endDateInput.value;
                const startM = sv ? moment(sv, "YYYY-MM-DD") : moment().startOf("month");
                const endM = ev ? moment(ev, "YYYY-MM-DD") : moment().endOf("month");

                if (sv && ev) setSpanFromMoments(startM, endM);
                syncFilterFormDates();

                const RP = cfg.dateRangePicker || { ranges: {}, locale: {} };
                const r = RP.ranges || {};
                const loc = RP.locale || {};
                const ranges = {};
                if (r.today) ranges[r.today] = [moment(), moment()];
                if (r.yesterday) ranges[r.yesterday] = [moment().subtract(1, "days"), moment().subtract(1, "days")];
                if (r.thisMonth) ranges[r.thisMonth] = [moment().startOf("month"), moment().endOf("month")];
                if (r.lastMonth) ranges[r.lastMonth] = [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ];
                if (r.thisYear) ranges[r.thisYear] = [moment().startOf("year"), moment().endOf("year")];
                if (r.lastYear) ranges[r.lastYear] = [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year"),
                ];

                $dr.daterangepicker(
                    {
                        startDate: startM,
                        endDate: endM,
                        locale: {
                            format: "DD/MM/YYYY",
                            separator: " – ",
                            applyLabel: loc.apply || "OK",
                            cancelLabel: loc.cancel || "Cancel",
                            customRangeLabel: loc.customRange || "Custom range",
                            firstDay: 1,
                        },
                        ranges: ranges,
                        opens: "left",
                        autoUpdateInput: false,
                    },
                    (start, end) => {
                        if (startDateInput) startDateInput.value = start.format("YYYY-MM-DD");
                        if (endDateInput) endDateInput.value = end.format("YYYY-MM-DD");
                        setSpanFromMoments(start, end);
                        syncFilterFormDates();
                        fetchTransactions();
                    },
                );
            })(jQuery);
        }

        // Konfirmasi export via email: redirect ke route dengan query filter + email penerima.
        const btnConfirmExportEmail = document.getElementById("btnConfirmExportEmail");
        if (btnConfirmExportEmail) {
            btnConfirmExportEmail.addEventListener("click", function () {
                const recipientEmail = document.getElementById("export_recipient_email");
                if (!recipientEmail || !recipientEmail.value) {
                    alert("Please enter a valid email address.");
                    return;
                }

                const params = new URLSearchParams();
                if (searchInput && searchInput.value) params.append("search", searchInput.value);
                if (startDateInput && startDateInput.value) params.append("start_date", startDateInput.value);
                if (endDateInput && endDateInput.value) params.append("end_date", endDateInput.value);
                tomPemasukan.getValue().forEach((val) => {
                    if (val) params.append("pemasukan[]", val);
                });
                tomPengeluaran.getValue().forEach((val) => {
                    if (val) params.append("pengeluaran[]", val);
                });
                params.append("email", recipientEmail.value);

                const exportUrl = (routes.exportEmail || "") + "?" + params.toString();
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                btn.disabled = true;

                window.location.href = exportUrl;

                setTimeout(() => {
                    const modalEl = document.getElementById("emailExportModal");
                    const modal = modalEl && typeof bootstrap !== "undefined" ? bootstrap.Modal.getInstance(modalEl) : null;
                    if (modal) modal.hide();
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            });
        }

        // Bulk delete: menghapus transaksi yang dicentang.
        if (btnBulkDelete) {
            btnBulkDelete.addEventListener("click", function () {
                const checked = document.querySelectorAll(".check-item:checked");
                const ids = Array.from(checked).map((cb) => cb.value);
                if (ids.length === 0) return;

                if (typeof window.confirmAction !== "function") {
                    if (!confirm("Delete selected transactions?")) return;
                }

                (window.confirmAction || ((opt) => opt.onConfirm && opt.onConfirm()))({
                    title: "Delete selected transactions?",
                    text: "Deleted data cannot be recovered!",
                    onConfirm: async () => {
                        const btn = this;
                        const originalText = btn.innerHTML;
                        btn.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                        btn.disabled = true;

                        try {
                            const response = await fetch(routes.bulkDelete, {
                                method: "DELETE",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": csrfToken,
                                },
                                body: JSON.stringify({ ids: ids }),
                            });
                            if (response.ok) {
                                fetchTransactions();
                                btn.classList.add("d-none");
                            } else {
                                alert("Failed to delete transactions.");
                            }
                        } catch (e) {
                            console.error("Error:", e);
                            alert("Failed to delete transactions.");
                        } finally {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }
                    },
                });
            });
        }

        // Delete 1 transaksi (delegasi dari container).
        tableContainer.addEventListener("submit", (e) => {
            if (!e.target.classList.contains("form-delete")) return;
            e.preventDefault();

            (window.confirmAction || ((opt) => opt.onConfirm && opt.onConfirm()))({
                title: "Are you sure?",
                text: "Deleted data cannot be recovered!",
                onConfirm: async () => {
                    const form = e.target;
                    const url = form.getAttribute("action");
                    const btn = form.querySelector("button");
                    const originalContent = btn ? btn.innerHTML : "";

                    if (btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                        btn.disabled = true;
                    }

                    try {
                        const response = await fetch(url, {
                            method: "POST",
                            body: new FormData(form),
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            fetchTransactions();
                        } else {
                            alert(data.message || "Gagal menghapus data");
                            if (btn) {
                                btn.innerHTML = originalContent;
                                btn.disabled = false;
                            }
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat menghapus data");
                        if (btn) {
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    }
                },
            });
        });

        // Upload bukti transaksi (modal upload) + refresh list setelah sukses.
        const uploadModal = document.getElementById("uploadModal");
        const uploadForm = document.getElementById("uploadForm");
        const transaksiIdInput = document.getElementById("transaksiId");

        tableContainer.addEventListener("click", (e) => {
            const uploadBtn = e.target.closest(".btn-upload");
            if (uploadBtn && transaksiIdInput) {
                const id = uploadBtn.getAttribute("data-id");
                transaksiIdInput.value = id;
            }
        });

        if (uploadForm) {
            uploadForm.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : "";

                if (submitBtn) {
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading...';
                    submitBtn.disabled = true;
                }

                fetch(routes.upload, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            if (uploadModal && typeof bootstrap !== "undefined") {
                                const modal = bootstrap.Modal.getInstance(uploadModal);
                                if (modal) modal.hide();
                            }
                            uploadForm.reset();
                            fetchTransactions();
                        } else {
                            alert(data.message || "Gagal mengupload file");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat mengupload file");
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    });
            });
        }

        // Hapus file bukti transaksi (delegasi dari container).
        tableContainer.addEventListener("click", (e) => {
            const delBtn = e.target.closest(".btn-delete-file");
            if (!delBtn) return;

            const id = delBtn.getAttribute("data-id");
            (window.confirmAction || ((opt) => opt.onConfirm && opt.onConfirm()))({
                title: "Delete this proof?",
                text: "The file will be permanently removed.",
                onConfirm: async () => {
                    const originalContent = delBtn.innerHTML;
                    delBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    delBtn.disabled = true;

                    try {
                        const response = await fetch(routes.transaksiBase + "/" + id + "/file", {
                            method: "DELETE",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            fetchTransactions();
                        } else {
                            alert(data.message || "Gagal menghapus file");
                            delBtn.innerHTML = originalContent;
                            delBtn.disabled = false;
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat menghapus file");
                        delBtn.innerHTML = originalContent;
                        delBtn.disabled = false;
                    }
                },
            });
        });

        // Shortcut "Go to date" (redirect ke halaman transaksi by date).
        const btnGoToDate = document.getElementById("btnGoToDate");
        const inputOpenDate = document.getElementById("inputOpenDate");
        if (btnGoToDate && inputOpenDate) {
            btnGoToDate.addEventListener("click", function () {
                const date = inputOpenDate.value;
                if (!date) return;
                const url = this.dataset.baseUrl.replace("__DATE__", date);
                window.location.href = url;
            });
        }

        // Trigger initial export link sync.
        updateExportLinks();
    }

    return { init };
})();

// Expose init function (bila suatu saat perlu dipanggil manual)
window.initTransaksiIndexPage = () => TransaksiIndexPageHandler.init();

// =====================================================
// MAIN INITIALIZATION
// =====================================================
let isTransaksiInitRunning = false;

window.initTransaksiAll = function () {
    if (isTransaksiInitRunning) return;
    isTransaksiInitRunning = true;
    setTimeout(() => { isTransaksiInitRunning = false; }, 500);

    // Inisialisasi fitur halaman transaksi per halaman (aman karena ada guard elemen).
    TransaksiCreatePageHandler.init();
    TransaksiIndexPageHandler.init();

    // Make sure we only initialize if the transaction table exists!
    if (!$('#transaksiTable').length) return;

    // Check if DataTable is already initialized to avoid re-init error
    if ($.fn.DataTable.isDataTable('#transaksiTable')) {
        $('#transaksiTable').DataTable().destroy();
    }

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

    // Only bind global body listeners once to prevent duplication
    if (!window.transaksiEventsBound) {
        TransaksiCRUD.initCreate();
        TransaksiCRUD.initEdit();
        TransaksiCRUD.initUpdate();
        TransaksiCRUD.initDelete();
        TransaksiCRUD.initToggleStatus();
        UploadHandler.init();
        ImportHandler.init();
        window.transaksiEventsBound = true;
    }

    AssetListHandler.init();
    LaporanToggleHandler.init();
    TomSelectHandler.initAll();

    // Download template handler
    const downloadTemplateBtn = document.getElementById('btn-download-template');
    if (downloadTemplateBtn) {
        // use off() to prevent duplicate binds if replaced or use simple onclick
        downloadTemplateBtn.onclick = () => DownloadHandler.downloadTemplate();
    }
};

$(document).ready(function () {
    initTransaksiAll();
});

document.addEventListener('livewire:navigated', function () {
    initTransaksiAll();
});
