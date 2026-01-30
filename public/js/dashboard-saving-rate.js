document.addEventListener('DOMContentLoaded', function () {

    const el = document.querySelector('#savingRateChart');
    if (!el || !window.savingRateData) return;

    const labels = window.savingRateData.map(item => item.bulan);
    const rates = window.savingRateData.map(item => Number(item.saving_rate));

    const options = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Saving Rate (%)',
            data: rates
        }],
        xaxis: {
            categories: labels
        },
        yaxis: {
            labels: {
                formatter: val => `${val}%`
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 4
        },
        colors: ['#0d6efd'],
        dataLabels: { enabled: false }
    };

    new ApexCharts(el, options).render();
});