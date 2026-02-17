document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#cashflowChart');
    if (!el) return;

    window.renderCashflowChart = function (data) {
        if (!data) return;
        const labels = data.map(item => item.bulan);
        const pemasukan = data.map(item => Number(item.total_pemasukan));
        const pengeluaran = data.map(item => Number(item.total_pengeluaran));

        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        const options = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                foreColor: isDark ? '#e0e0e0' : '#333'
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light'
            },
            series: [
                { name: 'Pendapatan', data: pemasukan },
                { name: 'Pengeluaran', data: pengeluaran }
            ],
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
            colors: ['#198754', '#dc3545'],
            dataLabels: { enabled: false },
            legend: { position: 'top' },
            grid: {
                borderColor: isDark ? '#444' : '#e0e0e0'
            }
        };

        if (window.cashflowChartInstance) {
            window.cashflowChartInstance.destroy();
        }
        window.cashflowChartInstance = new ApexCharts(el, options);
        window.cashflowChartInstance.render();
    }

    if (window.cashflowData) {
        window.renderCashflowChart(window.cashflowData);
    }
});