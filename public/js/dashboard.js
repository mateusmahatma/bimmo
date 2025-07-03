// Cash Flow
document.addEventListener("DOMContentLoaded", function () {
    let charts = {};
    let chartData = {};

    // Fungsi untuk memfilter data berdasarkan bulan yang dipilih
    function filterData(data, months) {
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

    // Fungsi umum untuk merender chart
    function renderChart(type, selector, data, options) {
        const theme = document.documentElement.getAttribute("data-bs-theme") || "light";
        const isDark = theme === "dark";

        const defaultOptions = {
            chart: {
                type: type,
                height: 350,
                toolbar: { show: true, tools: { download: false } },
                foreColor: isDark ? '#fff' : '#333'
            },
            xaxis: {
                categories: data.labels,
                labels: {
                    style: {
                        colors: isDark ? '#ccc' : '#555'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: isDark ? '#ccc' : '#555'
                    },
                    formatter: (val) => val.toLocaleString('id-ID')
                }
            },
            dataLabels: {
                style: {
                    colors: [isDark ? '#fff' : '#000']
                }
            },
            tooltip: {
                theme: isDark ? "dark" : "light"
            },
            series: [],
        };

        const finalOptions = { ...defaultOptions, ...options };

        if (charts[selector]) {
            charts[selector].updateOptions(finalOptions);
        } else {
            charts[selector] = new ApexCharts(document.querySelector(selector), finalOptions);
            charts[selector].render();
        }
    }

    document.addEventListener("themeChanged", function () {
        const currentTheme = document.documentElement.getAttribute("data-bs-theme") || "light";

        // Render ulang chart dengan tema baru
        const months = document.getElementById("filterPeriod")?.value || "all";
        const parsedMonths = months === "all" ? "all" : parseInt(months);

        renderCashFlowChart(chartData["cashFlow"], parsedMonths);
        renderIncomeExpenseChart(chartData["incomeExpense"], parsedMonths);
    });

    // Ambil data dan render chart awal
    Promise.all([
        fetch("/dashboard/line-data").then((res) => res.json()),
        fetch("/dashboard/chart-data").then((res) => res.json()),
    ]).then(([cashFlowData, incomeExpenseData]) => {
        chartData["cashFlow"] = cashFlowData;
        chartData["incomeExpense"] = incomeExpenseData;

        renderCashFlowChart(cashFlowData, 6);
        renderIncomeExpenseChart(incomeExpenseData, 6);

        // Event listener untuk filter periode
        document.getElementById("filterPeriod").addEventListener("change", handleFilterChange);
    });

    // Fungsi render chart arus kas
    function renderCashFlowChart(data, months = "all") {
        const filteredData = filterData(data, months);
        renderChart("bar", "#columnChart", filteredData, {
            plotOptions: {
                bar: { columnWidth: "45%", borderRadius: 5 },
            },
            dataLabels: {
                enabled: false,
            },
            series: [
                { name: "Expense", data: filteredData.pemasukan },
                { name: "Income", data: filteredData.pengeluaran },
            ],
            tooltip: {
                theme: (document.documentElement.getAttribute("data-bs-theme") === "dark") ? "dark" : "light",
                y: {
                    formatter: (val) => "Rp " + val.toLocaleString(),
                },
            },
        });
    }

    // Fungsi render line
    function renderIncomeExpenseChart(data, months = "all") {
        const filteredData = filterData(data, months);
        renderChart("line", "#barChart", filteredData, {
            series: [
                { name: "Income", data: filteredData.data_pemasukan },
                { name: "Expense", data: filteredData.data_pengeluaran },
            ],
            yaxis: [
                {
                    labels: {
                        formatter: (val) => val.toLocaleString('id-ID')
                    }
                }
            ],
        });
    }

    // Fungsi untuk menangani perubahan filter
    function handleFilterChange(e) {
        const months = e.target.value === "all" ? "all" : parseInt(e.target.value);
        renderCashFlowChart(chartData["cashFlow"], months);
        renderIncomeExpenseChart(chartData["incomeExpense"], months);
    }

    // Toggle antara chart dengan animasi
    document.getElementById("chartType").addEventListener("change", toggleChart);

    function toggleChart() {
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
                refreshChart(columnChart);
            } else {
                barChart.style.display = "block";
                setTimeout(() => barChart.classList.add("show"), 50);
                refreshChart(barChart);
            }
        }, 300);
    }

    // Refresh chart untuk memaksa resize
    function refreshChart(chartElement) {
        setTimeout(() => {
            chartElement.style.display = "block";
            window.dispatchEvent(new Event("resize"));
        }, 0);
    }

    // Tampilan chart saat halaman dimuat
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
        var table = $("#todayTransactionsTable");

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
                        ${transaction.pemasukan && transaction.pemasukan.nama ? transaction.pemasukan.nama : "-"}
                    </td>
                    <td class="text-center">
                        ${transaction.nominal_pemasukan ? formatNumberWithSeparator(transaction.nominal_pemasukan) : "0"}
                    </td>
                    <td class="text-center">
                        ${transaction.pengeluaran && transaction.pengeluaran.nama ? transaction.pengeluaran.nama : "-"}
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
                        chartElement.style.height = "100px";
                        chartElement.style.minHeight = "unset";
                        chartElement.style.maxHeight = "unset";
                        chartElement.style.padding = "0";
                        chartElement.style.display = "flex";
                        chartElement.style.justifyContent = "center";
                        chartElement.style.alignItems = "center";
                        chartElement.style.background = dark ? "#2c2f33" : "#c9c9c9";
                        chartElement.innerHTML = `<span class="${dark ? 'text-light' : 'text-muted'}">No data available for this month/year.</span>`;
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

                const chartHeight = Math.min(800, Math.max(400, filteredData.length * 60));
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
                            borderRadius: 4,
                            horizontal: true,
                            barHeight: "70%",
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
                    }
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
        if (modalBody) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading...
                    </td>
                </tr>`;
        }

        const modal = new bootstrap.Modal(document.getElementById('detailModalBarPengeluaran'));
        modal.show();

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
                modalTitle.textContent = `Detail Transaksi - ${data[0].pengeluaran_nama}`;
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
        if (value < target) return "#00E396";
        if (value > target) return "#FF4560";
        return "#FFA500";
    };

    const getColorDanaDarurat = (value, target) => {
        if (value < target) return "#FF4560";
        return "#00E396";
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
                            target = '50%';
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
                        goals: [{ name: "Target", value: 0, strokeWidth: 4, strokeColor: "#9B59B6" }]
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
                        fillColor: getColorByTarget(rasio_pengeluaran_pendapatan, 50),
                        goals: [{ name: "Target", value: 49, strokeWidth: 4, strokeColor: "#9B59B6" }]
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
    // ==== THEME HANDLER ====
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