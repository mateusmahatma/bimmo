@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')
<div class="pagetitle mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>{{ __('Dashboard') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    {{ __('Hai') }}, {{ auth()->user()->name }}
                </li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-light-corporate rounded-pill px-4 border shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#notesModal">
            <i class="bi bi-sticky fs-5 text-primary"></i>
            <span class="fw-bold">{{ __('Reminders') }}</span>
        </button>
        <a href="{{ route('transaksi.create') }}" class="btn btn-danger rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill fs-5"></i>
            <span class="fw-bold">{{ __('Record Now') }}</span>
        </a>
    </div>
</div>

<section class="dashboard">
    <div class="row g-4">

        <!-- Kolom Kiri -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                            {{ __('Financial Summary') }}
                            @if($totalNominalSisa > 0)
                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">{{ __('Surplus') }}</span>
                            @elseif($totalNominalSisa < 0)
                            <span class="badge bg-danger ms-2" style="font-size: 0.7em;">{{ __('Deficit') }}</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Overview of your financial status this month.') }}</p>
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
                    <div class="col-12 col-md-6 col-xl">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Current Balance') }}</h6>
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
                                    <span class="text-muted ms-1" style="font-size: 0.8rem;">{{ __('vs last month') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INCOME -->
                    <div class="col-12 col-md-6 col-xl">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Total Income') }}</h6>
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
                    <div class="col-12 col-md-6 col-xl">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Total Expense') }}</h6>
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

                    <!-- NEXT INSTALLMENT -->
                    <div class="col-12 col-md-6 col-xl">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Next Installment') }}</h6>
                                        <h4 class="mb-2 fw-bold text-primary" id="summary-cicilan-besok">{{ $cicilanBesokView }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-calendar-check fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    <span class="text-muted" style="font-size: 0.8rem;">{{ __('Next repayment goal') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TODAY -->
                    <div class="col-12 col-md-6 col-xl">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Expense Today') }}</h6>
                                        <h4 class="mb-2 fw-bold text-dark" id="summary-hari-ini">{{ $pengeluaranHariIni }}</h4>
                                    </div>
                                    <div class="icon-shape bg-light text-warning rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="bi bi-calendar-event fs-5"></i>
                                    </div>
                                </div>
                                <div class="mt-2 small">
                                    <span class="text-muted" style="font-size: 0.8rem;">{{ __('Monitor daily spending') }}</span>
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
                            {{ __('Cash Flow') }}
                            <span class="text-muted small fw-normal ms-1" id="cashFlowPeriodeLabel">
                                ({{ request('periode', 6) }} {{ __('months ago') }})
                            </span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Monitor your income and expenses over time.') }}</p>
                    </div>
                    <div class="m-0">
                        <select id="filterPeriode"
                            name="periode"
                            class="form-select form-select-sm rounded-pill shadow-sm"
                            style="width:120px">
                            <option value="2" {{ request('periode') == 2 ? 'selected' : '' }}>2 {{ __('Months') }}</option>
                            <option value="6" {{ request('periode', 6) == 6 ? 'selected' : '' }}>6 {{ __('Months') }}</option>
                            <option value="12" {{ request('periode') == 12 ? 'selected' : '' }}>12 {{ __('Months') }}</option>
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
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                     <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                             {{ __('Net Worth') }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Your solvency and wealth status.') }}</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <h2 class="fw-bold mb-0 {{ $netWorthFormatted >= 1 ? 'text-success' : 'text-danger' }}" style="font-size: 2.5rem;">{{ $netWorthFormatted }}</h2>
                            {{-- <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 1px;">{{ __('Asset-to-Debt Ratio') }}</p> --}}
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 12px; border-radius: 10px; background-color: #f0f2f5;">
                        @php
                            $totalVal = ($totalAset + $totalDanaDarurat) + $totalHutang;
                            $assetPct = $totalVal > 0 ? (($totalAset + $totalDanaDarurat) / $totalVal) * 100 : 0;
                            $debtPct = $totalVal > 0 ? ($totalHutang / $totalVal) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success shadow-sm"
                             role="progressbar"
                             style="width: {{ $assetPct }}%; border-radius: 10px 0 0 10px;"
                             aria-valuenow="{{ $assetPct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                        <div class="progress-bar bg-danger shadow-sm"
                             role="progressbar"
                             style="width: {{ $debtPct }}%; border-radius: 0 10px 10px 0;"
                             aria-valuenow="{{ $debtPct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-none">
                                <p class="text-muted small mb-1"><a href="{{ url($prefix ?? 'aset') }}" target="_blank" class="text-decoration-underline">{{ __('Total Assets') }}</a></p>+
                                <p class="text-muted small mb-1"><a href="{{ url($prefix ?? 'dana-darurat') }}" target="_blank" class="text-decoration-underline">{{ __('Emergency Fund') }}</a></p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalAset + $totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-none">
                                <p class="text-muted small mb-1"><a href="{{ url($prefix ?? 'pinjaman') }}" target="_blank" class="text-decoration-underline">{{ __('Total Debt') }}</a></p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        @if($netWorth >= 1)
                        <div class="alert alert-success border-0 shadow-sm py-2 mb-0" style="border-radius: 10px;">
                            <i class="bi bi-shield-check-fill me-2"></i>
                            <span class="small fw-bold">{{ __('Your assets comfortably cover your debts.') }}</span>
                        </div>
                        @else
                        <div class="alert alert-warning border-0 shadow-sm py-2 mb-0" style="border-radius: 10px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span class="small fw-bold">{{ __('Your debt level is higher than your current assets.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Emergency Fund Progress') }}</h5>
                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Track your safety net status.') }}</p>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <h2 class="fw-bold mb-0 text-primary" style="font-size: 2.5rem;">{{ $persentaseDanaDarurat }}%</h2>
                            <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 1px;">{{ __('Achieved') }}</p>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 12px; border-radius: 10px; background-color: #f0f2f5;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary shadow-sm"
                             role="progressbar"
                             style="width: {{ $persentaseDanaDarurat }}%; border-radius: 10px;"
                             aria-valuenow="{{ $persentaseDanaDarurat }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-none">
                                <p class="text-muted small mb-1">{{ __('Actual') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-none">
                                <p class="text-muted small mb-1">{{ __('Target') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    @if($persentaseDanaDarurat >= 100)
                    <div class="alert alert-success border-0 shadow-sm mt-4 mb-0 py-2 text-center" style="border-radius: 10px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span class="small fw-bold">{{ __('Goal Reached! You are financially safe.') }}</span>
                    </div>
                    @else
                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-0">
                            {{ __('Keep up the good work! Just') }}
                            <span class="fw-bold text-primary">Rp {{ number_format(max(0, $targetDanaDarurat - $totalDanaDarurat), 0, ',', '.') }}</span>
                            {{ __('left to reach your target.') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>


            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Expense Bar') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Expenses breakdown by category.') }}</p>
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
                        {{ __('Total Expenditures') }}:
                        <strong id="totalPengeluaranValue">Rp {{ number_format((float)$totalPengeluaranBulan,0,',','.') }}</strong>
                    </div>
                </div>
                </div>
            </div>

        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-12 mb-4">
            @include('dashboard.partials.calendar')
        </div>

        <div class="col-12">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __("Today's Transactions") }}</h5>
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
                            <th>{{ __('Time') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Explanation') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
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
                                Rp {{ number_format((float)$row->nominal,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                {{ __('No transactions today') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top bg-light rounded-bottom d-flex justify-content-between small">
                <span class="text-success">
                    {{ __('Total Income') }}: <strong>Rp {{ number_format((float)$totalMasukHariIni,0,',','.') }}</strong>
                </span>
                <span class="text-danger">
                    {{ __('Total Expenses') }}: <strong>Rp {{ number_format((float)$totalKeluarHariIni,0,',','.') }}</strong>
                </span>
            </div>
            </div>
        </div>

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                 <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Budget Performance') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Track your budget usage.') }}</p>
                    </div>
                    <div class="m-0">
                        <select id="filterTanggal" class="form-select form-select-sm rounded-pill shadow-sm" style="width: 150px;">
                            <option value="">{{ __('All Data') }}</option>
                            @foreach($filterOptions as $row)
                            <option value="{{ $row->tanggal_mulai }}_{{ $row->tanggal_selesai }}">
                                {{ $row->tanggal_mulai }} s/d {{ $row->tanggal_selesai }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    @include('dashboard.anggaran')
                    @stack('anggaran-css')
                    @stack('anggaran.scripts')
                </div>
            </div>

        @include('modal.dashboard.detail')
        @include('modal.dashboard.rasio')
        @include('modal.dashboard.notes')
</section>
@endsection

@push('scripts')
<script>
    window.cashflowData = @json($cashflow ?? []);
    window.dashboardFilterUrl = "{{ route('dashboard.filter') }}";
    window.eventsUrl = "{{ url('events') }}";
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-cashflow.js') }}?v={{ filemtime(public_path('js/dashboard-cashflow.js')) }}"></script>
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
<script src="{{ asset('js/calendar.js') }}?v={{ filemtime(public_path('js/calendar.js')) }}"></script>
@endpush