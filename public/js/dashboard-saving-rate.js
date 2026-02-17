document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#savingRateChart');
    if (!el) return;

    window.renderSavingRateChart = function (data) {
        if (!data) return;
        const labels = data.map(item => item.bulan);
        const rates = data.map(item => Number(item.saving_rate));

        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        const options = {
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                foreColor: isDark ? '#e0e0e0' : '#333'
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light'
            },
            series: [{
                name: 'Saving Rate (%)',
                data: rates
            }],
            xaxis: {
                categories: labels,
                labels: {
                    style: { colors: isDark ? '#e0e0e0' : '#333' }
                }
            },
            yaxis: {
                labels: {
                    formatter: val => `${val}%`,
                    style: { colors: isDark ? '#e0e0e0' : '#333' }
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
            dataLabels: { enabled: false },
            grid: {
                borderColor: isDark ? '#444' : '#e0e0e0'
            }
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