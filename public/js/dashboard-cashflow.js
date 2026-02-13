document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#cashflowChart');
    if (!el) return;

    window.renderCashflowChart = function (data) {
        if (!data) return;
        const labels = data.map(item => item.bulan);
        const pemasukan = data.map(item => Number(item.total_pemasukan));
        const pengeluaran = data.map(item => Number(item.total_pengeluaran));

        const options = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            series: [
                { name: 'Pendapatan', data: pemasukan },
                { name: 'Pengeluaran', data: pengeluaran }
            ],
            xaxis: {
                categories: labels
            },
            colors: ['#198754', '#dc3545'],
            dataLabels: { enabled: false },
            legend: { position: 'top' }
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