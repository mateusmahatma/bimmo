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
                    renderGrowthPercent(data);
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

            const netWorthSeries = data.map(item => Number(item.net_worth ?? 0));
            const netWorthPercents = netWorthSeries.map((value, index) => {
                if (index === 0) {
                    return null;
                }

                const prev = Number(netWorthSeries[index - 1] ?? 0);
                if (prev === 0) {
                    return value === 0 ? 0 : 100;
                }

                return ((value - prev) / Math.abs(prev)) * 100;
            });

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const labelColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f1f1';
            const positiveColor = '#16a34a';
            const negativeColor = '#dc2626';
            const neutralColor = labelColor;
            const annotationTextColor = isDark ? '#0b1220' : '#ffffff';

            const percentAnnotations = netWorthPercents
                .map((pct, index) => {
                    if (pct === null || typeof pct === 'undefined') {
                        return null;
                    }

                    const rounded = Math.round(pct * 10) / 10;
                    const sign = rounded > 0 ? '+' : '';
                    const color = rounded > 0 ? positiveColor : (rounded < 0 ? negativeColor : neutralColor);

                    return {
                        x: data[index]?.bulan,
                        y: netWorthSeries[index],
                        seriesIndex: 0,
                        marker: {
                            size: 0,
                        },
                        label: {
                            text: `${sign}${rounded}%`,
                            offsetY: -12,
                            borderColor: color,
                            style: {
                                background: color,
                                color: annotationTextColor,
                                fontSize: '11px',
                                fontWeight: 700,
                                padding: {
                                    left: 6,
                                    right: 6,
                                    top: 3,
                                    bottom: 3,
                                },
                            },
                        },
                    };
                })
                .filter(Boolean);

            const options = {
                series: [
                    { name: pageConfig.labels.netWorth, type: 'line', data: netWorthSeries },
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
                dataLabels: { enabled: false },
                annotations: {
                    points: percentAnnotations,
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

        function renderGrowthPercent(data) {
            const badge = document.getElementById('netWorthGrowthPercent');

            if (!badge || !Array.isArray(data) || data.length < 2) {
                badge?.classList.add('d-none');
                return;
            }

            const last = Number(data[data.length - 1]?.net_worth ?? 0);
            const prev = Number(data[data.length - 2]?.net_worth ?? 0);

            const percent = prev === 0
                ? (last === 0 ? 0 : 100)
                : ((last - prev) / Math.abs(prev)) * 100;

            const rounded = Math.round(percent * 10) / 10;
            const sign = rounded > 0 ? '+' : '';

            badge.textContent = `${sign}${rounded}%`;
            badge.classList.remove('d-none', 'bg-success', 'bg-danger', 'bg-body-secondary', 'text-white', 'text-dark');

            if (rounded > 0) {
                badge.classList.add('bg-success', 'text-white');
            } else if (rounded < 0) {
                badge.classList.add('bg-danger', 'text-white');
            } else {
                badge.classList.add('bg-body-secondary', 'text-dark');
            }
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
                    <td class="ps-4"><span class="fw-bold text-body">${item.bulan}</span></td>
                    <td class="text-end">
                        <button onclick="showNetWorthDetail(${index}, 'wealth')" class="btn btn-link text-success text-decoration-none fw-semibold p-0 net-worth-link" type="button">
                            <span class="net-worth-amount">Rp ${currencyFormatter.format(item.total_aset)}</span>
                            <i class="bi bi-search ms-1 small opacity-50"></i>
                        </button>
                    </td>
                    <td class="text-end">
                        <button onclick="showNetWorthDetail(${index}, 'debt')" class="btn btn-link text-danger text-decoration-none fw-semibold p-0 net-worth-link" type="button">
                            <span class="net-worth-amount">Rp ${currencyFormatter.format(item.total_hutang)}</span>
                            <i class="bi bi-search ms-1 small opacity-50"></i>
                        </button>
                    </td>
                    <td class="text-end pe-4 fw-bold ${item.net_worth >= 0 ? 'text-primary' : 'text-danger'}">
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
                            <div class="fw-bold text-body">${item.name}</div>
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
