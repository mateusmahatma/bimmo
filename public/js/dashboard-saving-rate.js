document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#savingRateChart');
    if (!el) return;

    window.renderSavingRateChart = function (data) {
        if (!data) return;
        const labels = data.map(item => item.bulan);
        const rates = data.map(item => Number(item.saving_rate));

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

        if (window.savingRateChartInstance) {
            window.savingRateChartInstance.destroy();
        }
        window.savingRateChartInstance = new ApexCharts(el, options);
        window.savingRateChartInstance.render();
    }

    if (window.savingRateData) {
        window.renderSavingRateChart(window.savingRateData);
    }
});