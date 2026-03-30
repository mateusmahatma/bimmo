@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')
@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp

<div class="pagetitle mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="{{ $uiStyle === 'milenial' ? 'fw-extrabold ls-1' : '' }}">{{ __('Dashboard') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    {{ __('Hai') }}, <span class="{{ $uiStyle === 'milenial' ? 'text-primary fw-bold' : '' }}">{{ auth()->user()->name }}</span>
                </li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn {{ $uiStyle === 'milenial' ? 'btn-light glass-card border-0' : 'btn-light-corporate border' }} rounded-pill px-4 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#notesModal">
            <i class="bi bi-sticky fs-5 text-primary"></i>
            <span class="fw-bold">{{ __('Reminders') }}</span>
        </button>
        <a href="{{ route('transaksi.create') }}" class="btn {{ $uiStyle === 'milenial' ? 'btn-primary' : 'btn-danger' }} rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill fs-5"></i>
            <span class="fw-bold">{{ __('Record Now') }}</span>
        </a>
    </div>
</div>

<section class="dashboard">
    <div class="row g-4">

        <!-- Kolom Kiri -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                            {{ __('Financial Summary') }}
                            @if($totalNominalSisa > 0)
                            <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern bg-success text-white' : 'bg-success' }} ms-2" style="{{ $uiStyle === 'corporate' ? 'font-size: 0.7em;' : '' }}">{{ __('Surplus') }}</span>
                            @elseif($totalNominalSisa < 0)
                            <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern bg-danger text-white' : 'bg-danger' }} ms-2" style="{{ $uiStyle === 'corporate' ? 'font-size: 0.7em;' : '' }}">{{ __('Deficit') }}</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Overview of your financial status this month.') }}</p>
                    </div>
                    <button id="toggleNominalBtn"
                        class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-secondary' }} btn-sm rounded-pill px-3 shadow-sm"
                        data-url="{{ route('dashboard.toggle-nominal.ajax') }}">
                        <i class="bi {{ $showNominal ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                    </button>
                </div>
                <div class="card-body p-3 p-md-4">
                <div class="row g-4 mb-4">
                    <!-- BALANCE (Hero) -->
                    <div class="col-12 col-md-12 col-xl-4">
                        <div class="card {{ $uiStyle === 'milenial' ? 'm-hero-balance shadow-lg' : 'border-0 shadow-sm' }} h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="{{ $uiStyle === 'milenial' ? 'text-white text-uppercase fw-bold ls-1 opacity-75' : 'text-muted text-uppercase fw-bold' }}" style="font-size: 0.7rem;">{{ __('Current Balance') }}</h6>
                                        <div class="icon-shape {{ $uiStyle === 'milenial' ? 'bg-white bg-opacity-20 text-white' : 'bg-light text-primary' }} rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-wallet2 fs-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="mb-2 fw-extrabold {{ $uiStyle === 'milenial' ? 'text-white' : ($totalNominalSisa >= 0 ? 'text-dark' : 'text-danger') }}" id="summary-saldo" style="font-size: 2.2rem; letter-spacing: -1.5px;">{{ $saldoView }}</h2>
                                </div>
                                    <div class="mt-2 small growth-detail-trigger"
                                         style="cursor: pointer;"
                                         data-title="{{ __('Balance Change') }}"
                                         data-current="{{ round($numbers['saldo'], 0) }}"
                                         data-last="{{ round($saldoLalu, 0) }}"
                                         data-percent="{{ $persenSaldo }}"
                                         data-type="balance">
                                        @if($persenSaldo > 0)
                                            <span class="{{ $uiStyle === 'milenial' ? 'text-white fw-bold' : 'text-success fw-bold' }}"><i class="bi bi-arrow-up-short"></i>{{ abs($persenSaldo) }}%</span>
                                        @elseif($persenSaldo < 0)
                                            <span class="{{ $uiStyle === 'milenial' ? 'text-white fw-bold' : 'text-danger fw-bold' }}"><i class="bi bi-arrow-down-short"></i>{{ abs($persenSaldo) }}%</span>
                                        @else
                                            <span class="{{ $uiStyle === 'milenial' ? 'text-white opacity-50' : 'text-muted' }}"><i class="bi bi-dash"></i></span>
                                        @endif
                                        <span class="{{ $uiStyle === 'milenial' ? 'text-white opacity-50' : 'text-muted' }} ms-1" style="font-size: 0.8rem;">{{ __('vs last month') }}</span>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- OTHER STATS -->
                    <div class="col-12 col-md-12 col-xl-8">
                        <div class="row g-3">
                            <!-- INCOME -->
                            <div class="col-12 col-md-6">
                                <div class="card {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'border-0 shadow-sm' }} h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Total Income') }}</h6>
                                                <h4 class="mb-2 fw-bold text-success" id="summary-pemasukan">{{ $pemasukanView }}</h4>
                                            </div>
                                            <div class="icon-shape {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10' : 'bg-light' }} text-success rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="bi bi-graph-up-arrow fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="mt-1 small growth-detail-trigger"
                                             style="cursor: pointer;"
                                             data-title="{{ __('Income Change') }}"
                                             data-current="{{ round($numbers['pemasukan'], 0) }}"
                                             data-last="{{ round($pemasukanLalu, 0) }}"
                                             data-percent="{{ $persenPemasukan }}"
                                             data-type="income">
                                            @if($persenPemasukan > 0)
                                                <span class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPemasukan) }}%</span>
                                            @elseif($persenPemasukan < 0)
                                                <span class="text-danger fw-bold"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPemasukan) }}%</span>
                                            @else
                                                <span class="text-muted"><i class="bi bi-dash"></i> 0%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EXPENSE -->
                            <div class="col-12 col-md-6">
                                <div class="card {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'border-0 shadow-sm' }} h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Total Expense') }}</h6>
                                                <h4 class="mb-2 fw-bold text-danger" id="summary-pengeluaran">{{ $pengeluaranView }}</h4>
                                            </div>
                                            <div class="icon-shape {{ $uiStyle === 'milenial' ? 'bg-danger bg-opacity-10' : 'bg-light' }} text-danger rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="bi bi-graph-down-arrow fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="mt-1 small growth-detail-trigger"
                                             style="cursor: pointer;"
                                             data-title="{{ __('Expense Change') }}"
                                             data-current="{{ round($numbers['pengeluaran'], 0) }}"
                                             data-last="{{ round($pengeluaranLalu, 0) }}"
                                             data-percent="{{ $persenPengeluaran }}"
                                             data-type="expense">
                                            @if($persenPengeluaran > 0)
                                                <span class="text-danger fw-bold"><i class="bi bi-arrow-up-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                            @elseif($persenPengeluaran < 0)
                                                <span class="text-success fw-bold"><i class="bi bi-arrow-down-short"></i>{{ abs($persenPengeluaran) }}%</span>
                                            @else
                                                <span class="text-muted"><i class="bi bi-dash"></i> 0%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TODAY -->
                            <div class="col-12 col-md-6">
                                <div class="card {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'border-0 shadow-sm' }} h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Expense Today') }}</h6>
                                                <h4 class="mb-2 fw-bold text-dark" id="summary-hari-ini">{{ $pengeluaranHariIni }}</h4>
                                            </div>
                                            <div class="icon-shape {{ $uiStyle === 'milenial' ? 'bg-warning bg-opacity-10' : 'bg-light' }} text-warning rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="bi bi-calendar-event fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="mt-1 small">
                                            <span class="text-muted" style="font-size: 0.8rem;">{{ __('Daily monitoring') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- INSTALLMENT -->
                            <div class="col-12 col-md-6">
                                <div class="card {{ $uiStyle === 'milenial' ? 'm-glass-container' : 'border-0 shadow-sm' }} h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ __('Next Installment') }}</h6>
                                                <h4 class="mb-2 fw-bold text-primary" id="summary-cicilan-besok">{{ $cicilanBesokView }}</h4>
                                            </div>
                                            <div class="icon-shape {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-10' : 'bg-light' }} text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="bi bi-calendar-check fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="mt-1 small">
                                            <span class="text-muted" style="font-size: 0.8rem;">{{ __('Repayment goal') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">
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

            <div class="card card-dashboard border-0 shadow-sm networth-card {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }}; cursor: pointer; transition: transform 0.2s;" data-bs-toggle="modal" data-bs-target="#netWorthModal" title="{{ __('Click to view Net Worth Details') }}" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                     <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                             {{ __('Net Worth') }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Your solvency and wealth status.') }}</p>
                    </div>
                    <button type="button" class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-primary' }} btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#netWorthModal">
                        <i class="bi bi-clock-history"></i>
                        <span class="fw-bold" style="font-size: 0.75rem;">{{ __('History') }}</span>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <h2 class="fw-bold mb-0 {{ $netWorthFormatted >= 1 ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.5rem; letter-spacing: -1px;">{{ $netWorthFormatted }}</h2>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: {{ $uiStyle === 'milenial' ? '16px' : '12px' }}; border-radius: 20px; background-color: #f0f2f5;">
                        @php
                            $totalVal = ($totalAset + $totalDanaDarurat) + $totalHutang;
                            $assetPct = $totalVal > 0 ? (($totalAset + $totalDanaDarurat) / $totalVal) * 100 : 0;
                            $debtPct = $totalVal > 0 ? ($totalHutang / $totalVal) * 100 : 0;
                        @endphp
                        <div class="progress-bar {{ $uiStyle === 'milenial' ? '' : 'bg-success' }} shadow-sm"
                             role="progressbar"
                             style="width: {{ $assetPct }}%; border-radius: 20px 0 0 20px; background: {{ $uiStyle === 'milenial' ? 'var(--m-secondary-gradient)' : '' }};"
                             aria-valuenow="{{ $assetPct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                        <div class="progress-bar {{ $uiStyle === 'milenial' ? '' : 'bg-danger' }} shadow-sm"
                             role="progressbar"
                             style="width: {{ $debtPct }}%; border-radius: 0 20px 20px 0; background: {{ $uiStyle === 'milenial' ? 'var(--m-danger-gradient)' : '' }};"
                             aria-valuenow="{{ $debtPct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'bg-light' }} rounded-4 border-0 shadow-none h-100">
                                <p class="text-muted small mb-1 fw-bold text-uppercase ls-1 opacity-75" style="font-size: 0.65rem;">{{ __('Wealth') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalAset + $totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 {{ $uiStyle === 'milenial' ? 'bg-danger bg-opacity-5' : 'bg-light' }} rounded-4 border-0 shadow-none h-100">
                                <p class="text-muted small mb-1 fw-bold text-uppercase ls-1 opacity-75" style="font-size: 0.65rem;">{{ __('Debt') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        @if($netWorth >= 1)
                        <div class="alert {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success shadow-sm' }} border-0 py-2 mb-0" style="border-radius: 12px;">
                            <i class="bi bi-shield-check-fill me-2"></i>
                            <span class="small fw-bold">{{ __('Your assets comfortably cover your debts.') }}</span>
                        </div>
                        @else
                        <div class="alert {{ $uiStyle === 'milenial' ? 'bg-warning bg-opacity-10 text-warning' : 'alert-warning shadow-sm' }} border-0 py-2 mb-0" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span class="small fw-bold">{{ __('Your debt level is higher than your assets.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }}; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Emergency Fund Progress') }}</h5>
                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Track your safety net status.') }}</p>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <h2 class="fw-bold mb-0 text-primary {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.5rem; letter-spacing: -1px;">{{ $persentaseDanaDarurat }}%</h2>
                            <p class="text-muted small fw-bold text-uppercase mb-0 opacity-75" style="letter-spacing: 1px;">{{ __('Achieved') }}</p>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: {{ $uiStyle === 'milenial' ? '16px' : '12px' }}; border-radius: 20px; background-color: #f0f2f5;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated shadow-sm"
                             role="progressbar"
                             style="width: {{ $persentaseDanaDarurat }}%; border-radius: 20px; background: {{ $uiStyle === 'milenial' ? 'var(--m-primary-gradient)' : '' }};"
                             aria-valuenow="{{ $persentaseDanaDarurat }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'bg-light' }} rounded-4 border-0 shadow-none h-100">
                                <p class="text-muted small mb-1 fw-bold text-uppercase ls-1 opacity-75" style="font-size: 0.65rem;">{{ __('Actual') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border-0 shadow-none h-100">
                                <p class="text-muted small mb-1 fw-bold text-uppercase ls-1 opacity-75" style="font-size: 0.65rem;">{{ __('Target') }}</p>
                                <h6 class="fw-bold mb-0 text-dark">Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>

                    @if($persentaseDanaDarurat >= 100)
                    <div class="alert {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success shadow-sm' }} border-0 mt-4 mb-0 py-2 text-center" style="border-radius: 12px;">
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


            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Expense Bar') }}</h5>
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
                    <div class="p-3 border-top {{ $uiStyle === 'milenial' ? 'bg-transparent' : 'bg-light' }} rounded-bottom">
                        <div class="small text-muted">
                            {{ __('Total Expenditures') }}:
                            <strong id="totalPengeluaranValue" class="{{ $uiStyle === 'milenial' ? 'text-primary fw-bold' : '' }}">Rp {{ number_format((float)$totalPengeluaranBulan,0,',','.') }}</strong>
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
            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __("Today's Transactions") }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">
                            {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table {{ $uiStyle === 'milenial' ? 'table-borderless' : 'table-sm' }} align-middle mb-0">
                            @if($uiStyle !== 'milenial')
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Time') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Explanation') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            @endif
                            <tbody>
                                @forelse ($transaksiHariIni as $row)
                                <tr class="{{ $uiStyle === 'milenial' ? 'm-list-item' : '' }}">
                                    <td class="{{ $uiStyle === 'milenial' ? 'ps-4' : '' }} border-0">
                                        <div class="fw-bold fs-6 {{ $uiStyle === 'milenial' ? 'text-dark' : '' }}">{{ \Carbon\Carbon::parse($row->waktu)->format('H:i') }}</div>
                                        @if($uiStyle === 'milenial')
                                        <div class="small text-muted">{{ $row->kategori }}</div>
                                        @endif
                                    </td>
                                    <td class="border-0">
                                        <span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} {{ $row->jenis === 'pemasukan' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ ucfirst($row->jenis) }}
                                        </span>
                                    </td>
                                    <td class="border-0">
                                        <div class="fw-semibold text-dark">{!! $row->keterangan ?? '-' !!}</div>
                                        @if($uiStyle !== 'milenial')
                                        <div class="small text-muted">{{ $row->kategori }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold {{ $row->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'pe-4' : '' }} border-0" style="{{ $uiStyle === 'milenial' ? 'font-size: 1.1rem;' : '' }}">
                                        {{ $row->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format((float)$row->nominal,0,',','.') }}
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
                    <div class="p-4 border-top {{ $uiStyle === 'milenial' ? 'bg-transparent' : 'bg-light' }} rounded-bottom d-flex justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-arrow-down-left"></i>
                            </div>
                            <span class="text-success small fw-bold">
                                {{ __('Income') }}: <strong>Rp {{ number_format((float)$totalMasukHariIni,0,',','.') }}</strong>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-arrow-up-right"></i>
                            </div>
                            <span class="text-danger small fw-bold">
                                {{ __('Expenses') }}: <strong>Rp {{ number_format((float)$totalKeluarHariIni,0,',','.') }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Budget Performance') }}</h5>
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

        @include('modal.dashboard.growth_detail')
        @include('modal.dashboard.detail')
        @include('modal.dashboard.rasio')
        @include('modal.dashboard.notes')
        @include('modal.dashboard.networth')
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