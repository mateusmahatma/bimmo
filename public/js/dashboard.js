// Arus Kas
document.addEventListener("DOMContentLoaded", function () {
    let charts = {}; // Menyimpan semua instance chart
    let chartData = {}; // Menyimpan data mentah chart

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
        const defaultOptions = {
            chart: {
                type: type,
                height: 350,
                toolbar: { show: true, tools: { download: true } },
            },
            xaxis: { categories: data.labels },
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
                { name: "Pemasukan", data: filteredData.pemasukan },
                { name: "Pengeluaran", data: filteredData.pengeluaran },
            ],
            tooltip: {
                theme: "light",
                y: {
                    formatter: (val) => "Rp " + val.toLocaleString(),
                },
            },
        });
    }

    // Fungsi render chart pendapatan dan pengeluaran
    function renderIncomeExpenseChart(data, months = "all") {
        const filteredData = filterData(data, months);
        renderChart("line", "#barChart", filteredData, {
            series: [
                { name: "Pengeluaran", data: filteredData.data_pengeluaran },
                { name: "Pemasukan", data: filteredData.data_pemasukan },
            ],
            yaxis: [
                { title: { text: "Pengeluaran (IDR)" } },
                { opposite: true, title: { text: "Pemasukan (IDR)" } },
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

    // Tampilan default saat load
    window.onload = () => {
        const columnChart = document.getElementById("columnChart");
        const barChart = document.getElementById("barChart");
        columnChart.style.display = "block";
        columnChart.classList.add("show");
        barChart.style.display = "none";
    };
});

// Handle Show Hide Nominal
function toggleNominal() {
    // Ambil elemen-elemen <h3> yang ada dalam .box-info
    const h3Elements = document.querySelectorAll(".box-info h3");

    // Periksa apakah data saat ini sedang disembunyikan
    const isHidden = localStorage.getItem("nominalHidden") === "true";

    // Lakukan perubahan pada setiap elemen <h3>
    h3Elements.forEach((h3) => {
        if (isHidden) {
            // Tampilkan nilai asli dari data-value
            h3.textContent = h3.getAttribute("data-value");
        } else {
            // Sembunyikan nilai dengan asterisk
            h3.textContent = "****";
        }
    });

    // Perbarui status di localStorage
    localStorage.setItem("nominalHidden", !isHidden);
}

// Expenses Bar
document.addEventListener("DOMContentLoaded", function () {
    let barChart = null;

    function fetchDataAndRenderChart(month, year) {
        fetch(`/dashboard/jenis-pengeluaran?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                // console.log("Data dari API:", data); // Debug data

                if (!data || data.length === 0) {
                    if (barChart) {
                        barChart.destroy();
                        barChart = null;
                    }

                    const chartElement = document.querySelector("#barJenisPengeluaran");
                    if (chartElement) {
                        chartElement.style.height = "100px";
                        chartElement.style.minHeight = "unset";
                        chartElement.style.maxHeight = "unset";
                        chartElement.style.padding = "0";
                        chartElement.style.display = "flex";
                        chartElement.style.justifyContent = "center";
                        chartElement.style.alignItems = "center";
                        chartElement.style.background = "#f3f3f3";
                        chartElement.innerHTML = `<span class="text-muted">No data available for this month/year.</span>`;
                    }
                    return
                }
                else {
                    const chartElement = document.querySelector("#barJenisPengeluaran");
                    if (chartElement) {
                        chartElement.style.background = "#ffffff"; // background putih saat grafik tampil
                    }
                }


                // Filter data yang punya pengeluaran_id tidak null
                const filteredData = data.filter(item => item.pengeluaran_id !== null);

                if (filteredData.length === 0) {
                    console.log("Tidak ada data yang valid");
                    return;
                }

                // PERBAIKAN: Ambil ID dan nama dari field yang benar
                const labels = filteredData.map(item => item.pengeluaran_nama);
                const pengeluaranIds = filteredData.map(item => item.pengeluaran_id); // PERBAIKAN: gunakan pengeluaran_id
                const values = filteredData.map(item => parseFloat(item.total) || 0);

                // debug
                // console.log("Labels:", labels);
                // console.log("Pengeluaran IDs:", pengeluaranIds);
                // console.log("Values:", values);

                const chartElement = document.querySelector("#barJenisPengeluaran");
                if (!chartElement) {
                    // console.error("Element chart tidak ditemukan!");
                    return;
                }

                chartElement.innerHTML = "";
                if (barChart) {
                    barChart.destroy();
                    barChart = null;
                }

                var chartHeight = Math.min(800, Math.max(400, filteredData.length * 60));
                var maxXValue = Math.max(...values) * 1.2;

                var options = {
                    series: [{
                        name: 'Total Pengeluaran',
                        data: values
                    }],
                    chart: {
                        type: 'bar',
                        height: chartHeight,
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                // console.log("Bar diklik:", config);
                                // console.log("Data point index:", config.dataPointIndex);

                                const selectedPengeluaranId = pengeluaranIds[config.dataPointIndex];
                                // console.log("Selected pengeluaran ID:", selectedPengeluaranId);

                                if (selectedPengeluaranId) {
                                    fetchTransactionDetails(selectedPengeluaranId, month, year);
                                } else {
                                    // console.error("Pengeluaran ID tidak ditemukan untuk index:", config.dataPointIndex);
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
                        formatter: function (value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    },
                    xaxis: {
                        categories: labels,
                        max: maxXValue,
                        labels: {
                            style: { fontSize: '12px' },
                            formatter: function (value) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { fontSize: '12px' }
                        }
                    },
                    colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'],
                    tooltip: {
                        y: {
                            formatter: function (value) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    }
                };

                barChart = new ApexCharts(chartElement, options);
                barChart.render()
                // .then(() => {
                //     console.log("Chart berhasil di-render");
                // })
                // .catch(error => {
                //     console.error("Error rendering chart:", error);
                // });
            })
            .catch(error => {
                console.error("Error fetching data:", error);
            });
    }

    function fetchTransactionDetails(pengeluaranId, month, year) {
        // console.log("Fetching transaction details untuk:", { pengeluaranId, month, year });

        // Validasi parameter
        if (!pengeluaranId || pengeluaranId === 'undefined' || pengeluaranId === null) {
            console.error("pengeluaranId tidak valid:", pengeluaranId);
            alert("Data pengeluaran tidak valid. Silakan coba lagi.");
            return;
        }

        if (!month || !year) {
            console.error("Month atau year tidak valid:", { month, year });
            alert("Parameter bulan/tahun tidak valid.");
            return;
        }

        // Tampilkan loading di modal
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

        // Tampilkan modal terlebih dahulu
        const modal = new bootstrap.Modal(document.getElementById('detailModalBarPengeluaran'));
        modal.show();

        fetch(`/dashboard/transaksi?pengeluaran=${pengeluaranId}&month=${month}&year=${year}`)
            .then(response => {
                // console.log("Response status:", response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // console.log("Response transaksi:", data);
                displayTransactionData(data);
            })
            .catch(error => {
                console.error("Error fetching transactions:", error);
                displayError(error.message);
            });
    }

    function displayTransactionData(data) {
        const modalBody = document.querySelector("#modalBodyBarPengeluaran");

        if (!modalBody) {
            console.error("Modal body tidak ditemukan!");
            return;
        }

        modalBody.innerHTML = "";

        if (!data || data.length === 0) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-inbox"></i><br>
                        Tidak ada transaksi ditemukan
                    </td>
                </tr>`;
        } else {
            let htmlContent = "";
            data.forEach((item, index) => {
                let tanggalTransaksi = new Date(item.tgl_transaksi).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                let keterangan = item.keterangan || 'Tidak ada keterangan';
                let numberedKeterangan = keterangan
                    .split("\n")
                    .filter(line => line.trim() !== '')
                    .map((line, idx) => `${idx + 1}. ${line.trim()}`)
                    .join("<br>");

                htmlContent += `
                    <tr class="transaction-row">
                        <td class="fw-semibold">${tanggalTransaksi}</td>
                        <td>${numberedKeterangan}</td>
                        <td class="text-end fw-bold text-primary">
                            ${new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(item.nominal)}
                        </td>
                    </tr>`;
            });
            modalBody.innerHTML = htmlContent;

            // Update modal title dengan nama pengeluaran
            const modalTitle = document.querySelector("#modalTitle");
            if (modalTitle && data.length > 0) {
                modalTitle.textContent = `Detail Transaksi - ${data[0].pengeluaran_nama}`;
            }
        }
    }

    function displayError(errorMessage) {
        const modalBody = document.querySelector("#modalBodyBarPengeluaran");
        if (modalBody) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        <strong>Error:</strong> ${errorMessage}
                    </td>
                </tr>`;
        }
    }

    // Inisialisasi
    const filterMonth = document.getElementById("filterMonth");
    const filterYear = document.getElementById("filterYear");

    if (filterMonth && filterYear) {
        // Load data pertama kali
        fetchDataAndRenderChart(filterMonth.value, filterYear.value);

        // Event listeners
        filterMonth.addEventListener("change", () => {
            fetchDataAndRenderChart(filterMonth.value, filterYear.value);
        });

        filterYear.addEventListener("change", () => {
            fetchDataAndRenderChart(filterMonth.value, filterYear.value);
        });
    } else {
        console.error("Filter elements tidak ditemukan!");
    }
});

// Transaksi Hari ini
$(document).ready(function () {
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

document.addEventListener("DOMContentLoaded", () => {
    initializeNominalDisplay();
});

// Cek status toggle saat halaman dimuat
window.addEventListener('load', () => {
    const isHidden = localStorage.getItem("nominalHidden") === "true";
    const h3Elements = document.querySelectorAll(".box-info h3");

    h3Elements.forEach((h3) => {
        if (isHidden) {
            h3.textContent = "****";
        } else {
            h3.textContent = h3.getAttribute("data-value");
        }
    });
});

// Inisialisasi saat halaman dimuat show hide nominal
document.addEventListener("DOMContentLoaded", () => {
    // Ambil status terakhir dari localStorage
    const isHidden = localStorage.getItem("nominalHidden") === "true";

    // Sinkronkan tampilan sesuai status
    const h3Elements = document.querySelectorAll(".box-info h3");
    h3Elements.forEach((h3) => {
        h3.textContent = isHidden ? "****" : h3.getAttribute("data-value");
    });
});

// Hidden nominal
function initializeNominalDisplay() {
    const isHidden = localStorage.getItem("nominalHidden") === "true";
    const h3Elements = document.querySelectorAll(".box-info h3");

    h3Elements.forEach((h3) => {
        if (isHidden) {
            h3.textContent = "****";
        } else {
            h3.textContent = h3.getAttribute("data-value");
        }
    });
}

// Compass
document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById("chartKompas");

    // Ambil data dari atribut HTML
    const totalPinjaman = parseFloat(chartElement.dataset.totalPinjaman);
    const totalBarang = parseFloat(chartElement.dataset.totalBarang);
    const rasio = parseFloat(chartElement.dataset.rasio);
    const rasio_inflasi = parseFloat(chartElement.dataset.rasioInflasi);
    const rasio_dana_darurat = parseFloat(chartElement.dataset.rasioDanaDarurat);
    const rasio_pengeluaran_pendapatan = parseFloat(chartElement.dataset.rasioPengeluaranPendapatan);

    // Fungsi untuk menentukan warna berdasarkan target
    const getColorByTarget = (value, target) => {
        if (value < 0) return "#800080";
        if (value < target) return "#00E396";
        if (value > target) return "#FF4560";
        return "#FFA500";
    };

    const getColorDanaDarurat = (value, target) => {
        if (value < target) return "#FF4560";
        if (value === target) return "#00E396";
        return "#00E396";
    };

    const getColorInflasiGayaHidup = (value, target) => {
        if (value > target) return "#FF4560";
        if (value === target) return "#00E396";
        if (value < target) return "#00E396";
        return "#00E396";
    };

    // Fungsi untuk analisis rasio
    function AnalisisRasio(rasio) {
        if (rasio < 20) {
            return "Sangat Sehat : Kamu memiliki sedikit utang dibandingkan aset. Ini menunjukkan stabilitas keuangan yang tinggi dan ruang yang luas untuk investasi atau pengembangan aset.";
        } else if (rasio >= 20 && rasio <= 40) {
            return "Cukup Sehat : Masih berada dalam batas aman, tetapi sebaiknya kamu mulai mengendalikan utang baru dan fokus membangun aset.";
        }
        return "Waspadai : Rasio utang terhadap aset sudah mulai tinggi. Segera kurangi utang atau tingkatkan aset.";
    }

    function AnalisisRasioDanaDarurat(rasio_dana_darurat) {
        if (rasio_dana_darurat < 100) {
            return "Kurang Aman : Dana darurat belum mencapai target. Segera tingkatkan tabungan untuk mencapai keamanan finansial.";
        } else if (rasio_dana_darurat === 100) {
            return "Ideal : Dana darurat sudah mencapai target. Tetap pertahankan dan perbarui sesuai kebutuhan.";
        } else {
            return "Lebih dari Cukup : Dana darurat melebihi target. Pertimbangkan untuk mengalokasikan ke investasi atau kebutuhan lainnya.";
        }
    }

    function AnalisisRasioInflasi(rasio_inflasi) {
        if (rasio_inflasi < 0) {
            return "Sangat Ideal : Pengeluaran naik jauh lebih lambat dibanding kenaikan pendapatan. Artinya, kamu memiliki ruang lebih untuk menabung, berinvestasi, atau mempercepat pencapaian tujuan keuangan.";
        } else if (rasio_inflasi >= 50 && rasio_inflasi <= 80) {
            return "Masih Aman : Pengeluaran tetap terkendali, tetapi kenaikan sudah mulai signifikan. Sebaiknya mulai dievaluasi, apalagi jika pengeluaran ini bersifat konsumtif.";
        } else if (rasio_inflasi > 80 && rasio_inflasi <= 100) {
            return "Perlu Waspada : Hampir seluruh kenaikan pendapatan terpakai untuk pengeluaran. Kurangi pengeluaran yang tidak perlu agar bisa meningkatkan tabungan atau investasi.";
        } else {
            return "Warning : Pengeluaran naik lebih cepat dari pendapatan. Ini bisa menjadi pertanda kamu mulai terjebak dalam inflasi gaya hidup.";
        }
    }

    function AnalisisRasioPengeluaranPendapatan(rasio_pengeluaran_pendapatan) {
        if (rasio_pengeluaran_pendapatan <= 50) {
            return "Sangat Sehat : Pengeluaran terkendali, kamu memiliki cukup ruang untuk menabung, berinvestasi, dan mengatur dana darurat. Ideal untuk mencapai kebebasan finansial.";
        } else if (rasio_pengeluaran_pendapatan >= 50 && rasio_pengeluaran_pendapatan <= 70) {
            return "Cukup Sehat : Masih dalam batas aman, tapi sebaiknya mulai mengevaluasi pengeluaran yang bisa dikurangi, terutama yang bersifat konsumtif.";
        } else {
            return "Perlu Waspada : Pengeluaran sudah mulai melebihi pendapatan. Segera kurangi pengeluaran yang tidak perlu atau tingkatkan pendapatan.";
        }
    }

    var options = {
        chart: {
            type: "bar",
            height: 250,
            events: {
                dataPointSelection: function (event, chartContext, config) {
                    const selectedIndex = config.dataPointIndex;
                    let title = "";
                    let rumus = "";
                    let nominal = "";
                    let target = 0;

                    if (selectedIndex === 0) {
                        title = "Rasio Utang terhadap Aset";
                        rumus = "Total Pinjaman / Total Aset";
                        target = '<20.00';
                        nominal = rasio.toFixed(2) + '%';
                        Analisis = AnalisisRasio(rasio);
                    } else if (selectedIndex === 1) {
                        title = "Rasio Inflasi Gaya Hidup";
                        rumus = "(Pengeluaran Bulan Ini - Pengeluaran Bulan Sebelumnya)/(Pemasukan Bulan Ini - Pemasukan Bulan Sebelumnya) x 100%";
                        target = '0';
                        nominal = rasio_inflasi.toFixed(2) + '%';
                        Analisis = AnalisisRasioInflasi(rasio_inflasi);
                    } else if (selectedIndex === 2) {
                        title = "Rasio Dana Darurat";
                        rumus = "Total Dana Darurat / (Rata-rata Total Pengeluaran Setiap Bulan * 6)";
                        target = '100';
                        nominal = rasio_dana_darurat.toFixed(2) + '%';
                        Analisis = AnalisisRasioDanaDarurat(rasio_dana_darurat);
                    } else if (selectedIndex === 3) {
                        title = "Rasio Pengeluaran Terhadap Pendapatan";
                        rumus = "Total Pengeluaran Bulanan / Total Pendapatan Bulanan";
                        target = '50';
                        nominal = rasio_pengeluaran_pendapatan.toFixed(2) + '%';
                        Analisis = AnalisisRasioPengeluaranPendapatan(rasio_pengeluaran_pendapatan);
                    }

                    showModal(title, rumus, nominal, `${target}%`);
                }
            }
        },
        states: {
            hover: {
                filter: {
                    type: "darken",
                    value: 0.15
                }
            }
        },
        series: [
            {
                name: "Rasio",
                data: [
                    {
                        x: ["Rasio Utang", "terhadap Aset"],
                        y: rasio > 0 ? rasio : 0.5,
                        fillColor: getColorByTarget(rasio, 20),
                        goals: [
                            {
                                name: "Target Rasio Utang terhadap Aset",
                                value: 20,
                                strokeWidth: 4,
                                strokeColor: "#9B59B6"
                            }
                        ]
                    },
                    {
                        x: "Rasio Inflasi Gaya Hidup",
                        y: rasio_inflasi > 0 ? rasio_inflasi : 0.5,
                        fillColor: getColorInflasiGayaHidup(rasio_inflasi, 0),
                        goals: [
                            {
                                name: "Target Rasio Inflasi Gaya Hidup",
                                value: 0,
                                strokeWidth: 4,
                                strokeColor: "#9B59B6"
                            }
                        ]
                    },
                    {
                        x: ["Rasio Dana", "Darurat"],
                        y: rasio_dana_darurat > 0 ? rasio_dana_darurat : 0.5,
                        fillColor: getColorDanaDarurat(rasio_dana_darurat, 3),
                        goals: [
                            {
                                name: "Target Rasio Dana Darurat",
                                value: 100,
                                strokeWidth: 4,
                                strokeColor: "#9B59B6"
                            }
                        ]
                    },
                    {
                        x: ["Rasio Pengeluaran", "Terhadap Pendapatan"],
                        y: rasio_pengeluaran_pendapatan > 0 ? rasio_pengeluaran_pendapatan : 0.5,
                        fillColor: getColorByTarget(rasio_pengeluaran_pendapatan, 70),
                        goals: [
                            {
                                name: "Target Rasio Dana Darurat",
                                value: 49,
                                strokeWidth: 4,
                                strokeColor: "#9B59B6"
                            }
                        ]
                    }
                ]
            }
        ],
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 8
            }
        },
        colors: ["#FF4560", "#008FFB", "#00E396"],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return `${val.toFixed(2)}%`;
            }
        },
        tooltip: {
            enabled: false
        },
        xaxis: {
            title: {
                text: "Nilai"
            }
        },
    };

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    // Fungsi untuk menampilkan modal detail
    function showModal(title, rumus, nominal, target, capaian) {
        const modal = document.getElementById("detailModal");
        document.getElementById("modalTitle").innerText = title;
        document.getElementById("modalRumus").innerText = rumus;
        document.getElementById("modalNominal").innerText = nominal;
        document.getElementById("modalTarget").innerText = target;
        document.getElementById("modalAnalisis").innerText = Analisis;


        // Tampilkan modal (asumsi menggunakan Bootstrap)
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
});