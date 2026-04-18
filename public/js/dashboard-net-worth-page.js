(() => {
    const pageConfig = window.netWorthPageConfig;

    if (!pageConfig) {
        return;
    }

    const currencyFormatter = new Intl.NumberFormat('id-ID');

    document.addEventListener('livewire:navigated', initNetWorthPage);
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.livewire_navigated_init) {
            initNetWorthPage();
        }
    });

    function initNetWorthPage() {
        const chartElement = document.getElementById('netWorthChart');
        const syncButton = document.getElementById('btnSyncNetWorth');

        if (!chartElement || !syncButton) {
            return;
        }

        window.livewire_navigated_init = true;

        let chartInstance = null;
        let isLoading = false;

        syncButton.onclick = fetchNetWorthData;
        fetchNetWorthData();

        window.showNetWorthDetail = showNetWorthDetail;

        function fetchNetWorthData() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            const loadingElement = document.getElementById('netWorthLoading');
            const contentElement = document.getElementById('netWorthContent');

            syncButton.disabled = true;
            syncButton.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> ${pageConfig.labels.syncing}`;

            loadingElement?.classList.remove('d-none');
            contentElement?.classList.add('d-none');

            fetch(`${pageConfig.historyUrl}?periode=${pageConfig.periode}`)
                .then(response => response.json())
                .then(data => {
                    window.netWorthData = data;
                    loadingElement?.classList.add('d-none');
                    contentElement?.classList.remove('d-none');

                    renderChart(data);
                    renderTable(data);
                })
                .catch(error => {
                    console.error('Error fetching net worth data:', error);
                    loadingElement?.classList.add('d-none');
                    alert(pageConfig.labels.fetchError);
                })
                .finally(() => {
                    isLoading = false;
                    syncButton.disabled = false;
                    syncButton.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> ${pageConfig.labels.sync}`;
                });
        }

        function renderChart(data) {
            if (!Array.isArray(data) || data.length === 0) {
                return;
            }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const labelColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f1f1';

            const options = {
                series: [
                    { name: pageConfig.labels.netWorth, type: 'line', data: data.map(item => item.net_worth) },
                    { name: pageConfig.labels.wealth, type: 'column', data: data.map(item => item.total_aset) },
                    { name: pageConfig.labels.debt, type: 'column', data: data.map(item => item.total_hutang) },
                ],
                chart: {
                    height: 380,
                    type: 'line',
                    stacked: false,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                stroke: {
                    width: [4, 0, 0],
                    curve: 'smooth',
                },
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 0,
                    },
                },
                colors: ['#4361ee', '#4cc9f0', '#f72585'],
                fill: {
                    opacity: [1, 0.85, 0.85],
                    gradient: {
                        shade: isDark ? 'dark' : 'light',
                        type: 'vertical',
                        opacityFrom: 0.85,
                        opacityTo: 0.55,
                        stops: [0, 100],
                    },
                },
                labels: data.map(item => item.bulan),
                markers: {
                    size: [5, 0, 0],
                    colors: ['#4361ee'],
                    strokeWidth: 2,
                    hover: { size: 7 },
                },
                yaxis: {
                    labels: {
                        formatter: value => `Rp ${currencyFormatter.format(value)}`,
                        style: { colors: labelColor },
                    },
                },
                xaxis: {
                    type: 'category',
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: labelColor } },
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: value => (typeof value !== 'undefined' ? `Rp ${currencyFormatter.format(value)}` : value),
                    },
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    offsetY: 0,
                    labels: { colors: labelColor },
                },
                grid: {
                    borderColor: gridColor,
                    padding: { bottom: 10 },
                },
            };

            chartInstance?.destroy();
            chartInstance = new ApexCharts(chartElement, options);
            chartInstance.render();
        }

        function renderTable(data) {
            const tbody = document.getElementById('netWorthTableBody');

            if (!tbody) {
                return;
            }

            tbody.innerHTML = '';

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'net-worth-row';
                row.innerHTML = `
                    <td class="ps-4"><span class="fw-bold text-dark">${item.bulan}</span></td>
                    <td class="text-end">
                        <button onclick="showNetWorthDetail(${index}, 'wealth')" class="btn btn-link text-success text-decoration-none fw-semibold p-0" style="font-size: 0.85rem;">
                            Rp ${currencyFormatter.format(item.total_aset)}
                            <i class="bi bi-search ms-1 small opacity-50"></i>
                        </button>
                    </td>
                    <td class="text-end">
                        <button onclick="showNetWorthDetail(${index}, 'debt')" class="btn btn-link text-danger text-decoration-none fw-semibold p-0" style="font-size: 0.85rem;">
                            Rp ${currencyFormatter.format(item.total_hutang)}
                            <i class="bi bi-search ms-1 small opacity-50"></i>
                        </button>
                    </td>
                    <td class="text-end pe-4 fw-bold ${item.net_worth >= 0 ? 'text-primary' : 'text-danger'}" style="font-size: 0.85rem;">
                        Rp ${currencyFormatter.format(item.net_worth)}
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showNetWorthDetail(monthIndex, type) {
            const data = window.netWorthData?.[monthIndex];

            if (!data) {
                return;
            }

            const modalTitle = document.getElementById('netWorthDetailTitle');
            const listContainer = document.getElementById('netWorthDetailList');
            const modalElement = document.getElementById('netWorthDetailModal');
            const modal = new bootstrap.Modal(modalElement);

            const items = type === 'wealth'
                ? [
                    ...data.details.assets.map(item => ({ ...item, type: pageConfig.labels.asset, icon: 'bi-box-seam', className: 'text-success' })),
                    ...data.details.emergency.map(item => ({ ...item, type: pageConfig.labels.emergencyFund, icon: 'bi-bank', className: 'text-info' })),
                    ...data.details.wallets.map(item => ({ ...item, type: pageConfig.labels.wallet, icon: 'bi-wallet2', className: 'text-primary' })),
                ]
                : data.details.loans.map(item => ({ ...item, type: pageConfig.labels.loan, icon: 'bi-cash-stack', className: 'text-danger' }));

            modalTitle.innerText = `${type === 'wealth' ? pageConfig.labels.wealthDetails : pageConfig.labels.debtDetails} - ${data.bulan}`;
            listContainer.innerHTML = '';

            if (items.length === 0) {
                listContainer.innerHTML = `<div class="text-center p-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i> ${pageConfig.labels.noRecords}</div>`;
                modal.show();
                return;
            }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const table = document.createElement('table');

            table.className = 'table table-hover align-middle mb-0';
            table.innerHTML = `
                <thead class="${isDark ? 'table-header-strip' : 'table-light'}">
                    <tr>
                        <th class="ps-4">${pageConfig.labels.name}</th>
                        <th>${pageConfig.labels.category}</th>
                        <th class="text-end pe-4">${pageConfig.labels.amount}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;

            const tbody = table.querySelector('tbody');

            items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="${isDark ? 'bg-dark' : 'bg-light'} p-2 rounded-0 me-3 text-center ${item.className}" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi ${item.icon} fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark">${item.name}</div>
                        </div>
                    </td>
                    <td><span class="badge ${isDark ? 'bg-dark text-light' : 'bg-light text-dark'} fw-normal border rounded-0">${item.type}</span></td>
                    <td class="text-end pe-4 fw-bold">
                        <span class="${type === 'wealth' ? 'text-success' : 'text-danger'}">
                            Rp ${currencyFormatter.format(Math.abs(item.value))}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });

            listContainer.appendChild(table);
            modal.show();
        }
    }
})();
