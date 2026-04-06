@extends('layouts.main')

@section('container')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-8">
            <h1 class="h3 mb-1 fw-bold text-dark">{{ __('Net Worth Detail') }}</h1>
            <p class="text-muted small mb-0">{{ __('Track your total assets, emergency fund, and debts over the last few months.') }}</p>
        </div>
        <div class="col-4 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">{{ __('Growth Chart') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div id="netWorthLoading" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">{{ __('Calculating your wealth history...') }}</p>
                    </div>
                    <div id="netWorthContent" style="display: none;">
                        <div id="netWorthChart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">{{ __('Detailed Records') }}</h5>
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" id="btnRefreshNetWorth">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 fw-600">{{ __('Month') }}</th>
                                    <th class="text-end fw-600">{{ __('Assets') }}</th>
                                    <th class="text-end fw-600">{{ __('Debt') }}</th>
                                    <th class="text-end pe-4 fw-600">{{ __('Net') }}</th>
                                </tr>
                            </thead>
                            <tbody id="netWorthTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Drilling Detail Modal -->
<div class="modal fade" id="netWorthDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="netWorthDetailTitle">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="netWorthDetailList" style="max-height: 450px; overflow-y: auto;" class="custom-scrollbar">
                    <!-- Details will be injected here -->
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fw-600 { font-weight: 600; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bbb; }
    
    .net-worth-row { cursor: pointer; transition: background 0.2s; }
    .net-worth-row:hover { background-color: rgba(13, 110, 253, 0.05); }

    .detail-item {
        border-radius: 12px;
        transition: all 0.2s;
    }
    .detail-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('livewire:navigated', function() {
    initNetWorthPage();
});

// For first load if not using wire:navigate or if Livewire not loaded
document.addEventListener('DOMContentLoaded', function() {
    if (!window.livewire_navigated_init) {
        initNetWorthPage();
    }
});

function initNetWorthPage() {
    window.livewire_navigated_init = true;
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
                height: 400,
                type: 'line',
                stacked: false,
                toolbar: { show: true },
                fontFamily: 'inherit'
            },
            stroke: {
                width: [4, 0, 0],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 6
                }
            },
            colors: ['#0d6efd', '#198754', '#dc3545'],
            fill: {
                opacity: [1, 0.85, 0.85],
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                    stops: [0, 100]
                }
            },
            labels: months,
            markers: {
                size: [6, 0, 0],
                colors: ['#0d6efd'],
                strokeWidth: 2,
                hover: { size: 8 }
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
                position: 'bottom',
                horizontalAlign: 'center',
                offsetY: 8
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
            tr.className = 'net-worth-row';
            tr.innerHTML = `
                <td class="ps-4"><strong>${item.bulan}</strong></td>
                <td class="text-end">
                    <a href="javascript:void(0)" onclick="showNetWorthDetail(${index}, 'wealth')" class="text-success text-decoration-none fw-semibold">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_aset)}
                        <i class="bi bi-search ms-1 small opacity-50"></i>
                    </a>
                </td>
                <td class="text-end">
                    <a href="javascript:void(0)" onclick="showNetWorthDetail(${index}, 'debt')" class="text-danger text-decoration-none fw-semibold">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_hutang)}
                        <i class="bi bi-search ms-1 small opacity-50"></i>
                    </a>
                </td>
                <td class="text-end pe-4 fw-bold ${item.net_worth >= 0 ? 'text-primary' : 'text-danger'}">Rp ${new Intl.NumberFormat('id-ID').format(item.net_worth)}</td>
            `;
            tbody.appendChild(tr);
        });
    };

    const fetchNetWorthData = () => {
        if (isCalculating) return;
        
        isCalculating = true;
        const btnRefresh = document.getElementById('btnRefreshNetWorth');
        const content = document.getElementById('netWorthContent');
        const loading = document.getElementById('netWorthLoading');

        if (btnRefresh) {
            btnRefresh.disabled = true;
            btnRefresh.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        if (loading) loading.style.display = 'block';
        if (content) content.style.display = 'none';

        const periode = {{ $periode }};

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
                alert('Gagal memproses data. Silakan coba lagi.');
            })
            .finally(() => {
                isCalculating = false;
                if (btnRefresh) {
                    btnRefresh.disabled = false;
                    btnRefresh.innerHTML = '<i class="fas fa-sync-alt"></i>';
                }
            });
    };

    const btnRefresh = document.getElementById('btnRefreshNetWorth');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', fetchNetWorthData);
    }

    // Auto load on init
    fetchNetWorthData();

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
            title = '{{ __("Wealth Details") }} - ' + data.bulan;
            items = [
                ...data.details.assets.map(a => ({ name: a.name, value: a.value, date: a.date, type: '{{ __("Asset") }}', icon: 'bi-box-seam', class: 'text-success' })),
                ...data.details.emergency.map(e => ({ name: e.name, value: e.value, date: e.date, type: '{{ __("Emergency Fund") }}', icon: 'bi-bank', class: 'text-info' })),
                ...data.details.wallets.map(w => ({ name: w.name, value: w.value, date: w.date, type: '{{ __("Wallet") }}', icon: 'bi-wallet2', class: 'text-primary' }))
            ];
        } else {
            title = '{{ __("Debt Details") }} - ' + data.bulan;
            items = data.details.loans.map(l => ({ name: l.name, value: l.value, date: l.date, type: '{{ __("Loan") }}', icon: 'bi-cash-stack', class: 'text-danger' }));
        }

        modalTitle.innerText = title;
        listContainer.innerHTML = '';

        if (items.length === 0) {
            listContainer.innerHTML = '<div class="text-center p-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i> No records found for this month.</div>';
        } else {
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'detail-item d-flex justify-content-between align-items-center p-3 mb-3 border bg-white';
                div.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="icon-box ${item.class} bg-light p-2 rounded-3 me-3">
                            <i class="bi ${item.icon} fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${item.name}</div>
                            <div class="small text-muted">${item.type} • ${item.date}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold ${item.value >= 0 ? 'text-success' : 'text-danger'}">
                            Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(item.value))}
                        </div>
                    </div>
                `;
                listContainer.appendChild(div);
            } );
        }

        modal.show();
    };
}
</script>
@endpush
@endsection
