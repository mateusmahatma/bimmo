@extends('layouts.main')

@section('title', 'Asset Analysis & Report')

@section('container')
<div class="pagetitle mb-4">
    <h1>Asset Analysis & Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Analysis</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Stats Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm info-card sales-card" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title">Total Assets <span class="text-muted small">| Active</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light">
                            <i class="bi bi-box-seam text-primary"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ $total_aset }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm info-card revenue-card" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title">Total Purchase Value</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                            <i class="bi bi-cart-check text-success"></i>
                        </div>
                        <div class="ps-3">
                            <h6 style="font-size: 1.25rem;">Rp {{ number_format($total_nilai_beli, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm info-card customers-card" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title">Total Book Value <span class="text-muted small">| Current</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-light">
                            <i class="bi bi-graph-down text-info"></i>
                        </div>
                        <div class="ps-3">
                            <h6 style="font-size: 1.25rem;">Rp {{ number_format($total_nilai_buku, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Asset Condition Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Asset Condition</h5>
                </div>
                <div class="card-body">
                    <canvas id="conditionChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Asset Value Distribution (Category) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Recent Maintenance Costs</h5>
                    <span class="text-muted small">Top 5 Records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3">Asset</th>
                                    <th>Activity</th>
                                    <th class="text-end px-3">Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentLogs = \App\Models\AsetMaintenance::with('aset')
                                        ->whereHas('aset', function($q) { $q->where('id_user', Auth::id()); })
                                        ->orderBy('tanggal', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($recentLogs as $log)
                                <tr>
                                    <td class="px-3">{{ $log->aset->nama_aset }}</td>
                                    <td>{{ $log->kegiatan }}</td>
                                    <td class="text-end px-3 fw-bold text-danger">Rp {{ number_format($log->biaya, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No maintenance records yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const kondisiData = @json($kondisi_stats);
        
        const labels = kondisiData.map(item => item.kondisi);
        const values = kondisiData.map(item => item.total);
        
        const colors = {
            'Baik': '#198754',
            'Kurang Baik': '#ffc107',
            'Rusak Berat': '#dc3545',
            'Hilang': '#6c757d'
        };
        
        const bgColors = labels.map(label => colors[label] || '#0d6efd');

        new Chart(document.querySelector('#conditionChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Assets',
                    data: values,
                    backgroundColor: bgColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
<style>
    .bg-primary-light { background: #e0f2fe; }
    .bg-success-light { background: #dcfce7; }
    .bg-info-light { background: #e0f7fa; }
    .card-icon { width: 48px; height: 48px; font-size: 24px; }
</style>
@endpush
