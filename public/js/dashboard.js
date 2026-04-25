function initDashboardGeneral() {
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

        if (!window.kompasThemeListenerAdded) {
            document.addEventListener("themeChanged", () => {
                if (chart) chart.updateOptions({ chart: { foreColor: getTextColor() } });
            });
            window.kompasThemeListenerAdded = true;
        }
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
                document.getElementById('summary-cicilan-besok').textContent = res.data.cicilan_besok;
                document.getElementById('summary-hari-ini').textContent = res.data.hari_ini;

                const goalsCollected = document.getElementById('goals-total-collected');
                const goalsTarget = document.getElementById('goals-total-target');
                if (goalsCollected && res.data.financial_goals_collected) goalsCollected.textContent = res.data.financial_goals_collected;
                if (goalsTarget && res.data.financial_goals_target) goalsTarget.textContent = res.data.financial_goals_target;

                toggleBtn.innerHTML = res.show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            } catch (e) { console.error(e); }
            toggleBtn.disabled = false;
        });
    }

}
document.addEventListener('DOMContentLoaded', initDashboardGeneral);
document.addEventListener('livewire:navigated', initDashboardGeneral);

// ==== EXPENSES BAR DETAILED CHART (Managed by own listener) ====
function initDashboardExpensesBar() {
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
}
document.addEventListener('DOMContentLoaded', initDashboardExpensesBar);
document.addEventListener('livewire:navigated', initDashboardExpensesBar);

// ==== GROWTH DETAIL MODAL HANDLER ====
function initDashboardGrowthDetail() {
    const growthTriggers = document.querySelectorAll('.growth-detail-trigger');
    growthTriggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
            const title = this.dataset.title;
            const current = parseFloat(this.dataset.current);
            const last = parseFloat(this.dataset.last);
            const percent = parseFloat(this.dataset.percent);
            const type = this.dataset.type;

            document.getElementById('growthDetailTitle').textContent = title;
            document.getElementById('growthPercentValue').textContent = (percent > 0 ? '+' : '') + percent + '%';
            document.getElementById('currentPeriodValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(current);
            document.getElementById('lastPeriodValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(last);
            document.getElementById('growthDiffValue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(current - last);

            const badge = document.getElementById('growthPercentBadge');
            const icon = document.getElementById('growthIcon');
            const alert = document.getElementById('growthSummaryAlert');
            const summaryIcon = document.getElementById('growthSummaryIcon');
            const summaryText = document.getElementById('growthSummaryText');

            // Reset classes
            badge.className = 'd-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 fw-bold fs-5 shadow-sm';
            alert.className = 'alert border-0 py-3 mb-0 rounded-4 d-flex align-items-start gap-3';

            let isPositive = percent > 0;
            // For expenses, positive growth is "bad" (red/danger), negative is "good" (green/success)
            let isGood = type === 'expense' ? !isPositive : isPositive;

            if (percent === 0) {
                badge.classList.add('bg-secondary-light');
                icon.className = 'bi bi-dash';
                alert.classList.add('alert-secondary');
                summaryIcon.className = 'bi bi-info-circle-fill text-secondary';
                summaryText.textContent = 'Tidak ada perubahan signifikan dibandingkan periode yang sama bulan lalu.';
            } else if (isGood) {
                badge.classList.add('bg-success-light');
                icon.className = isPositive ? 'bi bi-arrow-up-circle-fill' : 'bi bi-arrow-down-circle-fill';
                alert.classList.add('alert-success');
                summaryIcon.className = 'bi bi-check-circle-fill text-success';
                summaryText.textContent = type === 'expense'
                    ? 'Bagus! Pengeluaran Anda menurun dibandingkan periode yang sama bulan lalu.'
                    : 'Luar biasa! Pendapatan Anda meningkat dibandingkan periode yang sama bulan lalu.';
            } else {
                badge.classList.add('bg-danger-light');
                icon.className = isPositive ? 'bi bi-arrow-up-circle-fill' : 'bi bi-arrow-down-circle-fill';
                alert.classList.add('alert-danger');
                summaryIcon.className = 'bi bi-exclamation-triangle-fill text-danger';
                summaryText.textContent = type === 'expense'
                    ? 'Waspada! Pengeluaran Anda meningkat dibandingkan periode yang sama bulan lalu.'
                    : 'Perhatian! Pendapatan Anda menurun dibandingkan periode yang sama bulan lalu.';
            }

            const modal = new bootstrap.Modal(document.getElementById('growthDetailModal'));
            modal.show();
        });
    });
}
document.addEventListener('DOMContentLoaded', initDashboardGrowthDetail);
document.addEventListener('livewire:navigated', initDashboardGrowthDetail);
