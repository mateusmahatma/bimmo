function initDashboardCashflow() {
    const el = document.querySelector('#cashflowChart');
    if (!el) return;

    // Helper to format currency
    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    };

    window.renderCashflowChart = function (data) {
        if (!data || !Array.isArray(data)) {
            console.warn("Invalid Cashflow data provided:", data);
            return;
        }

        const labels = data.map(item => item.bulan);
        const pemasukan = data.map(item => Number(item.total_pemasukan));
        const pengeluaran = data.map(item => Number(item.total_pengeluaran));

        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        const options = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                foreColor: isDark ? '#e0e0e0' : '#333',
                fontFamily: 'inherit'
            },
            series: [
                { name: 'Income', data: pemasukan },
                { name: 'Expense', data: pengeluaran }
            ],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 6,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: labels,
                labels: {
                    style: { colors: isDark ? '#e0e0e0' : '#333' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: isDark ? '#e0e0e0' : '#333' },
                    formatter: (val) => val.toLocaleString("id-ID")
                }
            },
            fill: { opacity: 1 },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: (val) => formatCurrency(val)
                }
            },
            colors: ['#198754', '#dc3545'], // Green and Red
            legend: { position: 'top' },
            grid: {
                borderColor: isDark ? '#444' : '#e0e0e0',
                strokeDashArray: 4
            }
        };

        if (window.cashflowChartInstance) {
            window.cashflowChartInstance.destroy();
        }
        window.cashflowChartInstance = new ApexCharts(el, options);
        window.cashflowChartInstance.render();
    };

    // Initial render
    if (window.cashflowData) {
        window.renderCashflowChart(window.cashflowData);
    }

    // Filter Handler
    const filterPeriode = document.getElementById('filterPeriode');
    if (filterPeriode) {
        filterPeriode.addEventListener('change', function () {
            const periode = this.value;
            const label = document.getElementById('cashFlowPeriodeLabel');
            if (label) label.textContent = `(${periode} months ago)`;

            // Show loading state if needed
            const tableContainer = document.getElementById('cashflowTableContainer');
            if (tableContainer) tableContainer.style.opacity = '0.5';

            fetch(`${window.dashboardFilterUrl}?periode=${periode}`)
                .then(res => res.json())
                .then(data => {
                    // Update Table
                    if (tableContainer && data.cashflow) {
                        tableContainer.innerHTML = data.cashflow;
                        tableContainer.style.opacity = '1';
                    }

                    // Update Chart
                    if (data.chartData && data.chartData.cashflow) {
                        window.renderCashflowChart(data.chartData.cashflow);
                    }

                    // Update Net Worth
                    if (data.netWorthHistory && typeof window.updateNetWorthData === 'function') {
                        window.updateNetWorthData(data.netWorthHistory);
                    }
                })
                .catch(err => {
                    console.error("Error fetching filtered cashflow:", err);
                    if (tableContainer) tableContainer.style.opacity = '1';
                });
        });
    }

    // Theme Change Handler
    if (!window.cashflowThemeListenerAdded) {
        document.addEventListener('themeChanged', function () {
            if (window.cashflowChartInstance && window.lastRenderedData) {
                window.renderCashflowChart(window.lastRenderedData);
            } else if (window.cashflowData) {
                window.renderCashflowChart(window.cashflowData);
            }
        });
        window.cashflowThemeListenerAdded = true;
    }
}
document.addEventListener('DOMContentLoaded', initDashboardCashflow);
document.addEventListener('spa:page-loaded', initDashboardCashflow);