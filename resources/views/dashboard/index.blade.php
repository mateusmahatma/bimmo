@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')
@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp

{{-- PAGE HEADER --}}
<div class="pagetitle dashboard-pagetitle mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="{{ $uiStyle === 'milenial' ? 'fw-extrabold ls-1' : '' }}">{{ __('Dashboard') }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted" style="font-size: 0.88rem;">
                {{ __('Hai') }}, <strong class="{{ $uiStyle === 'milenial' ? 'text-primary' : 'text-dark' }}">{{ auth()->user()->name }}</strong> 👋
            </span>
            <span class="dashboard-date-badge">
                <i class="bi bi-calendar3"></i>
                {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-light glass-card border-0' : 'btn-outline-secondary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#notesModal">
            <i class="bi bi-sticky fs-6 text-primary"></i>
            <span class="fw-semibold">{{ __('Reminders') }}</span>
        </button>
        <a href="{{ route('transaksi.create') }}"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-primary' : 'btn-primary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill fs-6"></i>
            <span class="fw-semibold">{{ __('Record Now') }}</span>
        </a>
    </div>
</div>

<section class="dashboard">
    <div class="row g-4">

        {{-- KOLOM KIRI --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            {{-- FINANCIAL SUMMARY --}}
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                                {{ __('Financial Summary') }}
                            </h5>
                            @if($totalNominalSisa > 0)
                                <span class="badge bg-success" style="font-size: 0.65rem;">{{ __('Surplus') }}</span>
                            @elseif($totalNominalSisa < 0)
                                <span class="badge bg-danger" style="font-size: 0.65rem;">{{ __('Deficit') }}</span>
                            @endif
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Overview of your financial status this month.') }}</p>
                    </div>
                    <button id="toggleNominalBtn"
                        class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-secondary' }} btn-sm rounded-0 px-3"
                        data-url="{{ route('dashboard.toggle-nominal.ajax') }}">
                        <i class="bi {{ $showNominal ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                    </button>
                </div>

                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">

                        {{-- BALANCE HERO --}}
                        <div class="col-12 col-xl-4">
                            <div class="{{ $uiStyle === 'milenial' ? 'm-hero-balance' : 'card-hero-balance' }} h-100 rounded-0 p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="section-label mb-0" style="color: rgba(255,255,255,0.65);">{{ __('Current Balance') }}</p>
                                        <div class="icon-circle balance">
                                            <i class="bi bi-wallet2"></i>
                                        </div>
                                    </div>
                                    <h2 class="mb-1 fw-extrabold text-white stat-number-lg" id="summary-saldo">{{ $saldoView }}</h2>
                                </div>
                                <div class="mt-2 growth-detail-trigger"
                                     style="cursor: pointer;"
                                     data-title="{{ __('Balance Change') }}"
                                     data-current="{{ round($numbers['saldo'], 0) }}"
                                     data-last="{{ round($saldoLalu, 0) }}"
                                     data-percent="{{ $persenSaldo }}"
                                     data-type="balance">
                                    @if($persenSaldo > 0)
                                        <span class="text-white fw-bold" style="font-size:0.82rem;"><i class="bi bi-arrow-up-short"></i>{{ abs($persenSaldo) }}%</span>
                                    @elseif($persenSaldo < 0)
                                        <span class="text-white fw-bold" style="font-size:0.82rem;"><i class="bi bi-arrow-down-short"></i>{{ abs($persenSaldo) }}%</span>
                                    @else
                                        <span class="text-white opacity-50" style="font-size:0.82rem;"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                    <span class="text-white opacity-50 ms-1" style="font-size: 0.78rem;">{{ __('vs last month') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- OTHER STATS --}}
                        <div class="col-12 col-xl-8">
                            <div class="row g-3">

                                {{-- INCOME --}}
                                <div class="col-6">
                                    <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="background: #f8fafc; border-radius: 12px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <p class="section-label mb-0">{{ __('Total Income') }}</p>
                                                <div class="icon-circle income">
                                                    <i class="bi bi-graph-up-arrow"></i>
                                                </div>
                                            </div>
                                            <h4 class="mb-1 fw-bold text-success stat-number-md" id="summary-pemasukan">{{ $pemasukanView }}</h4>
                                            <div class="growth-detail-trigger"
                                                 style="cursor: pointer;"
                                                 data-title="{{ __('Income Change') }}"
                                                 data-current="{{ round($numbers['pemasukan'], 0) }}"
                                                 data-last="{{ round($pemasukanLalu, 0) }}"
                                                 data-percent="{{ $persenPemasukan }}"
                                                 data-type="income">
                                                @if($persenPemasukan > 0)
                                                    <span class="text-success fw-bold stat-vs-label"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPemasukan) }}%</span>
                                                @elseif($persenPemasukan < 0)
                                                    <span class="text-danger fw-bold stat-vs-label"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPemasukan) }}%</span>
                                                @else
                                                    <span class="text-muted stat-vs-label"><i class="bi bi-dash"></i> 0%</span>
                                                @endif
                                                <span class="stat-vs-label ms-1">{{ __('vs last month') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- EXPENSE --}}
                                <div class="col-6">
                                    <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="background: #f8fafc; border-radius: 12px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <p class="section-label mb-0">{{ __('Total Expense') }}</p>
                                                <div class="icon-circle expense">
                                                    <i class="bi bi-graph-down-arrow"></i>
                                                </div>
                                            </div>
                                            <h4 class="mb-1 fw-bold text-danger stat-number-md" id="summary-pengeluaran">{{ $pengeluaranView }}</h4>
                                            <div class="growth-detail-trigger"
                                                 style="cursor: pointer;"
                                                 data-title="{{ __('Expense Change') }}"
                                                 data-current="{{ round($numbers['pengeluaran'], 0) }}"
                                                 data-last="{{ round($pengeluaranLalu, 0) }}"
                                                 data-percent="{{ $persenPengeluaran }}"
                                                 data-type="expense">
                                                @if($persenPengeluaran > 0)
                                                    <span class="text-danger fw-bold stat-vs-label"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                                @elseif($persenPengeluaran < 0)
                                                    <span class="text-success fw-bold stat-vs-label"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                                @else
                                                    <span class="text-muted stat-vs-label"><i class="bi bi-dash"></i> 0%</span>
                                                @endif
                                                <span class="stat-vs-label ms-1">{{ __('vs last month') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TODAY --}}
                                <div class="col-6">
                                    <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="background: #f8fafc; border-radius: 12px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <p class="section-label mb-0">{{ __('Expense Today') }}</p>
                                                <div class="icon-circle today">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                            </div>
                                            <h4 class="mb-1 fw-bold text-dark stat-number-md" id="summary-hari-ini">{{ $pengeluaranHariIni }}</h4>
                                            <span class="stat-vs-label">{{ __('Daily monitoring') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- INSTALLMENT --}}
                                <div class="col-6">
                                    <div class="card border-0 shadow-none h-100 {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="background: #f8fafc; border-radius: 12px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <p class="section-label mb-0">{{ __('Next Installment') }}</p>
                                                <div class="icon-circle install">
                                                    <i class="bi bi-calendar-check"></i>
                                                </div>
                                            </div>
                                            <h4 class="mb-1 fw-bold text-primary stat-number-md" id="summary-cicilan-besok">{{ $cicilanBesokView }}</h4>
                                            <span class="stat-vs-label">{{ __('Repayment goal') }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- CASH FLOW --}}
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                            {{ __('Cash Flow') }}
                            <span class="text-muted fw-normal ms-1" id="cashFlowPeriodeLabel" style="font-size: 0.8rem;">
                                ({{ request('periode', 6) }} {{ __('months ago') }})
                            </span>
                        </h5>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Monitor your income and expenses over time.') }}</p>
                    </div>
                    <select id="filterPeriode" name="periode" class="form-select form-select-sm rounded-0" style="width:120px">
                        <option value="2"  {{ request('periode') == 2  ? 'selected' : '' }}>2 {{ __('Months') }}</option>
                        <option value="6"  {{ request('periode', 6) == 6  ? 'selected' : '' }}>6 {{ __('Months') }}</option>
                        <option value="12" {{ request('periode') == 12 ? 'selected' : '' }}>12 {{ __('Months') }}</option>
                    </select>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div id="cashflowChart" style="height: 300px;"></div>
                    <div id="cashflowTableContainer" class="table-responsive mt-3">
                        @include('dashboard.partials.cashflow-table', ['cashflow' => $cashflow])
                    </div>
                </div>
            </div>

            {{-- NET WORTH --}}
            <div class="card card-dashboard border-0 border-0 shadow-none networth-card {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}"
                 style="cursor: pointer;"
                 data-bs-toggle="modal" data-bs-target="#netWorthModal"
                 title="{{ __('Click to view Net Worth Details') }}">
                <div class="card-header d-flex justify-content-between align-items-center {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                            {{ __('Net Worth') }}
                        </h5>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Your solvency and wealth status.') }}</p>
                    </div>
                    <button type="button"
                        class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-primary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-1"
                        data-bs-toggle="modal" data-bs-target="#netWorthModal">
                        <i class="bi bi-clock-history"></i>
                        <span class="fw-semibold" style="font-size: 0.75rem;">{{ __('History') }}</span>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-1 {{ $netWorthFormatted >= 1 ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.2rem; letter-spacing: -1px;">
                            {{ $netWorthFormatted }}
                        </h2>
                    </div>

                    <div class="progress-clean mb-4">
                        @php
                            $totalVal = ($totalAset + $totalDanaDarurat) + $totalHutang;
                            $assetPct = $totalVal > 0 ? (($totalAset + $totalDanaDarurat) / $totalVal) * 100 : 0;
                            $debtPct  = $totalVal > 0 ? ($totalHutang / $totalVal) * 100 : 0;
                        @endphp
                        <div class="d-flex h-100">
                            <div class="{{ $uiStyle === 'milenial' ? '' : 'bg-success' }} border-0 shadow-none"
                                 style="width: {{ $assetPct }}%; border-radius: 100px 0 0 100px; background: {{ $uiStyle === 'milenial' ? 'var(--m-secondary-gradient)' : '' }};">
                            </div>
                            <div class="{{ $uiStyle === 'milenial' ? '' : 'bg-danger' }} border-0 shadow-none"
                                 style="width: {{ $debtPct }}%; border-radius: 0 100px 100px 0; background: {{ $uiStyle === 'milenial' ? 'var(--m-danger-gradient)' : '' }};">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 text-center mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'stat-card-inner' }}">
                                <p class="section-label mb-1">{{ __('Wealth') }}</p>
                                <h6 class="fw-bold mb-0 text-success">Rp {{ number_format($totalAset + $totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-danger bg-opacity-5' : 'stat-card-inner' }}">
                                <p class="section-label mb-1">{{ __('Debt') }}</p>
                                <h6 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    @if($netWorth >= 1)
                    <div class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success' }} text-center">
                        <i class="bi bi-shield-check-fill me-1"></i>
                        <span>{{ __('Your assets comfortably cover your debts.') }}</span>
                    </div>
                    @else
                    <div class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-warning bg-opacity-10 text-warning' : 'alert-warning' }} text-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <span>{{ __('Your debt level is higher than your assets.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            {{-- EMERGENCY FUND --}}
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="overflow: hidden;">
                <div class="card-header {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Emergency Fund Progress') }}</h5>
                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Track your safety net status.') }}</p>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-0 text-primary {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.4rem; letter-spacing: -1px;">{{ $persentaseDanaDarurat }}%</h2>
                        <p class="section-label mt-1 mb-0">{{ __('Achieved') }}</p>
                    </div>

                    <div class="progress-clean mb-4">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: {{ $persentaseDanaDarurat }}%; height: 100%; border-radius: 100px; background: {{ $uiStyle === 'milenial' ? 'var(--m-primary-gradient)' : '#2563eb' }};"
                             aria-valuenow="{{ $persentaseDanaDarurat }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="row g-3 text-center mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : '' }}" style="{{ $uiStyle !== 'milenial' ? 'background:#f8fafc;' : '' }}">
                                <p class="section-label mb-1">{{ __('Actual') }}</p>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-0 stat-card-inner">
                                <p class="section-label mb-1">{{ __('Target') }}</p>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    @if($persentaseDanaDarurat >= 100)
                    <div class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success' }} text-center">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        <span>{{ __('Goal Reached! You are financially safe.') }}</span>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0" style="font-size: 0.82rem;">
                        {{ __('Keep up the good work! Just') }}
                        <strong class="text-primary">Rp {{ number_format(max(0, $targetDanaDarurat - $totalDanaDarurat), 0, ',', '.') }}</strong>
                        {{ __('left to reach your target.') }}
                    </p>
                    @endif
                </div>
            </div>

            {{-- EXPENSE BAR --}}
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Expense Bar') }}</h5>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Expenses breakdown by category.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <select id="filterBulan" name="bulan" class="form-select form-select-sm rounded-0" style="width: 110px;">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="filterTahun" name="tahun" class="form-select form-select-sm rounded-0" style="width: 80px;">
                            @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="expenseBarTableContainer">
                        @include('dashboard.partials.expense-bar-table', ['pengeluaranKategori' => $pengeluaranKategori])
                    </div>
                    <div class="px-4 py-3 border-top {{ $uiStyle === 'milenial' ? 'bg-transparent' : 'card-footer-strip' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="section-label">{{ __('Total Expenditures') }}</span>
                            <strong id="totalPengeluaranValue" class="text-dark">Rp {{ number_format((float)$totalPengeluaranBulan, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- CALENDAR (full width) --}}
        <div class="col-12">
            @include('dashboard.partials.calendar')
        </div>

        {{-- TODAY'S TRANSACTIONS (full width) --}}
        <div class="col-12">
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __("Today's Transactions") }}</h5>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dashboard {{ $uiStyle === 'milenial' ? 'table-borderless' : '' }} align-middle mb-0">
                            @if($uiStyle !== 'milenial')
                            <thead>
                                <tr class="table-header-strip">
                                    <th class="px-4 py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Time') }}</th>
                                    <th class="py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Type') }}</th>
                                    <th class="py-3 section-label border-bottom" style="font-size: 0.68rem;">{{ __('Explanation') }}</th>
                                    <th class="py-3 pe-4 text-end section-label border-bottom" style="font-size: 0.68rem;">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            @endif
                            <tbody>
                                @forelse ($transaksiHariIni as $row)
                                <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
                                    <td class="{{ $uiStyle === 'milenial' ? 'ps-4' : 'ps-4' }}">
                                        <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($row->waktu)->format('H:i') }}</div>
                                        @if($uiStyle === 'milenial')
                                        <div class="stat-vs-label">{{ $row->kategori }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-type {{ $row->jenis === 'pemasukan' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ ucfirst($row->jenis) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{!! $row->keterangan ?? '-' !!}</div>
                                        @if($uiStyle !== 'milenial')
                                        <div class="stat-vs-label">{{ $row->kategori }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold {{ $row->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'pe-4' : 'pe-4' }}">
                                        {{ $row->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format((float)$row->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        {{ __('No transactions today') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary footer --}}
                    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3 {{ $uiStyle !== 'milenial' ? 'card-footer-strip' : '' }}">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle income" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px;">
                                <i class="bi bi-arrow-down-left"></i>
                            </div>
                            <span class="text-success fw-semibold" style="font-size: 0.85rem;">
                                {{ __('Income') }}: <strong>Rp {{ number_format((float)$totalMasukHariIni, 0, ',', '.') }}</strong>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle expense" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px;">
                                <i class="bi bi-arrow-up-right"></i>
                            </div>
                            <span class="text-danger fw-semibold" style="font-size: 0.85rem;">
                                {{ __('Expenses') }}: <strong>Rp {{ number_format((float)$totalKeluarHariIni, 0, ',', '.') }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BUDGET PERFORMANCE (full width) --}}
        <div class="col-12">
            <div class="card card-dashboard border-0 border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Budget Performance') }}</h5>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Track your budget usage.') }}</p>
                    </div>
                    <select id="filterTanggal" class="form-select form-select-sm rounded-0" style="width: 180px;">
                        <option value="">{{ __('All Data') }}</option>
                        @foreach($filterOptions as $row)
                        <option value="{{ $row->tanggal_mulai }}_{{ $row->tanggal_selesai }}">
                            {{ $row->tanggal_mulai }} s/d {{ $row->tanggal_selesai }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body p-3 p-md-4">
                    @include('dashboard.anggaran')
                    @stack('anggaran-css')
                    @stack('anggaran.scripts')
                </div>
            </div>
        </div>

        @include('modal.dashboard.growth_detail')
        @include('modal.dashboard.detail')
        @include('modal.dashboard.rasio')
        @include('modal.dashboard.notes')
        @include('modal.dashboard.networth')
    </div>
</section>
@endsection

@push('scripts')
<script>
    window.cashflowData = @json($cashflow ?? []);
    window.dashboardFilterUrl = "{{ route('dashboard.filter') }}";
    window.eventsUrl = "{{ url('events') }}";
    window.netWorthData = null;
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard-cashflow.js') }}?v={{ filemtime(public_path('js/dashboard-cashflow.js')) }}"></script>
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
<script src="{{ asset('js/calendar.js') }}?v={{ filemtime(public_path('js/calendar.js')) }}"></script>
@endpush