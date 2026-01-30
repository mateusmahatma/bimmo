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

<!-- <div id="dashboardCarousel" class="carousel slide mb-4 rounded-4 shadow-sm overflow-hidden" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('img/gambar1.png') }}" class="d-block w-100"
                alt="Banner 1" style="object-fit: cover; height: 250px;">
        </div>
        <div class="carousel-item">
            <img src="{{ asset('img/gambar2.png') }}" class="d-block w-100"
                alt="Banner 2" style="object-fit: cover; height: 250px;">
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sebelumnya</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Selanjutnya</span>
    </button>
</div> -->

<section class="dashboard">
    <div class="row g-4">

        <!-- Kolom Kiri -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            <div class="finance-summary card-dashboard">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="d-flex align-items-center gap-2">
                        Financial Summary
                        @if($totalNominalSisa > 0)
                        <span class="badge bg-success">Surplus</span>
                        @else
                        <span class="badge bg-danger">Defisit</span>
                        @endif
                    </h3>
                    <button id="toggleNominalBtn"
                        class="btn btn-sm btn-outline-secondary"
                        data-url="{{ route('dashboard.toggle-nominal.ajax') }}">
                        <i class="bi {{ $showNominal ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                    </button>
                </div>
                <ul class="box-info">

                    <div class="row g-3">

                        {{-- SALDO (PRIMARY CARD) --}}
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="summary-card primary">
                                <div class="label">This Month's Balance</div>
                                <div class="value {{ $totalNominalSisa >= 0 ? 'text-success' : 'text-danger' }}" id="summary-saldo">
                                    {{ $saldoView }}
                                </div>
                            </div>
                        </div>

                        {{-- PEMASUKAN --}}
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="summary-card">
                                <div class="label">Income This Month</div>
                                <div class="value text-success" id="summary-pemasukan">
                                    {{ $pemasukanView }}
                                </div>
                            </div>
                        </div>

                        {{-- PENGELUARAN --}}
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="summary-card">
                                <div class="label">Expense This Month</div>
                                <div class="value text-danger" id="summary-pengeluaran">
                                    {{ $pengeluaranView }}
                                </div>
                            </div>
                        </div>

                        {{-- HARI INI --}}
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="summary-card">
                                <div class="label">Expense Today</div>
                                <div class="value" id="summary-hari-ini">
                                    {{ $pengeluaranHariIni }}
                                </div>
                            </div>
                        </div>
                    </div>

                </ul>

            </div>

            <div class="cash-flow">
                <form method="GET" class="mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="fw-semibold mb-0">Period:</label>

                        <select name="periode"
                            class="form-select form-select-sm"
                            style="width:150px"
                            onchange="this.form.submit()">
                            <option value="2" {{ request('periode') == 2 ? 'selected' : '' }}>2 Months</option>
                            <option value="6" {{ request('periode', 6) == 6 ? 'selected' : '' }}>6 Months</option>
                            <option value="12" {{ request('periode') == 12 ? 'selected' : '' }}>12 Months</option>
                        </select>
                    </div>
                </form>

                <div class="card-header mb-3">
                    <h3 class="mb-1">
                        Cash Flow
                        <span class="text-muted fs-6">
                            ({{ request('periode', 6) }} months ago)
                        </span>
                    </h3>
                </div>

                {{-- CHART --}}
                <div id="cashflowChart" style="height: 300px;"></div>

                <div class="table-responsive mt-3">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Income</th>
                                <th class="text-end">Expense</th>
                                <th class="text-end">Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashflow as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->bulan.'-01')->translatedFormat('F Y') }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($row->total_pemasukan,0,',','.') }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($row->total_pengeluaran,0,',','.') }}
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($row->selisih,0,',','.') }}
                                    @if($row->selisih < 0)
                                        <span class="badge bg-danger ms-1">Defisit</span>
                                        @elseif($row->selisih < 1000000)
                                            <span class="badge bg-warning text-dark ms-1">Tipis</span>
                                            @else
                                            <span class="badge bg-success ms-1">Aman</span>
                                            @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No cash flow data available yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            <div class="card-dashboard">
                <div class="card-header mb-3">
                    <h3 class="mb-1">
                        Saving Rate
                        <span class="text-muted fs-6">
                            ({{ request('periode', 6) }} bulan terakhir)
                        </span>
                    </h3>
                </div>

                {{-- CHART --}}
                <div id="savingRateChart" style="height: 300px;"></div>

                {{-- FALLBACK TABLE --}}
                <div class="table-responsive mt-3">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Saving Rate (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($savingRate as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->bulan.'-01')->translatedFormat('F Y') }}</td>
                                <td class="text-end fw-bold
                                    {{ $row->saving_rate >= 0 ? 'text-success' : 'text-danger' }}">
                                    <span class="badge bg-{{ $row->saving_class }}">
                                        {{ $row->saving_rate }}% – {{ $row->saving_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="small text-muted mt-2">
                        <strong>Explanation:</strong>
                        <span class="badge bg-success">Very Healthy</span>
                        <span class="badge bg-primary">Healthy</span>
                        <span class="badge bg-warning text-dark">Warning</span>
                        <span class="badge bg-danger">Deficit</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            <div class="financial-ratio card-dashboard">
                <h3>Financial Ratio</h3>

                <ul class="list-group list-group-flush">

                    {{-- Expense Ratio --}}
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Expense / Income
                        <span class="badge bg-{{ $expenseStatus['class'] }}">
                            {{ $expenseRatio }}% – {{ $expenseStatus['label'] }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Saving Rate
                        <span class="badge bg-{{ $savingStatus['class'] }}">
                            {{ $savingRateLatest }}% – {{ $savingStatus['label'] }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Emergency Fund
                        <span class="badge bg-{{ $emergencyStatus['class'] }}">
                            {{ $danaDaruratBulan }} months – {{ $emergencyStatus['label'] }}
                        </span>
                    </li>
                </ul>
            </div>


            <div class="card-dashboard">
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h3>Expense Bar</h3>

                    <form method="GET" class="d-flex gap-2">
                        <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                                @endfor
                        </select>

                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                                @endfor
                        </select>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengeluaranKategori as $row)
                            <tr>
                                <td>{{ $row->kategori }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($row->total,0,',','.') }}
                                </td>
                                <td class="text-end fw-bold
                                    {{ $row->persen > 40 ? 'text-danger' : ($row->persen > 25 ? 'text-warning' : 'text-success') }}">
                                    {{ $row->persen }}%
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No expense data available
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="small text-muted mt-2">
                    Total Expenditures:
                    <strong>Rp {{ number_format($totalPengeluaranBulan,0,',','.') }}</strong>
                </div>
            </div>

        </div>

        <!-- Transaksi Hari Ini -->
        <div class="card-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Today's Transactions</h3>

                <span class="badge bg-light text-dark">
                    {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                </span>
            </div>

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

            <div class="d-flex justify-content-between mt-2 small">
                <span class="text-success">
                    Total Income: <strong>Rp {{ number_format($totalMasukHariIni,0,',','.') }}</strong>
                </span>
                <span class="text-danger">
                    Total Expenses: <strong>Rp {{ number_format($totalKeluarHariIni,0,',','.') }}</strong>
                </span>
            </div>
        </div>

        <div class="col-12">
            <div class="card-dashboard">
                <h3 class="fw-bold mb-4">Budget Performance</h3>
                @include('dashboard.anggaran')
                @stack('anggaran-css')
                @stack('anggaran.scripts')
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
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-cashflow.js') }}"></script>
<script src="{{ asset('js/dashboard-saving-rate.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush