<!-- Modal Net Worth Tracking -->
<div class="modal fade" id="netWorthModal" tabindex="-1" aria-labelledby="netWorthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="netWorthModalLabel" style="font-size: 1.25rem;">{{ __('Net Worth Tracker') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Track your total assets, emergency fund, and debts over the last few months.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="text-center mb-4" id="netWorthGenerateContainer">
                    <p class="text-muted">{{ __('Calculations may take a few seconds as we process your historical data.') }}</p>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" id="btnGenerateNetWorth">
                        <i class="fas fa-sync-alt me-2"></i> {{ __('Generate History') }}
                    </button>
                </div>

                <div id="netWorthContent" style="display: none;">
                    <div id="netWorthChartContainer" style="min-height: 350px;">
                        <!-- ApexCharts will inject here -->
                        <div id="netWorthChart"></div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Month') }}</th>
                                    <th class="text-end">{{ __('Wealth (Assets + Emergency)') }}</th>
                                    <th class="text-end">{{ __('Debt') }}</th>
                                    <th class="text-end">{{ __('Net Worth') }}</th>
                                </tr>
                            </thead>
                            <tbody id="netWorthTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="netWorthLoading" class="text-center p-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">{{ __('Calculating your wealth history...') }}</p>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4" style="background-color: transparent;">
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let nwChart = null;
    let isCalculating = false;

    const renderNetWorthChart = (data) => {
        if (!data || data.length === 0) return;

        const months = data.map(item => item.bulan);
        const wealthData = data.map(item => item.total_aset);
        const debtData = data.map(item => item.total_hutang);
        const netWorthTrend = data.map(item => item.net_worth);

        const options = {
            series: [
                {
                    name: 'Net Worth',
                    type: 'line',
                    data: netWorthTrend
                },
                {
                    name: 'Wealth (Assets + Emergency Fund)',
                    type: 'column',
                    data: wealthData
                },
                {
                    name: 'Debt',
                    type: 'column',
                    data: debtData
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            stroke: {
                width: [4, 0, 0],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4
                }
            },
            colors: ['#0d6efd', '#198754', '#dc3545'],
            fill: {
                opacity: [1, 0.85, 0.85],
                gradient: {
                    inverseColors: false,
                    shade: 'light',
                    type: "vertical",
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                    stops: [0, 100, 100, 100]
                }
            },
            labels: months,
            markers: {
                size: [6, 0, 0],
                colors: ['#0d6efd']
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            xaxis: {
                type: 'category'
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y) {
                        if(typeof y !== "undefined") {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(y);
                        }
                        return y;
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            }
        };

        if (nwChart) {
            nwChart.destroy();
        }

        const chartElement = document.querySelector("#netWorthChart");
        if (chartElement) {
            nwChart = new ApexCharts(chartElement, options);
            nwChart.render();
        }
    };

    const renderNetWorthTable = (data) => {
        const tbody = document.getElementById('netWorthTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        data.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${item.bulan}</strong></td>
                <td class="text-end">
                    <a href="javascript:void(0)" onclick="showNetWorthDetail(${index}, 'wealth')" class="text-success text-decoration-none fw-semibold">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_aset)}
                        <i class="fas fa-search-plus ms-1 small opacity-50"></i>
                    </a>
                </td>
                <td class="text-end">
                    <a href="javascript:void(0)" onclick="showNetWorthDetail(${index}, 'debt')" class="text-danger text-decoration-none fw-semibold">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_hutang)}
                        <i class="fas fa-search-plus ms-1 small opacity-50"></i>
                    </a>
                </td>
                <td class="text-end fw-bold ${item.net_worth >= 0 ? 'text-primary' : 'text-danger'}">Rp ${new Intl.NumberFormat('id-ID').format(item.net_worth)}</td>
            `;
            tbody.appendChild(tr);
        });
    };

    const fetchNetWorthData = () => {
        if (isCalculating) return;
        
        isCalculating = true;
        const btnGenerate = document.getElementById('btnGenerateNetWorth');
        const content = document.getElementById('netWorthContent');
        const loading = document.getElementById('netWorthLoading');
        const generateContainer = document.getElementById('netWorthGenerateContainer');

        if (btnGenerate) btnGenerate.disabled = true;
        if (loading) loading.style.display = 'block';
        if (generateContainer) generateContainer.style.display = 'none';
        if (content) content.style.display = 'none';

        // Get current period from URL or dashboard filter
        const params = new URLSearchParams(window.location.search);
        const periode = params.get('periode') || 6;

        fetch(`{{ route('dashboard.net-worth-history') }}?periode=${periode}`)
            .then(response => response.json())
            .then(data => {
                window.netWorthData = data;
                if (loading) loading.style.display = 'none';
                if (content) content.style.display = 'block';
                
                renderNetWorthChart(data);
                renderNetWorthTable(data);
            })
            .catch(error => {
                console.error('Error fetching net worth data:', error);
                if (loading) loading.style.display = 'none';
                if (generateContainer) generateContainer.style.display = 'block';
                alert('Gagal memproses data. Silakan coba lagi.');
            })
            .finally(() => {
                isCalculating = false;
                if (btnGenerate) btnGenerate.disabled = false;
            });
    };

    const btnGenerate = document.getElementById('btnGenerateNetWorth');
    if (btnGenerate) {
        btnGenerate.addEventListener('click', fetchNetWorthData);
    }

    const netWorthModal = document.getElementById('netWorthModal');
    if (netWorthModal) {
        netWorthModal.addEventListener('shown.bs.modal', function () {
            // If data already exists (from previous generation), show it
            if (window.netWorthData && window.netWorthData.length > 0) {
                document.getElementById('netWorthContent').style.display = 'block';
                document.getElementById('netWorthGenerateContainer').style.display = 'none';
                renderNetWorthChart(window.netWorthData);
                renderNetWorthTable(window.netWorthData);
            }
        });
    }

    // Handle updates when dashboard period changes
    window.updateNetWorthData = function(data) {
        // We reset the data so they have to "Generate" again if they want the updated chart
        window.netWorthData = null;
        if (netWorthModal && !netWorthModal.classList.contains('show')) {
            const content = document.getElementById('netWorthContent');
            const genContainer = document.getElementById('netWorthGenerateContainer');
            if (content) content.style.display = 'none';
            if (genContainer) genContainer.style.display = 'block';
        }
    };

    // --- DRILL DOWN LOGIC ---
    window.showNetWorthDetail = function(monthIndex, type) {
        const data = window.netWorthData[monthIndex];
        if (!data) return;

        const modalTitle = document.getElementById('netWorthDetailTitle');
        const listContainer = document.getElementById('netWorthDetailList');
        const modalElement = document.getElementById('netWorthDetailModal');
        const modal = new bootstrap.Modal(modalElement);

        let items = [];
        let title = '';

        if (type === 'wealth') {
            title = `Wealth Details - ${data.bulan}`;
            // Combine assets and emergency fund
            items = [
                ...data.details.assets.map(a => ({ name: a.name, value: a.value, date: a.date, type: 'Asset' })),
                ...data.details.emergency.map(e => ({ name: e.name, value: e.value, date: e.date, type: 'Emergency Fund' }))
            ];
        } else {
            title = `Debt Details - ${data.bulan}`;
            items = data.details.loans.map(l => ({ name: l.name, value: l.value, date: l.date, type: 'Loan' }));
        }

        modalTitle.innerText = title;
        listContainer.innerHTML = '';

        if (items.length === 0) {
            listContainer.innerHTML = '<div class="text-center p-3 text-muted">No records found for this month.</div>';
        } else {
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'd-flex justify-content-between align-items-center p-3 mb-2 border rounded shadow-sm bg-white';
                div.innerHTML = `
                    <div>
                        <div class="fw-bold text-dark">${item.name}</div>
                        <div class="small text-muted">${item.type} • ${item.date}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold ${item.value >= 0 ? 'text-success' : 'text-danger'}">
                            Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(item.value))}
                            ${item.value < 0 ? '<small>(Withdrawal)</small>' : ''}
                        </div>
                    </div>
                `;
                listContainer.appendChild(div);
            } );
        }

        modal.show();
    };
});
</script>

<!-- Detail Modal -->
<div class="modal fade" id="netWorthDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="netWorthDetailTitle">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div id="netWorthDetailList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Details will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
