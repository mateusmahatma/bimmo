@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')
<div class="pagetitle mb-4">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                Hai, {{ auth()->user()->name }}
            </li>
        </ol>
    </nav>
</div>

<section class="dashboard">
    <div class="row g-4">

        <!-- Kolom Kiri -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                            Financial Summary
                            @if($totalNominalSisa > 0)
                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">Surplus</span>
                            @elseif($totalNominalSisa < 0)
                            <span class="badge bg-danger ms-2" style="font-size: 0.7em;">Defisit</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Overview of your financial status this month.</p>
                    </div>
                    <button id="toggleNominalBtn"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm"
                        data-url="{{ route('dashboard.toggle-nominal.ajax') }}">
                        <i class="bi {{ $showNominal ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                    </button>
                </div>
                <div class="card-body p-3 p-md-4">
                <div class="row g-3 mb-4">
                    <!-- BALANCE -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Current Balance</h6>
                                        <h4 class="mb-2 fw-bold {{ $totalNominalSisa >= 0 ? 'text-dark' : 'text-danger' }}" id="summary-saldo">{{ $saldoView }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-wallet2 fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    @if($persenSaldo > 0)
                                        <span class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i>{{ abs($persenSaldo) }}%</span>
                                    @elseif($persenSaldo < 0)
                                        <span class="text-danger fw-bold"><i class="bi bi-arrow-down-short"></i>{{ abs($persenSaldo) }}%</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="text-muted ms-1" style="font-size: 0.8rem;">vs last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INCOME -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Income</h6>
                                        <h4 class="mb-2 fw-bold text-success" id="summary-pemasukan">{{ $pemasukanView }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-success rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-graph-up-arrow fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    @if($persenPemasukan > 0)
                                        <span class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPemasukan) }}%</span>
                                    @elseif($persenPemasukan < 0)
                                        <span class="text-danger fw-bold"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPemasukan) }}%</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="text-muted ms-1" style="font-size: 0.8rem;">vs last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EXPENSE -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Expense</h6>
                                        <h4 class="mb-2 fw-bold text-danger" id="summary-pengeluaran">{{ $pengeluaranView }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-danger rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-graph-down-arrow fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    {{-- Expense Logic: Increase is BAD (Red), Decrease is GOOD (Green) --}}
                                    @if($persenPengeluaran > 0)
                                        <span class="text-danger fw-bold"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                    @elseif($persenPengeluaran < 0)
                                        <span class="text-success fw-bold"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="text-muted ms-1" style="font-size: 0.8rem;">vs last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TODAY -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Expense Today</h6>
                                        <h4 class="mb-2 fw-bold text-dark" id="summary-hari-ini">{{ $pengeluaranHariIni }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-warning rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-calendar-event fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    <span class="text-muted" style="font-size: 0.8rem;">Monitor daily spending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                            Cash Flow
                            <span class="text-muted small fw-normal ms-1" id="cashFlowPeriodeLabel">
                                ({{ request('periode', 6) }} months ago)
                            </span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Monitor your income and expenses over time.</p>
                    </div>
                    <div class="m-0">
                        <select id="filterPeriode"
                            name="periode"
                            class="form-select form-select-sm rounded-pill shadow-sm"
                            style="width:120px">
                            <option value="2" {{ request('periode') == 2 ? 'selected' : '' }}>2 Months</option>
                            <option value="6" {{ request('periode', 6) == 6 ? 'selected' : '' }}>6 Months</option>
                            <option value="12" {{ request('periode') == 12 ? 'selected' : '' }}>12 Months</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">

                <div id="cashflowChart" style="height: 300px;"></div>

                <div id="cashflowTableContainer" class="table-responsive mt-3">
                    @include('dashboard.partials.cashflow-table', ['cashflow' => $cashflow])
                </div>
                </div>
            </div>



            <!-- <div class="card-dashboard">
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h3>Rasio Tabungan</h3>
                    <select id="filterPeriodSavingRate" class="filter-dropdown">
                        <option value="2">2 Bulan</option>
                        <option value="4">4 Bulan</option>
                        <option value="6" selected>6 Bulan</option>
                        <option value="12">1 Tahun</option>
                        <option value="all">Semua</option>
                    </select>
                </div>

                <div class="alert alert-secondary mb-0">
                    Rasio tabungan sedang disiapkan.
                </div>
            </div> -->
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                     <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                             Saving Rate
                            <span class="text-muted small fw-normal ms-1" id="savingRatePeriodeLabel">
                                ({{ request('periode', 6) }} bulan terakhir)
                            </span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Track your savings performance.</p>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">

                {{-- CHART --}}
                <div id="savingRateChart" style="height: 300px;"></div>

                {{-- FALLBACK TABLE --}}
                <div id="savingRateTableContainer" class="table-responsive mt-3">
                    @include('dashboard.partials.saving-rate-table', ['savingRate' => $savingRate])
                </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Financial Ratio</h5>
                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Key financial health indicators.</p>
                </div>
                <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                    {{-- Expense Ratio --}}
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        Expense / Income
                        <span class="badge bg-{{ $expenseStatus['class'] }}">
                            {{ $expenseRatio }}% – {{ $expenseStatus['label'] }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        Saving Rate
                        <span class="badge bg-{{ $savingStatus['class'] }}">
                            {{ $savingRateLatest }}% – {{ $savingStatus['label'] }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        Emergency Fund
                        <span class="badge bg-{{ $emergencyStatus['class'] }}">
                            {{ $danaDaruratBulan }} months – {{ $emergencyStatus['label'] }}
                        </span>
                    </li>
                </ul>
                </div>
            </div>


            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Expense Bar</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Expenses breakdown by category.</p>
                    </div>

                    <div class="d-flex gap-2">
                        <select id="filterBulan" name="bulan" class="form-select form-select-sm rounded-pill shadow-sm">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                                @endfor
                        </select>

                        <select id="filterTahun" name="tahun" class="form-select form-select-sm rounded-pill shadow-sm">
                            @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                                @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">

                <div id="expenseBarTableContainer">
                    @include('dashboard.partials.expense-bar-table', ['pengeluaranKategori' => $pengeluaranKategori])
                </div>

                <div class="p-3 border-top bg-light rounded-bottom">
                    <div class="small text-muted">
                        Total Expenditures:
                        <strong id="totalPengeluaranValue">Rp {{ number_format($totalPengeluaranBulan,0,',','.') }}</strong>
                    </div>
                </div>
                </div>
            </div>

        </div>

        <!-- Transaksi Hari Ini -->
        <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Today's Transactions</h5>
                     <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">
                        {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
            <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Explanation</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiHariIni as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->waktu)->format('H:i') }}</td>

                            <td>
                                <span class="badge {{ $row->jenis === 'pemasukan' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($row->jenis) }}
                                </span>
                            </td>

                            <td>
                                {{ $row->keterangan ?? '-' }}
                                <div class="small text-muted">{{ $row->kategori }}</div>
                            </td>

                            <td class="text-end fw-bold {{ $row->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($row->nominal,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada transaksi hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top bg-light rounded-bottom d-flex justify-content-between small">
                <span class="text-success">
                    Total Income: <strong>Rp {{ number_format($totalMasukHariIni,0,',','.') }}</strong>
                </span>
                <span class="text-danger">
                    Total Expenses: <strong>Rp {{ number_format($totalKeluarHariIni,0,',','.') }}</strong>
                </span>
            </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                 <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Budget Performance</h5>
                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Track your budget usage.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    @include('dashboard.anggaran')
                    @stack('anggaran-css')
                    @stack('anggaran.scripts')
                </div>
            </div>
        </div>

        @include('modal.dashboard.detail')
        @include('modal.dashboard.rasio')
</section>
@endsection

@push('scripts')
<script>
    window.cashflowData = @json($cashflow ?? []);
    window.savingRateData = @json($savingRate ?? []);
    window.dashboardFilterUrl = "{{ route('dashboard.filter') }}";
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-cashflow.js') }}"></script>
<script src="{{ asset('js/dashboard-saving-rate.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush