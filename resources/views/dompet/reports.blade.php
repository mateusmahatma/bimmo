@extends('layouts.main')

@section('title', __('Wallet Reports'))

@push('css')
<style>
    /* Header Enhancements */
    .pagetitle {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.75rem;
    }
    .pagetitle h1 {
        font-size: 1.75rem;
        letter-spacing: -0.03em;
        color: #2d3436;
    }
    .breadcrumb {
        font-size: 0.85rem;
    }
    .breadcrumb-item a {
        color: #636e72;
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-item a:hover {
        color: #0984e3;
    }
    .breadcrumb-item.active {
        color: #0984e3;
        font-weight: 600;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "\F285"; /* bi-chevron-right */
        font-family: "bootstrap-icons";
        font-size: 0.65rem;
        color: #b2bec3;
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }

    [data-bs-theme="dark"] .pagetitle {
        border-bottom: 1px solid #2d2d2d;
    }
    [data-bs-theme="dark"] .pagetitle h1 {
        color: #e0e0e0;
    }
    [data-bs-theme="dark"] .breadcrumb-item a {
        color: #a0a0a0;
    }
    [data-bs-theme="dark"] .breadcrumb-item.active {
        color: #60a5fa;
    }
</style>
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Financial Reports') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dompet.index') }}">{{ __('Wallet') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Reports') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row g-4">
        <!-- Summary Cards -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">{{ __('Balance Distribution') }}</h5>
                    <div id="walletDistributionChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">{{ __('Income vs Expense') }}</h5>
                    <div id="incomeVsExpenseChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wallet Distribution Chart
        var walletData = @json($wallets->map(fn($w) => ['name' => $w->nama, 'balance' => (float)$w->saldo]));
        
        var optionsDistribution = {
            series: walletData.map(w => w.balance),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: walletData.map(w => w.name),
            colors: ['#4154f1', '#2eca6a', '#ff771d', '#198754', '#dc3545', '#0dcaf0'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chartDistribution = new ApexCharts(document.querySelector("#walletDistributionChart"), optionsDistribution);
        chartDistribution.render();

        // Income vs Expense Chart (Placeholder or real data if available)
        var optionsComparison = {
            series: [{
                name: 'Income',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125] // Example data
            }, {
                name: 'Expense',
                data: [20, 30, 25, 40, 39, 50, 60, 81, 105] // Example data
            }],
            chart: {
                type: 'area',
                height: 300,
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            colors: ['#2eca6a', '#dc3545'],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
            }
        };

        var chartComparison = new ApexCharts(document.querySelector("#incomeVsExpenseChart"), optionsComparison);
        chartComparison.render();
    });
</script>
@endpush
@endsection
