@extends('layouts.main')

@section('title', __('Net Worth Detail'))

@section('container')
<div class="pagetitle mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">{{ __('Net Worth Detail') }}</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Net Worth') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-0 px-4 shadow-sm border" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Main Chart Card -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm overflow-hidden rounded-0">
                <div class="card-header border-0 pt-4 px-4 d-flex justify-content-between align-items-center" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-0">{{ __('Net Worth Growth') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Historical trend of your assets and liabilities') }}</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-0" type="button" id="btnRefreshNetWorth" title="{{ __('Refresh Data') }}">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="netWorthLoading" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-medium">{{ __('Calculating your wealth history...') }}</p>
                    </div>
                    <div id="netWorthContent" style="display: none;">
                        <div id="netWorthChart" style="min-height: 380px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Summary Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm overflow-hidden rounded-0">
                <div class="card-header border-0 pt-4 px-4" style="background: transparent;">
                    <h5 class="fw-bold mb-0">{{ __('Monthly Data') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Click on values to see details') }}</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 net-worth-table">
                            <thead class="table-header-strip">
                                <tr>
                                    <th class="ps-4 fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Month') }}</th>
                                    <th class="text-end fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Assets') }}</th>
                                    <th class="text-end fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Debt') }}</th>
                                    <th class="text-end pe-4 fw-600 py-3 border-0 text-uppercase text-secondary">{{ __('Net') }}</th>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-0 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-body-tertiary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-0 me-3">
                        <i class="bi bi-journal-text fs-4 text-primary"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="netWorthDetailTitle">Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="netWorthDetailList" style="max-height: 500px; overflow-y: auto;" class="custom-scrollbar">
                    <!-- Details will be injected here as a table -->
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-body-tertiary">
                <button type="button" class="btn btn-secondary rounded-0 px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fw-600 { font-weight: 600; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 0; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 0; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bbb; }
    
    .net-worth-row { cursor: pointer; transition: background 0.2s; }
    .net-worth-row:hover { background-color: rgba(13, 110, 253, 0.03); }

    [data-bs-theme="dark"] .net-worth-row:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .table > :not(caption) > * > * {
        padding: 0.75rem 0.4rem;
    }
    
    .net-worth-table { font-size: 0.85rem; }
    .net-worth-table th { font-size: 0.75rem; letter-spacing: 0.5px; }
    
    .modal-lg { max-width: 800px; }

    /* Dark mode fixes for modal tertiary background */
    [data-bs-theme="dark"] .bg-body-tertiary {
        background-color: #2c2c2c !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('livewire:navigated', function() {
    initNetWorthPage();
});

// For first load
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

        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const labelColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f1f1';

        const months = data.map(item => item.bulan);
        const wealthData = data.map(item => item.total_aset);
        const debtData = data.map(item => item.total_hutang);
        const netWorthTrend = data.map(item => item.net_worth);

        const options = {
            series: [
                {
                    name: '{{ __("Net Worth") }}',
                    type: 'line',
                    data: netWorthTrend
                },
                {
                    name: '{{ __("Wealth") }}',
                    type: 'column',
                    data: wealthData
                },
                {
                    name: '{{ __("Debt") }}',
                    type: 'column',
                    data: debtData
                }
            ],
            chart: {
                height: 380,
                type: 'line',
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            stroke: {
                width: [4, 0, 0],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 0
                }
            },
            colors: ['#4361ee', '#4cc9f0', '#f72585'],
            fill: {
                opacity: [1, 0.85, 0.85],
                gradient: {
                    shade: isDark ? 'dark' : 'light',
                    type: "vertical",
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                    stops: [0, 100]
                }
            },
            labels: months,
            markers: {
                size: [5, 0, 0],
                colors: ['#4361ee'],
                strokeWidth: 2,
                hover: { size: 7 }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                    },
                    style: { colors: labelColor }
                }
            },
            xaxis: {
                type: 'category',
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: labelColor } }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
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
                horizontalAlign: 'right',
                offsetY: 0,
                labels: {
                    colors: labelColor
                }
            },
            grid: {
                borderColor: gridColor,
                padding: { bottom: 10 }
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
                <td class="ps-4"><span class="fw-bold text-dark">${item.bulan}</span></td>
                <td class="text-end">
                    <button onclick="showNetWorthDetail(${index}, 'wealth')" class="btn btn-link text-success text-decoration-none fw-semibold p-0" style="font-size: 0.85rem;">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_aset)}
                        <i class="bi bi-search ms-1 small opacity-50"></i>
                    </button>
                </td>
                <td class="text-end">
                    <button onclick="showNetWorthDetail(${index}, 'debt')" class="btn btn-link text-danger text-decoration-none fw-semibold p-0" style="font-size: 0.85rem;">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.total_hutang)}
                        <i class="bi bi-search ms-1 small opacity-50"></i>
                    </button>
                </td>
                <td class="text-end pe-4 fw-bold ${item.net_worth >= 0 ? 'text-primary' : 'text-danger'}" style="font-size: 0.85rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.net_worth)}</td>
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
            listContainer.innerHTML = `<div class="text-center p-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i> {{ __('No records found for this month.') }}</div>`;
        } else {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const table = document.createElement('table');
            table.className = 'table table-hover align-middle mb-0';
            table.innerHTML = `
                <thead class="${isDark ? 'table-header-strip' : 'table-light'}">
                    <tr>
                        <th class="ps-4">{{ __('Name') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th class="text-end pe-4">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            `;
            const tbody = table.querySelector('tbody');
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="${isDark ? 'bg-dark' : 'bg-light'} p-2 rounded-0 me-3 text-center ${item.class}" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi ${item.icon} fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark">${item.name}</div>
                        </div>
                    </td>
                    <td><span class="badge ${isDark ? 'bg-dark text-light' : 'bg-light text-dark'} fw-normal border rounded-0">${item.type}</span></td>
                    <td class="text-end pe-4 fw-bold">
                        <span class="${type === 'wealth' ? 'text-success' : 'text-danger'}">
                        Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(item.value))}
                        </span>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            listContainer.appendChild(table);
        }

        modal.show();
    };
}
</script>
@endpush
@endsection
