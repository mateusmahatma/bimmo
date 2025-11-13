document.addEventListener("DOMContentLoaded", function () {
    const charts = {};
    const chartData = {};

    function isValidChartData(data) {
        return data && Array.isArray(data.labels);
    }

    function filterData(data, months) {
        if (!isValidChartData(data)) {
            return {
                labels: [],
                pemasukan: [],
                pengeluaran: [],
                data_pemasukan: [],
                data_pengeluaran: [],
            };
        }

        if (months === "all") return data;

        const startIndex = Math.max(data.labels.length - months, 0);
        const filtered = { labels: data.labels.slice(startIndex) };

        Object.keys(data).forEach((key) => {
            if (Array.isArray(data[key])) {
                filtered[key] = data[key].slice(startIndex);
            }
        });

        return filtered;
    }

    function renderChart(type, selector, data, options) {
        const theme = document.documentElement.getAttribute("data-bs-theme") || "light";
        const isDark = theme === "dark";

        const defaultOptions = {
            chart: {
                type: type,
                height: 350,
                toolbar: { show: true, tools: { download: false } },
                foreColor: isDark ? "#fff" : "#333",
            },
            xaxis: {
                categories: data.labels,
                labels: {
                    style: { colors: isDark ? "#ccc" : "#555" }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: isDark ? "#ccc" : "#555" },
                    formatter: (val) => val.toLocaleString("id-ID")
                }
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                theme: isDark ? "dark" : "light"
            },
            series: [],
            ...options,
        };

        const el = document.querySelector(selector);
        if (!el) return;

        if (charts[selector]) {
            charts[selector].updateOptions(defaultOptions);
        } else {
            charts[selector] = new ApexCharts(el, defaultOptions);
            charts[selector].render();
        }
    }

    // old
    // function renderCashFlowChart(data, months = "all") {
    //     const filteredData = filterData(data, months);

    //     if (!Array.isArray(filteredData.pemasukan) || !Array.isArray(filteredData.pengeluaran)) {
    //         console.warn("Data cash flow tidak lengkap:", filteredData);
    //         return;
    //     }

    //     renderChart("bar", "#columnChart", filteredData, {
    //         colors: ["var(--bs-green)", "var(--bs-red)"],
    //         plotOptions: {
    //             bar: {
    //                 columnWidth: "30%",
    //                 borderRadius: 4,
    //                 dataLabels: {
    //                     position: "top",
    //                 },
    //                 border: { color: 'var(--bs-gray)', width: 1 }
    //             },
    //         },
    //         series: [
    //             { name: "Income", data: filteredData.pemasukan },
    //             { name: "Expense", data: filteredData.pengeluaran },
    //         ],
    //         legend: {
    //             position: "top",
    //             horizontalAlign: "center",
    //         },
    //     });
    // }

    // new
    function renderCashFlowChart(data, months = "all") {
        const filteredData = filterData(data, months);

        if (!Array.isArray(filteredData.pemasukan) || !Array.isArray(filteredData.pengeluaran)) {
            console.warn("Data cash flow tidak lengkap:", filteredData);
            return;
        }

        renderChart("line", "#columnChart", filteredData, {
            series: [
                {
                    name: "Income",
                    type: "column",
                    data: filteredData.pemasukan
                },
                {
                    name: "Expense",
                    type: "column",
                    data: filteredData.pengeluaran
                },
                {
                    name: "Net Flow",
                    type: "line",
                    data: filteredData.pemasukan.map((v, i) => v - (filteredData.pengeluaran[i] || 0))
                },
            ],
            colors: ["#008FFB", "#00E396", "#FEB019"],
            stroke: {
                width: [1, 1, 3],
            },
            plotOptions: {
                bar: {
                    columnWidth: "35%",
                    borderRadius: 4,
                },
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: filteredData.labels,
            },
            yaxis: [
                {
                    seriesName: "Income",
                    axisTicks: { show: true },
                    axisBorder: { show: true, color: "#008FFB" },
                    labels: { style: { colors: "#008FFB" } },
                    title: {
                        text: "Income",
                        style: { color: "#008FFB" },
                    },
                },
                {
                    seriesName: "Expense",
                    opposite: true,
                    axisTicks: { show: true },
                    axisBorder: { show: true, color: "#00E396" },
                    labels: { style: { colors: "#00E396" } },
                    title: {
                        text: "Expense",
                        style: { color: "#00E396" },
                    },
                },
                {
                    seriesName: "Net Flow",
                    opposite: true,
                    show: false,
                },
            ],
            tooltip: {
                fixed: {
                    enabled: true,
                    position: "topLeft",
                    offsetY: 30,
                    offsetX: 60,
                },
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40,
            },
        });
    }


    function renderIncomeExpenseChart(data, months = "all") {
        const filteredData = filterData(data, months);

        if (!Array.isArray(filteredData.data_pemasukan) || !Array.isArray(filteredData.data_pengeluaran)) {
            console.warn("Data income/expense tidak lengkap:", filteredData);
            return;
        }

        renderChart("line", "#barChart", filteredData, {
            colors: ["var(--bs-green)", "var(--bs-red)"],
            stroke: {
                curve: "smooth",
                width: 2,
                colors: ["var(--bs-green)", "var(--bs-red)"],
                dashArray: [0, 4],
                lineCap: "round",
            },
            markers: { size: 0 },
            series: [
                { name: "Income", data: filteredData.data_pemasukan },
                { name: "Expense", data: filteredData.data_pengeluaran },
            ],
            legend: {
                position: "top",
                horizontalAlign: "center",
            },
        });
    }

    function handleFilterChange(e) {
        const months = e.target.value === "all" ? "all" : parseInt(e.target.value);
        renderCashFlowChart(chartData["cashFlow"], months);
        renderIncomeExpenseChart(chartData["incomeExpense"], months);
    }

    // Ambil data awal
    Promise.all([
        fetch("/dashboard/line-data").then(res => res.json()),
        fetch("/dashboard/chart-data").then(res => res.json())
    ]).then(([cashFlowData, incomeExpenseData]) => {
        chartData["cashFlow"] = cashFlowData;
        chartData["incomeExpense"] = incomeExpenseData;

        renderCashFlowChart(cashFlowData, 6);
        renderIncomeExpenseChart(incomeExpenseData, 6);

        const filter = document.getElementById("filterPeriod");
        if (filter) filter.addEventListener("change", handleFilterChange);
    }).catch(err => {
        console.error("Gagal memuat data chart:", err);
    });

    // Theme switching
    document.addEventListener("themeChanged", () => {
        const filter = document.getElementById("filterPeriod");
        const months = filter?.value === "all" ? "all" : parseInt(filter?.value || "6");

        renderCashFlowChart(chartData["cashFlow"], months);
        renderIncomeExpenseChart(chartData["incomeExpense"], months);
    });

    // Chart switch toggle
    document.getElementById("chartType")?.addEventListener("change", () => {
        const chartType = document.getElementById("chartType").value;
        const columnChart = document.getElementById("columnChart");
        const barChart = document.getElementById("barChart");

        columnChart.classList.remove("show");
        barChart.classList.remove("show");

        setTimeout(() => {
            columnChart.style.display = "none";
            barChart.style.display = "none";

            if (chartType === "cashFlow") {
                columnChart.style.display = "block";
                setTimeout(() => columnChart.classList.add("show"), 50);
                window.dispatchEvent(new Event("resize"));
            } else {
                barChart.style.display = "block";
                setTimeout(() => barChart.classList.add("show"), 50);
                window.dispatchEvent(new Event("resize"));
            }
        }, 300);
    });

    // Set default chart saat halaman pertama dimuat
    window.onload = () => {
        const chartType = document.getElementById("chartType")?.value || "cashFlow";
        const columnChart = document.getElementById("columnChart");
        const barChart = document.getElementById("barChart");

        if (chartType === "cashFlow") {
            columnChart.style.display = "block";
            columnChart.classList.add("show");
            barChart.style.display = "none";
        } else {
            barChart.style.display = "block";
            barChart.classList.add("show");
            columnChart.style.display = "none";
        }
    };
});

// Today's Transaction
document.addEventListener("DOMContentLoaded", function () {
    function getTodayTransactions() {
        $.ajax({
            url: "/dashboard/todayTransactions",
            type: "GET",
            dataType: "json",
            success: function (data) {
                populateTodayTransactionsTable(data);
            },
            error: function (error) {
                console.error("Error fetching today transactions:", error);
            },
        });
    }

    function formatNumberWithSeparator(number) {
        if (!number) return "0";
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatKeterangan(keterangan) {
        if (!keterangan) return "-";
        if (typeof keterangan !== "string") return keterangan;

        var lines = keterangan.split("\n");
        var renderedText = "";
        lines.forEach(function (line, index) {
            renderedText += index + 1 + ". " + line + "<br>";
        });
        return renderedText;
    }

    function populateTodayTransactionsTable(data) {
        const table = $("#todayTransactionsTable").DataTable({
            paging: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
        });

        // Hapus semua <tr> kecuali baris pertama (header)
        table.find("tr:gt(0)").remove();

        var cardTitle = $(".card-transaksi h3");

        if (data.length === 0) {
            var noDataRow = `
            <tr>
                <td class="text-center" colspan="6">No data available in table</td>
            </tr>
        `;
            table.append(noDataRow);
        } else {
            $.each(data, function (key, transaction) {
                var row = `
                <tr>
                    <td class="text-center">${key + 1}</td>
                    <td class="text-center">
                        ${transaction.pemasukan && transaction.pemasukan_relation?.nama
                        ? transaction.pemasukan_relation?.nama
                        : "-"}
                    </td>
                    <td class="text-center">
                        ${transaction.nominal_pemasukan ? formatNumberWithSeparator(transaction.nominal_pemasukan) : "0"}
                    </td>
                    <td class="text-center">
                        ${transaction.pengeluaran && transaction.pengeluaran_relation?.nama
                        ? transaction.pengeluaran_relation.nama
                        : "-"}
                    </td>
                    <td class="text-center">
                        ${transaction.nominal ? formatNumberWithSeparator(transaction.nominal) : "0"}
                    </td>
                    <td class="text-left">
                        ${transaction.keterangan ? formatKeterangan(transaction.keterangan) : "-"}
                    </td>
                </tr>
            `;
                table.append(row);
            });
        }
    }
    getTodayTransactions();
});

// Expenses Bar
document.addEventListener("DOMContentLoaded", function () {
    let barChart = null;
    let chartData = null;
    let chartLabels = null;
    let chartPengeluaranIds = null;

    function isDarkMode() {
        return document.documentElement.getAttribute("data-bs-theme") === "dark";
    }

    function fetchDataAndRenderChart(month, year) {
        fetch(`/dashboard/jenis-pengeluaran?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                const chartElement = document.querySelector("#barJenisPengeluaran");

                if (!data || data.length === 0 || !chartElement) {
                    if (barChart) {
                        barChart.destroy();
                        barChart = null;
                    }

                    if (chartElement) {
                        const dark = isDarkMode();
                        chartElement.innerHTML = "";
                        chartElement.className = "empty-state-container";
                        chartElement.style.cssText = `
                            width: 100%;
                            height: 200px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            background: ${dark ? '#2b2d31' : '#f8f9fa'};
                            border-radius: 12px;
                            transition: all 0.3s ease;
                        `;

                        chartElement.innerHTML = `
                            <div class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="${dark ? '#6c757d' : '#adb5bd'}" stroke-width="2">
                                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005.5 1"/>
                                </svg>
                                <p class="mt-3 ${dark ? 'text-light' : 'text-muted'}" style="font-size: 0.9rem;">
                                    No data available for this period
                                </p>
                            </div>
                        `;
                    }
                    return;
                } else {
                    chartElement.innerHTML = "";
                }

                const filteredData = data.filter(item => item.pengeluaran_id !== null);
                if (filteredData.length === 0) return;

                chartLabels = filteredData.map(item => item.pengeluaran_nama);
                chartPengeluaranIds = filteredData.map(item => item.pengeluaran_id);
                chartData = filteredData.map(item => parseFloat(item.total) || 0);

                if (barChart) {
                    barChart.destroy();
                    barChart = null;
                }

                const chartHeight = Math.min(600, Math.max(350, filteredData.length * 45));
                const maxXValue = Math.max(...chartData) * 1.2;
                const dark = isDarkMode();

                const options = {
                    series: [{
                        name: 'Total Pengeluaran',
                        data: chartData
                    }],
                    chart: {
                        type: 'bar',
                        height: chartHeight,
                        foreColor: dark ? '#ddd' : '#333',
                        background: 'transparent',
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                const selectedPengeluaranId = chartPengeluaranIds[config.dataPointIndex];
                                if (selectedPengeluaranId) {
                                    fetchTransactionDetails(selectedPengeluaranId, month, year);
                                }
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            horizontal: true,
                            barHeight: "45%",
                            distributed: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            colors: [dark ? '#ffffff' : '#ffffff']
                        },
                        formatter: value => new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(value)
                    },
                    xaxis: {
                        categories: chartLabels,
                        max: maxXValue,
                        labels: {
                            show: false,
                            style: { fontSize: '12px', colors: dark ? '#bbb' : '#444' },
                            formatter: value => new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(value)
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { fontSize: '12px', colors: dark ? '#bbb' : '#444' }
                        }
                    },
                    colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'],
                    tooltip: {
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: value => new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(value)
                        }
                    },
                    grid: {
                        borderColor: dark ? '#444' : '#eee',
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: false } },
                    },
                    states: {
                        hover: {
                            filter: { type: 'lighten', value: 0.15 }
                        },
                        active: {
                            filter: { type: 'darken', value: 0.2 }
                        }
                    },

                };

                barChart = new ApexCharts(chartElement, options);
                barChart.render();
            })
            .catch(error => {
                console.error("Error fetching data:", error);
            });
    }

    function fetchTransactionDetails(pengeluaranId, month, year) {
        if (!pengeluaranId || !month || !year) {
            alert("Data tidak valid");
            return;
        }

        const modalBody = document.querySelector("#modalBodyBarPengeluaran");

        const modalEl = document.getElementById('detailModalBarPengeluaran');
        if (modalEl) {
            const dialog = modalEl.querySelector('.modal-dialog');
            if (dialog) dialog.classList.add('modal-dialog-scrollable');

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            const closeBtn = modalEl.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => modal.hide());
            }

            modalEl.addEventListener('click', (e) => {
                if (e.target === modalEl) modal.hide();
            });
        }

        fetch(`/dashboard/transaksi?pengeluaran=${pengeluaranId}&month=${month}&year=${year}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (isDarkMode()) {
                    modalBody.classList.add('bg-dark', 'text-light');
                } else {
                    modalBody.classList.remove('bg-dark', 'text-light');
                }
                displayTransactionData(data);
            })
            .catch(error => displayError(error.message));
    }

    function displayTransactionData(data) {
        const modalBody = document.querySelector("#modalBodyBarPengeluaran");
        const modalTitle = document.querySelector("#modalTitle");

        if (!modalBody) return;
        modalBody.innerHTML = "";

        const isDark = isDarkMode();

        if (!data || data.length === 0) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center ${isDark ? 'text-light' : 'text-muted'}">
                        <i class="fas fa-inbox"></i><br>
                        Tidak ada transaksi ditemukan
                    </td>
                </tr>`;
        } else {
            let html = data.map((item, index) => {
                const tgl = new Date(item.tgl_transaksi).toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric'
                });

                const ket = (item.keterangan || 'Tidak ada keterangan')
                    .split("\n")
                    .filter(line => line.trim())
                    .map((line, i) => `${i + 1}. ${line}`)
                    .join("<br>");

                return `
                    <tr class="transaction-row">
                        <td class="fw-semibold">${tgl}</td>
                        <td>${ket}</td>
                        <td class="text-end fw-bold text-primary">
                            ${new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(item.nominal)}
                        </td>
                    </tr>`;
            }).join("");

            modalBody.innerHTML = html;

            if (modalTitle && data[0]) {
                modalTitle.textContent = "Detail Transaksi";
                const modalSubTitle = document.querySelector("#modalSubTitle");
                if (modalSubTitle) {
                    modalSubTitle.textContent = `Pengeluaran: ${data[0].pengeluaran_nama}`;
                    modalSubTitle.className = isDark ? "text-light opacity-75" : "text-muted";
                }
            }
        }
    }

    function displayError(msg) {
        const modalBody = document.querySelector("#modalBodyBarPengeluaran");
        if (modalBody) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        <strong>Error:</strong> ${msg}
                    </td>
                </tr>`;
        }
    }

    function updateChartTheme() {
        if (!barChart) return;
        const dark = isDarkMode();

        barChart.updateOptions({
            chart: {
                foreColor: dark ? '#ddd' : '#333'
            },
            dataLabels: {
                style: {
                    colors: [dark ? '#ffffff' : '#ffffff']
                }
            },
            xaxis: {
                labels: {
                    style: { colors: dark ? '#bbb' : '#444' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: dark ? '#bbb' : '#444' }
                }
            },
            tooltip: {
                theme: dark ? 'dark' : 'light'
            }
        });
    }

    // Inisialisasi
    const filterMonth = document.getElementById("filterMonth");
    const filterYear = document.getElementById("filterYear");

    if (filterMonth && filterYear) {
        fetchDataAndRenderChart(filterMonth.value, filterYear.value);

        filterMonth.addEventListener("change", () => {
            fetchDataAndRenderChart(filterMonth.value, filterYear.value);
        });
        filterYear.addEventListener("change", () => {
            fetchDataAndRenderChart(filterMonth.value, filterYear.value);
        });
    } else {
        console.error("Filter elements tidak ditemukan!");
    }

    // Saat ganti tema, update chart langsung tanpa fetch ulang
    document.addEventListener("themeChanged", () => {
        updateChartTheme();
    });
});

// Performance Ratio
document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById("chartKompas");
    let chart;

    const totalPinjaman = parseFloat(chartElement.dataset.totalPinjaman);
    const totalBarang = parseFloat(chartElement.dataset.totalBarang);
    const rasio = parseFloat(chartElement.dataset.rasio);
    const rasio_inflasi = parseFloat(chartElement.dataset.rasioInflasi);
    const rasio_dana_darurat = parseFloat(chartElement.dataset.rasioDanaDarurat);
    const rasio_pengeluaran_pendapatan = parseFloat(chartElement.dataset.rasioPengeluaranPendapatan);

    const getColorByTarget = (value, target) => {
        if (value < 0) return "#800080";
        if (value < target) return "var(--bs-green)";
        if (value > target) return "var(--bs-red)";
        return "#FFA500";
    };

    const getColorDanaDarurat = (value, target) => {
        if (value < target) return "var(--bs-red)";
        return "var(--bs-green)";
    };

    const getColorInflasiGayaHidup = (value, target) => {
        if (value > target) return "#FF4560";
        return "#00E396";
    };

    function AnalisisRasio(rasio) {
        if (rasio < 20) return "Sangat Sehat...";
        if (rasio >= 20 && rasio <= 40) return "Cukup Sehat...";
        return "Waspadai...";
    }

    function AnalisisRasioDanaDarurat(rasio) {
        if (rasio < 100) return "Kurang Aman...";
        if (rasio === 100) return "Ideal...";
        return "Lebih dari Cukup...";
    }

    function AnalisisRasioInflasi(rasio) {
        if (rasio < 0) return "Sangat Ideal...";
        if (rasio >= 50 && rasio <= 80) return "Masih Aman...";
        if (rasio > 80 && rasio <= 100) return "Perlu Waspada...";
        return "Warning...";
    }

    function AnalisisRasioPengeluaranPendapatan(rasio) {
        if (rasio <= 50) return "Sangat Sehat...";
        if (rasio >= 50 && rasio <= 70) return "Cukup Sehat...";
        return "Perlu Waspada...";
    }

    function getTextColor() {
        const theme = document.documentElement.getAttribute("data-bs-theme") || "light";
        return theme === "dark" ? "#ffffff" : "#000000";
    }

    function getChartOptions() {
        const textColor = getTextColor();

        return {
            chart: {
                type: "bar",
                height: 250,
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        const idx = config.dataPointIndex;
                        let title = "", rumus = "", nominal = "", target = "", analisis = "";

                        if (idx === 0) {
                            title = "Rasio Utang terhadap Aset";
                            rumus = "Total Pinjaman / Total Aset";
                            target = '<20.00%';
                            nominal = rasio.toFixed(2) + '%';
                            analisis = AnalisisRasio(rasio);
                        } else if (idx === 1) {
                            title = "Rasio Inflasi Gaya Hidup";
                            rumus = "(Pengeluaran Bulan Ini - Bulan Lalu)/(Pemasukan Bulan Ini - Lalu) x 100%";
                            target = '0%';
                            nominal = rasio_inflasi.toFixed(2) + '%';
                            analisis = AnalisisRasioInflasi(rasio_inflasi);
                        } else if (idx === 2) {
                            title = "Rasio Dana Darurat";
                            rumus = "Total Dana Darurat / (Pengeluaran Bulanan * 6)";
                            target = '100%';
                            nominal = rasio_dana_darurat.toFixed(2) + '%';
                            analisis = AnalisisRasioDanaDarurat(rasio_dana_darurat);
                        } else if (idx === 3) {
                            title = "Rasio Pengeluaran terhadap Pendapatan";
                            rumus = "Pengeluaran / Pendapatan";
                            target = '100%';
                            nominal = rasio_pengeluaran_pendapatan.toFixed(2) + '%';
                            analisis = AnalisisRasioPengeluaranPendapatan(rasio_pengeluaran_pendapatan);
                        }

                        showModal(title, rumus, nominal, target, analisis);
                    }
                }
            },
            series: [{
                name: "Rasio",
                data: [
                    {
                        x: ["Rasio Utang", "terhadap Aset"],
                        y: rasio > 0 ? rasio : 0.5,
                        fillColor: getColorByTarget(rasio, 20),
                        goals: [{ name: "Target", value: 20, strokeWidth: 4, strokeColor: "#9B59B6" }]
                    },
                    {
                        x: "Rasio Inflasi Gaya Hidup",
                        y: rasio_inflasi > 0 ? rasio_inflasi : 0.5,
                        fillColor: getColorInflasiGayaHidup(rasio_inflasi, 0),
                        goals: [{ name: "Target", value: 0.5, strokeWidth: 4, strokeColor: "#9B59B6" }]
                    },
                    {
                        x: ["Rasio Dana", "Darurat"],
                        y: rasio_dana_darurat > 0 ? rasio_dana_darurat : 0.5,
                        fillColor: getColorDanaDarurat(rasio_dana_darurat, 100),
                        goals: [{ name: "Target", value: 100, strokeWidth: 4, strokeColor: "#9B59B6" }]
                    },
                    {
                        x: ["Rasio Pengeluaran", "Terhadap Pendapatan"],
                        y: rasio_pengeluaran_pendapatan > 0 ? rasio_pengeluaran_pendapatan : 0.5,
                        fillColor: getColorByTarget(rasio_pengeluaran_pendapatan, 100),
                        goals: [{ name: "Target", value: 100, strokeWidth: 4, strokeColor: "#9B59B6" }]
                    }
                ]
            }],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 8
                }
            },
            dataLabels: {
                enabled: true,
                formatter: val => `${val.toFixed(2)}%`,
                style: {
                    colors: [textColor]
                }
            },
            tooltip: { enabled: false },
            xaxis: {
                title: {
                    text: "%",
                    style: { colors: textColor }
                },
                labels: {
                    style: { colors: textColor }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor }
                }
            }
        };
    }

    function updateChartTheme() {
        const textColor = getTextColor();
        if (chart) {
            chart.updateOptions({
                dataLabels: {
                    style: {
                        colors: [textColor]
                    }
                },
                xaxis: {
                    title: {
                        style: { colors: textColor }
                    },
                    labels: {
                        style: { colors: textColor }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: textColor }
                    }
                }
            });
        }
    }

    function isDarkMode() {
        return document.documentElement.getAttribute("data-bs-theme") === "dark";
    }

    function updateModalTheme() {
        const dark = isDarkMode();
        const modalContent = document.querySelector("#detailModal .modal-content");
        if (!modalContent) return;

        modalContent.classList.toggle("bg-dark", dark);
        modalContent.classList.toggle("text-white", dark);
        modalContent.classList.toggle("bg-white", !dark);
        modalContent.classList.toggle("text-dark", !dark);
    }

    function showModal(title, rumus, nominal, target, analisis) {
        const modal = document.getElementById("detailModal");
        document.getElementById("modalTitle").innerText = title;
        document.getElementById("modalRumus").innerText = rumus;
        document.getElementById("modalNominal").innerText = nominal;
        document.getElementById("modalTarget").innerText = target;
        document.getElementById("modalAnalisis").innerText = analisis;

        updateModalTheme(); // Terapkan tema modal

        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }


    // Buat chart awal
    chart = new ApexCharts(chartElement, getChartOptions());
    chart.render();

    // Saat tema berubah, update chart
    document.addEventListener("themeChanged", function () {
        updateChartTheme();
    });

    // Expose jika perlu manual trigger
    window.updateChartTheme = updateChartTheme;
});

document.addEventListener("DOMContentLoaded", function () {
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

    // ==== NOMINAL DISPLAY HANDLER ====
    function updateNominalDisplay(isHidden) {
        document.querySelectorAll(".box-info h3").forEach(h3 => {
            h3.textContent = isHidden ? "****" : h3.getAttribute("data-value");
        });
    }

    function initializeNominalDisplay() {
        const isHidden = localStorage.getItem("nominalHidden") === "true";
        updateNominalDisplay(isHidden);
    }

    function toggleNominal() {
        const isHidden = localStorage.getItem("nominalHidden") === "true";
        const newState = !isHidden;
        localStorage.setItem("nominalHidden", newState);
        updateNominalDisplay(newState);
    }

    // Eksekusi awal nominal
    initializeNominalDisplay();
    window.toggleNominal = toggleNominal;
});

// Saving rate
document.addEventListener("DOMContentLoaded", function () {
    const charts = {};           // Menyimpan semua instance chart
    const chartData = {};        // Menyimpan data untuk tiap chart

    const theme = () => document.documentElement.getAttribute("data-bs-theme") || "light";

    /**
     * Fungsi validasi data chart
     */
    function isValidChartData(data) {
        return data && Array.isArray(data.labels);
    }

    /**
     * Fungsi dasar konfigurasi chart Apex
     */
    function getBaseChartOptions(type, series, categories, customOptions = {}) {
        const isDark = theme() === "dark";

        return {
            chart: {
                type: type,
                height: 350,
                toolbar: { show: true, tools: { download: false } },
                foreColor: isDark ? '#fff' : '#333'
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: { colors: isDark ? '#ccc' : '#555' }
                },
                title: {
                    style: { color: isDark ? '#ccc' : '#555' }
                }
            },
            yaxis: {
                labels: {
                    formatter: val => val.toLocaleString('id-ID'),
                    style: { colors: isDark ? '#ccc' : '#555' }
                },
                title: {
                    text: "Nominal (Rp)",
                    style: { color: isDark ? '#ccc' : '#555' }
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: val => "Rp " + val.toLocaleString('id-ID')
                }
            },
            stroke: { curve: "smooth", width: 2 },
            markers: { size: 5 },
            colors: ['#28a745'],
            legend: {
                position: "top",
                horizontalAlign: "center"
            },
            series: series,
            ...customOptions
        };
    }

    /**
     * Fungsi untuk memfilter data berdasarkan jumlah bulan terakhir
     */
    function filterData(data, months) {
        if (!isValidChartData(data)) {
            console.warn("filterData error: data.labels tidak valid", data);
            return { labels: [], pemasukan: [], pengeluaran: [] };
        }

        if (months === "all") return data;

        const startIndex = Math.max(data.labels.length - months, 0);
        const filtered = { labels: data.labels.slice(startIndex) };

        Object.keys(data).forEach((key) => {
            if (Array.isArray(data[key])) {
                filtered[key] = data[key].slice(startIndex);
            }
        });

        return filtered;
    }    /**
     * Fungsi umum render chart
     */
    function renderChart(selector, type, series, categories, options = {}) {
        const el = document.querySelector(selector);
        if (!el) return;

        const config = getBaseChartOptions(type, series, categories, options);

        if (charts[selector]) {
            charts[selector].updateOptions(config);
        } else {
            charts[selector] = new ApexCharts(el, config);
            charts[selector].render();
        }
    }

    /**
     * FETCH & RENDER saving rate chart
     */
    function loadSavingRateChart(period = '6') {
        const url = `/dashboard/saving-rate-data?periode=${period}`;

        const loader = document.getElementById("savingRateLoader");
        if (loader) loader.style.display = "inline-block";

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!isValidChartData(data)) throw new Error("Invalid chart data");

                chartData.savingRate = data;
                renderChart("#savingRateChart", "line", [
                    { name: "Saving Rate", data: data.data }
                ], data.labels, {
                    markers: {
                        size: 5,
                        strokeColors: '#28a745',
                        strokeWidth: 2,
                        hover: { size: 7 }
                    }
                });
            })
            .catch(err => console.error("Gagal memuat saving rate:", err))
            .finally(() => {
                if (loader) loader.style.display = "none";
            });
    }

    /**
     * ON FILTER CHANGE
     */
    const filterSaving = document.getElementById("filterPeriodSavingRate");
    if (filterSaving) {
        loadSavingRateChart(filterSaving.value);

        filterSaving.addEventListener("change", function () {
            loadSavingRateChart(this.value);
        });

        document.addEventListener("themeChanged", function () {
            loadSavingRateChart(filterSaving.value);
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const avgPemasukan = parseFloat(document.getElementById("rataPemasukan").dataset.value);
    const avgPengeluaran = parseFloat(document.getElementById("rataPengeluaran").dataset.value);

    console.log("Pemasukan:", avgPemasukan, "Pengeluaran:", avgPengeluaran);
});
