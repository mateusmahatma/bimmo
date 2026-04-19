(function () {
    "use strict";

    function getConfig() {
        var el = document.getElementById("transaksi-page-config");
        if (!el || !el.textContent) {
            throw new Error("transaksi-page-config missing");
        }
        return JSON.parse(el.textContent);
    }

    var isInit = false;
    function initTransaksiIndexPage() {
        if (isInit) return;
        isInit = true;
        setTimeout(function () {
            isInit = false;
        }, 500);

        var cfg = getConfig();
        var routes = cfg.routes || {};
        var csrfToken = cfg.csrfToken;

        var debounceTimer;
        var searchInput = document.getElementById("searchTransaksi");
        var startDateInput = document.getElementById("transaksi_start_date");
        var endDateInput = document.getElementById("transaksi_end_date");
        var tableContainer = document.getElementById(
            "transaction-table-container",
        );
        var btnResetFilter = document.getElementById("btnResetFilter");

        if (!tableContainer || typeof TomSelect === "undefined") {
            return;
        }

        function syncFilterFormDates() {
            var fs = document.getElementById("transaksi_filter_form_start");
            var fe = document.getElementById("transaksi_filter_form_end");
            if (fs && startDateInput) fs.value = startDateInput.value;
            if (fe && endDateInput) fe.value = endDateInput.value;
        }

        function syncDaterangePickerFromInputs() {
            if (typeof jQuery === "undefined" || typeof moment === "undefined")
                return;
            var $dr = jQuery("#transaksiDaterange");
            if (!$dr.length) return;
            var picker = $dr.data("daterangepicker");
            if (!picker) return;
            var sv = startDateInput && startDateInput.value;
            var ev = endDateInput && endDateInput.value;
            if (sv && ev) {
                picker.setStartDate(moment(sv, "YYYY-MM-DD"));
                picker.setEndDate(moment(ev, "YYYY-MM-DD"));
            } else {
                picker.setStartDate(moment().startOf("month"));
                picker.setEndDate(moment().endOf("month"));
            }
        }

        var tomSettings = {
            plugins: ["remove_button"],
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
            persist: false,
            create: false,
        };
        var tomPemasukan = new TomSelect("#filter-pemasukan", tomSettings);
        var tomPengeluaran = new TomSelect("#filter-pengeluaran", tomSettings);

        if (btnResetFilter) {
            btnResetFilter.addEventListener("click", function (e) {
                e.preventDefault();
                if (searchInput) searchInput.value = "";
                if (startDateInput) startDateInput.value = "";
                if (endDateInput) endDateInput.value = "";
                syncFilterFormDates();
                if (typeof jQuery !== "undefined") {
                    var $lbl = jQuery(
                        "#transaksiDaterange .transaksi-daterange-label",
                    );
                    if ($lbl.length) $lbl.text("—");
                    var picker = jQuery("#transaksiDaterange").data(
                        "daterangepicker",
                    );
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

        var applyFilterBtn = document.getElementById("btnApplyFilter");
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener("click", function (e) {
                e.preventDefault();
                fetchTransactions();
            });
        }

        if (searchInput) {
            searchInput.addEventListener("keyup", function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    fetchTransactions();
                }, 500);
            });
        }

        function fetchTransactions(url) {
            var baseUrl = url || routes.index;
            var urlObj = new URL(baseUrl);

            var searchQuery = searchInput ? searchInput.value : "";
            if (searchQuery) urlObj.searchParams.set("search", searchQuery);

            if (startDateInput && startDateInput.value) {
                urlObj.searchParams.set("start_date", startDateInput.value);
            }
            if (endDateInput && endDateInput.value) {
                urlObj.searchParams.set("end_date", endDateInput.value);
            }

            urlObj.searchParams.delete("pemasukan[]");
            tomPemasukan.getValue().forEach(function (val) {
                if (val) urlObj.searchParams.append("pemasukan[]", val);
            });

            urlObj.searchParams.delete("pengeluaran[]");
            tomPengeluaran.getValue().forEach(function (val) {
                if (val) urlObj.searchParams.append("pengeluaran[]", val);
            });

            tableContainer.style.opacity = "0.5";

            fetch(urlObj.toString(), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    tableContainer.style.opacity = "1";
                    tableContainer.innerHTML = data.html;

                    if (data.stats) {
                        updateSummaryCards(data.stats);
                    }

                    if (data.modal_pemasukan) {
                        var incomeBody =
                            document.getElementById("income-modal-body");
                        if (incomeBody)
                            incomeBody.innerHTML = data.modal_pemasukan;
                    }
                    if (data.modal_pengeluaran) {
                        var expenseBody =
                            document.getElementById("expense-modal-body");
                        if (expenseBody)
                            expenseBody.innerHTML = data.modal_pengeluaran;
                    }

                    updateExportLinks();
                })
                .catch(function (error) {
                    console.error("Error fetching transactions:", error);
                    tableContainer.style.opacity = "1";
                    alert("Failed to load data. Please try again.");
                });
        }

        function updateSummaryCards(stats) {
            var cardIncome = document.querySelectorAll(".card-dashboard")[0];
            if (cardIncome) {
                var h4 = cardIncome.querySelector("h4");
                if (h4) {
                    h4.textContent =
                        "Rp " +
                        new Intl.NumberFormat("id-ID").format(
                            stats.totalPemasukan,
                        );
                }
            }

            var cardExpense = document.querySelectorAll(".card-dashboard")[1];
            if (cardExpense) {
                var h4e = cardExpense.querySelector("h4");
                if (h4e) {
                    h4e.textContent =
                        "Rp " +
                        new Intl.NumberFormat("id-ID").format(
                            stats.totalPengeluaran,
                        );
                }
            }

            var cardNet = document.querySelectorAll(".card-dashboard")[2];
            if (cardNet) {
                var netH4 = cardNet.querySelector("h4");
                if (netH4) {
                    netH4.textContent =
                        "Rp " +
                        new Intl.NumberFormat("id-ID").format(stats.netIncome);
                    netH4.classList.remove("text-success", "text-danger");
                    netH4.classList.add(
                        stats.netIncome >= 0 ? "text-success" : "text-danger",
                    );
                }
            }

            var dailyEl = document.getElementById("avg-daily");
            if (dailyEl && stats.avgDailyPengeluaran !== undefined) {
                dailyEl.textContent =
                    "Rp " +
                    new Intl.NumberFormat("id-ID").format(
                        stats.avgDailyPengeluaran,
                    );
            }

            var dateRangeEl = document.getElementById("avg-date-range");
            if (dateRangeEl && stats.dateRange) {
                dateRangeEl.innerHTML =
                    '<i class="bi bi-calendar3 me-1"></i> ' + stats.dateRange;
            }

            var drLabel = document.querySelector(
                "#transaksiDaterange .transaksi-daterange-label",
            );
            if (drLabel && stats.dateRange) {
                drLabel.textContent = stats.dateRange;
            }
            syncDaterangePickerFromInputs();
        }

        var btnExportExcel = document.getElementById("btnExportExcel");
        var btnExportPdf = document.getElementById("btnExportPdf");

        function updateExportLinks() {
            var params = new URLSearchParams();

            if (searchInput && searchInput.value)
                params.append("search", searchInput.value);
            if (startDateInput && startDateInput.value) {
                params.append("start_date", startDateInput.value);
            }
            if (endDateInput && endDateInput.value) {
                params.append("end_date", endDateInput.value);
            }

            tomPemasukan.getValue().forEach(function (val) {
                if (val) params.append("pemasukan[]", val);
            });
            tomPengeluaran.getValue().forEach(function (val) {
                if (val) params.append("pengeluaran[]", val);
            });

            function updateLink(link) {
                if (!link) return;
                var url = new URL(link.dataset.baseUrl || link.href);
                if (!link.dataset.baseUrl) link.dataset.baseUrl = link.href;
                link.href = url.origin + url.pathname + "?" + params.toString();
            }

            updateLink(btnExportExcel);
            updateLink(btnExportPdf);
        }

        var btnConfirmExportEmail = document.getElementById(
            "btnConfirmExportEmail",
        );
        if (btnConfirmExportEmail) {
            btnConfirmExportEmail.addEventListener("click", function () {
                var recipientEmail = document.getElementById(
                    "export_recipient_email",
                );
                if (!recipientEmail || !recipientEmail.value) {
                    alert("Please enter a valid email address.");
                    return;
                }

                var params = new URLSearchParams();
                if (searchInput && searchInput.value)
                    params.append("search", searchInput.value);
                if (startDateInput && startDateInput.value) {
                    params.append("start_date", startDateInput.value);
                }
                if (endDateInput && endDateInput.value) {
                    params.append("end_date", endDateInput.value);
                }
                tomPemasukan.getValue().forEach(function (val) {
                    if (val) params.append("pemasukan[]", val);
                });
                tomPengeluaran.getValue().forEach(function (val) {
                    if (val) params.append("pengeluaran[]", val);
                });
                params.append("email", recipientEmail.value);

                var exportUrl = routes.exportEmail + "?" + params.toString();
                var btn = this;
                var originalText = btn.innerHTML;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                btn.disabled = true;

                window.location.href = exportUrl;

                setTimeout(function () {
                    var modalEl = document.getElementById("emailExportModal");
                    var modal = modalEl && bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            });
        }

        updateExportLinks();

        if (btnBulkDelete) {
            btnBulkDelete.addEventListener("click", function () {
                var checked = document.querySelectorAll(".check-item:checked");
                var ids = Array.from(checked).map(function (cb) {
                    return cb.value;
                });

                if (ids.length === 0) return;

                window.confirmAction({
                    title: "Delete selected transactions?",
                    text: "Deleted data cannot be recovered!",
                    onConfirm: async () => {
                        var btn = this;
                        var originalText = btn.innerHTML;
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

        tableContainer.addEventListener("submit", function (e) {
            if (e.target.classList.contains("form-delete")) {
                e.preventDefault();

                window.confirmAction({
                    title: "Are you sure?",
                    text: "Deleted data cannot be recovered!",
                    onConfirm: async () => {
                        var form = e.target;
                        var url = form.getAttribute("action");
                        var btn = form.querySelector("button");
                        var originalContent = btn.innerHTML;

                        btn.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status"></span>';
                        btn.disabled = true;

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
                                btn.innerHTML = originalContent;
                                btn.disabled = false;
                            }
                        } catch (error) {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan saat menghapus data");
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    },
                });
            }
        });

        var uploadModal = document.getElementById("uploadModal");
        var uploadForm = document.getElementById("uploadForm");
        var transaksiIdInput = document.getElementById("transaksiId");

        tableContainer.addEventListener("click", function (e) {
            var uploadBtn = e.target.closest(".btn-upload");
            if (uploadBtn && transaksiIdInput) {
                var id = uploadBtn.getAttribute("data-id");
                transaksiIdInput.value = id;
            }
        });

        if (uploadForm) {
            uploadForm.addEventListener("submit", function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                var submitBtn = this.querySelector('button[type="submit"]');
                var originalText = submitBtn.innerHTML;

                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading...';
                submitBtn.disabled = true;

                fetch(routes.upload, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            if (uploadModal) {
                                var modal =
                                    bootstrap.Modal.getInstance(uploadModal);
                                if (modal) modal.hide();
                            }
                            uploadForm.reset();
                            fetchTransactions();
                        } else {
                            alert(data.message || "Gagal mengupload file");
                        }
                    })
                    .catch(function (error) {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat mengupload file");
                    })
                    .finally(function () {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        tableContainer.addEventListener("click", function (e) {
            var delBtn = e.target.closest(".btn-delete-file");
            if (!delBtn) return;

            var id = delBtn.getAttribute("data-id");
            window.confirmAction({
                title: "Delete this proof?",
                text: "The file will be permanently removed.",
                onConfirm: async () => {
                    var originalContent = delBtn.innerHTML;
                    delBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    delBtn.disabled = true;

                    try {
                        const response = await fetch(
                            routes.transaksiBase + "/" + id + "/file",
                            {
                                method: "DELETE",
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest",
                                    "X-CSRF-TOKEN": csrfToken,
                                },
                            },
                        );
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

        btnGoToDate.addEventListener("click", function () {
            var date = inputOpenDate.value;
            if (!date) return;
            var url = this.dataset.baseUrl.replace("__DATE__", date);
            window.location.href = url;
        });

        if (
            typeof jQuery !== "undefined" &&
            jQuery.fn.daterangepicker &&
            typeof moment !== "undefined"
        ) {
            (function ($) {
                var $dr = $("#transaksiDaterange");
                if (!$dr.length) return;
                if ($dr.data("daterangepicker")) {
                    $dr.data("daterangepicker").remove();
                }

                function setSpanFromMoments(startM, endM) {
                    $dr.find(".transaksi-daterange-label").text(
                        startM.format("DD/MM/YYYY") +
                            " \u2013 " +
                            endM.format("DD/MM/YYYY"),
                    );
                }

                var sv = startDateInput && startDateInput.value;
                var ev = endDateInput && endDateInput.value;
                var startM = sv
                    ? moment(sv, "YYYY-MM-DD")
                    : moment().startOf("month");
                var endM = ev
                    ? moment(ev, "YYYY-MM-DD")
                    : moment().endOf("month");

                if (sv && ev) {
                    setSpanFromMoments(startM, endM);
                }
                syncFilterFormDates();

                var RP = cfg.dateRangePicker || { ranges: {}, locale: {} };
                var r = RP.ranges || {};
                var loc = RP.locale || {};
                var ranges = {};
                if (r.today) ranges[r.today] = [moment(), moment()];
                if (r.yesterday) {
                    ranges[r.yesterday] = [
                        moment().subtract(1, "days"),
                        moment().subtract(1, "days"),
                    ];
                }
                if (r.thisMonth) {
                    ranges[r.thisMonth] = [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ];
                }
                if (r.lastMonth) {
                    ranges[r.lastMonth] = [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ];
                }
                if (r.thisYear) {
                    ranges[r.thisYear] = [
                        moment().startOf("year"),
                        moment().endOf("year"),
                    ];
                }
                if (r.lastYear) {
                    ranges[r.lastYear] = [
                        moment().subtract(1, "year").startOf("year"),
                        moment().subtract(1, "year").endOf("year"),
                    ];
                }

                $dr.daterangepicker(
                    {
                        startDate: startM,
                        endDate: endM,
                        locale: {
                            format: "DD/MM/YYYY",
                            separator: " \u2013 ",
                            applyLabel: loc.apply || "OK",
                            cancelLabel: loc.cancel || "Cancel",
                            customRangeLabel: loc.customRange || "Custom range",
                            firstDay: 1,
                        },
                        ranges: ranges,
                        opens: "left",
                        autoUpdateInput: false,
                    },
                    function (start, end) {
                        if (startDateInput) {
                            startDateInput.value = start.format("YYYY-MM-DD");
                        }
                        if (endDateInput) {
                            endDateInput.value = end.format("YYYY-MM-DD");
                        }
                        setSpanFromMoments(start, end);
                        syncFilterFormDates();
                        fetchTransactions();
                    },
                );
            })(jQuery);
        }
    }

    document.addEventListener("DOMContentLoaded", initTransaksiIndexPage);
    document.addEventListener("livewire:navigated", initTransaksiIndexPage);
})();
