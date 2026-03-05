@extends('layouts.main')

@section('title', 'Laporan Dompet')

@section('container')
<div class="pagetitle mb-4">
    <h1>Laporan Keuangan</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dompet.index') }}">Dompet</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row g-4">
        <!-- Summary Cards -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Distribusi Saldo</h5>
                    <div id="walletDistributionChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Pemasukan vs Pengeluaran</h5>
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
                name: 'Pemasukan',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125] // Example data
            }, {
                name: 'Pengeluaran',
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
