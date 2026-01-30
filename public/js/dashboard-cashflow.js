document.addEventListener('DOMContentLoaded', function () {

    const el = document.querySelector('#cashflowChart');
    if (!el || !window.cashflowData) return;

    const labels = window.cashflowData.map(item => item.bulan);
    const pemasukan = window.cashflowData.map(item => Number(item.total_pemasukan));
    const pengeluaran = window.cashflowData.map(item => Number(item.total_pengeluaran));

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

    new ApexCharts(el, options).render();
});