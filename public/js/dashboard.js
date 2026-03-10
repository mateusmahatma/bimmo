document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = window.csrfToken;

    // ==== DASHBOARD FILTER HANDLER (Global elements that aren't specific to Cash Flow) ====
    // Note: Cash Flow specific filtering is now in dashboard-cashflow.js

    // Expenses Bar & Table Initial Load
    const filterBulan = document.getElementById("filterBulan");
    const filterTahun = document.getElementById("filterTahun");

    function updateExpenseBar(month, year) {
        if (window.fetchDataAndRenderChart) window.fetchDataAndRenderChart(month, year);

        fetch(`${window.dashboardFilterUrl}?bulan=${month}&tahun=${year}`)
            .then(res => res.json())
            .then(data => {
                const tableContainer = document.getElementById("expenseBarTableContainer");
                const totalValue = document.getElementById("totalPengeluaranValue");
                if (tableContainer) tableContainer.innerHTML = data.expenseBar;
                if (totalValue) totalValue.textContent = "Rp " + data.totalPengeluaran;
            })
            .catch(err => console.error("Error updating expense bar:", err));
    }

    if (filterBulan && filterTahun) {
        const handleExpenseFilter = () => updateExpenseBar(filterBulan.value, filterTahun.value);
        filterBulan.addEventListener("change", handleExpenseFilter);
        filterTahun.addEventListener("change", handleExpenseFilter);
    }

    // ==== PERFORMANCE RATIO RADAR/BAR CHART ====
    const chartElement = document.getElementById("chartKompas");
    if (chartElement) {
        let chart;
        const rasio = parseFloat(chartElement.dataset.rasio);
        const rasio_inflasi = parseFloat(chartElement.dataset.rasioInflasi);
        const rasio_dana_darurat = parseFloat(chartElement.dataset.rasioDanaDarurat);
        const rasio_pengeluaran_pendapatan = parseFloat(chartElement.dataset.rasioPengeluaranPendapatan);

        const getTextColor = () => document.documentElement.getAttribute("data-bs-theme") === "dark" ? "#ffffff" : "#000000";

        const getColor = (val, target, inverse = false) => {
            if (inverse) return val >= target ? "var(--bs-success)" : "var(--bs-danger)";
            return val <= target ? "var(--bs-success)" : "var(--bs-danger)";
        };

        const options = {
            chart: { type: "bar", height: 300, toolbar: { show: false }, foreColor: getTextColor() },
            plotOptions: { bar: { horizontal: true, borderRadius: 8 } },
            series: [{
                name: "Value",
                data: [
                    { x: "Debt Ratio", y: rasio, fillColor: getColor(rasio, 20) },
                    { x: "Lifestyle Inflation", y: rasio_inflasi, fillColor: getColor(rasio_inflasi, 0) },
                    { x: "Emergency Fund", y: rasio_dana_darurat, fillColor: getColor(rasio_dana_darurat, 100, true) },
                    { x: "Exp/Inc Ratio", y: rasio_pengeluaran_pendapatan, fillColor: getColor(rasio_pengeluaran_pendapatan, 70) }
                ]
            }],
            xaxis: { labels: { formatter: v => v + '%' } },
            tooltip: { theme: document.documentElement.getAttribute("data-bs-theme") === 'dark' ? 'dark' : 'light' }
        };

        chart = new ApexCharts(chartElement, options);
        chart.render();

        document.addEventListener("themeChanged", () => {
            chart.updateOptions({ chart: { foreColor: getTextColor() } });
        });
    }

    // ==== TOGGLE NOMINAL VISIBILITY AJAX ====
    const toggleBtn = document.getElementById('toggleNominalBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', async () => {
            toggleBtn.disabled = true;
            try {
                const response = await fetch(toggleBtn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const res = await response.json();
                document.getElementById('summary-saldo').textContent = res.data.saldo;
                document.getElementById('summary-pemasukan').textContent = res.data.pemasukan;
                document.getElementById('summary-pengeluaran').textContent = res.data.pengeluaran;
                document.getElementById('summary-hari-ini').textContent = res.data.hari_ini;
                toggleBtn.innerHTML = res.show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            } catch (e) { console.error(e); }
            toggleBtn.disabled = false;
        });
    }
});

// ==== EXPENSES BAR DETAILED CHART (Managed by own listener) ====
document.addEventListener("DOMContentLoaded", function () {
    let barChart = null;
    const isDark = () => document.documentElement.getAttribute("data-bs-theme") === "dark";

    window.fetchDataAndRenderChart = function (month, year) {
        const chartElement = document.querySelector("#barJenisPengeluaran");
        if (!chartElement) return;

        fetch(`/dashboard/jenis-pengeluaran?month=${month}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    if (barChart) barChart.destroy();
                    chartElement.innerHTML = '<div class="text-center p-5 text-muted">No data available</div>';
                    return;
                }

                const labels = data.map(item => item.pengeluaran_nama);
                const values = data.map(item => parseFloat(item.total));

                const options = {
                    chart: { type: 'bar', height: 350, foreColor: isDark() ? '#ddd' : '#333' },
                    series: [{ name: 'Total', data: values }],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                    xaxis: { categories: labels },
                    colors: ['#667eea'],
                    tooltip: { theme: isDark() ? 'dark' : 'light' }
                };

                if (barChart) barChart.destroy();
                barChart = new ApexCharts(chartElement, options);
                barChart.render();
            });
    };

    const filterBulan = document.getElementById("filterBulan");
    const filterTahun = document.getElementById("filterTahun");
    if (filterBulan && filterTahun) {
        window.fetchDataAndRenderChart(filterBulan.value, filterTahun.value);
    }
});
