@extends('layouts.main')

@section('title', __('Budget Monitoring Detail'))

@push('css')
<link href="{{ asset('css/kalkulator.css') }}?v={{ filemtime(public_path('css/kalkulator.css')) }}" rel="stylesheet">
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

    /* ─── Burn Rate Card ───────────────────────────────────────── */
    .burn-rate-card {
        border-radius: 0;
        border: none;
        overflow: hidden;
    }
    .burn-rate-card.alert-danger-card {
        background: linear-gradient(135deg, #fff5f5 0%, #fff 60%);
        border-left: 4px solid #ef4444 !important;
    }
    .burn-rate-card.alert-safe-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 60%);
        border-left: 4px solid #22c55e !important;
    }
    [data-bs-theme="dark"] .burn-rate-card.alert-danger-card {
        background: linear-gradient(135deg, rgba(239,68,68,.08) 0%, transparent 60%);
        border-left: 4px solid #ef4444 !important;
    }
    [data-bs-theme="dark"] .burn-rate-card.alert-safe-card {
        background: linear-gradient(135deg, rgba(34,197,94,.08) 0%, transparent 60%);
        border-left: 4px solid #22c55e !important;
    }
    .burn-stat-box {
        padding: 0.75rem 1rem;
        background: rgba(255,255,255,0.6);
        border-radius: 0;
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform .15s;
    }
    [data-bs-theme="dark"] .burn-stat-box {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
    }
    .burn-stat-box:hover { transform: translateY(-1px); }
    .burn-gauge-wrap { position: relative; }
    .burn-gauge-bar {
        height: 10px;
        border-radius: 999px;
        background: #e9ecef;
        overflow: hidden;
    }
    [data-bs-theme="dark"] .burn-gauge-bar { background: rgba(255,255,255,.1); }
    .burn-gauge-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .6s ease;
        position: relative;
    }
    .burn-gauge-fill::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0;
        width: 20px;
        background: linear-gradient(to right, transparent, rgba(255,255,255,.35));
    }
    .burn-ideal-marker {
        position: absolute;
        top: -4px;
        width: 2px;
        height: 18px;
        background: #64748b;
        border-radius: 1px;
    }
    .burn-pulse-icon {
        animation: burn-pulse 1.8s ease-in-out infinite;
    }
    @keyframes burn-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .65; transform: scale(1.15); }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Budget Monitoring Detail') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kalkulator.index') }}">{{ __('Budget Monitoring') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Detail') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Overview Card -->
        <div class="col-lg-12">
            <div class="card-dashboard mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0 text-dark">{{ __('Budget Information') }}</h5>
                        <a href="{{ route('kalkulator.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-2" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold" style="width: 140px;">{{ __('Budget Name') }}</td>
                                        <td class="fw-medium">: {{ $HasilProsesAnggaran->nama_anggaran }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold">{{ __('Period') }}</td>
                                        <td class="fw-medium">: 
                                            {{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_mulai)->locale(app()->getLocale())->isoFormat('D MMM Y') }} 
                                            {{ __('to') }} 
                                            {{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_selesai)->locale(app()->getLocale())->isoFormat('D MMM Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold">{{ __('Expense Type') }}</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($namaPengeluaran as $index => $nama)
                                                    @if($index < 5)
                                                        <li><i class="bi bi-dot text-secondary"></i> {{ $nama }}</li>
                                                    @endif
                                                @endforeach
                                                @if(count($namaPengeluaran) > 5)
                                                    <li class="text-muted fst-italic ms-3 small">+{{ count($namaPengeluaran) - 5 }} {{ __('others') }}</li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">{{ __('Financial Summary') }}</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ __('Budget Amount') }}:</span>
                                        <span class="fw-bold">Rp {{ number_format($HasilProsesAnggaran->nominal_anggaran, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ __('Used') }}:</span>
                                        <span class="fw-bold text-danger">Rp {{ number_format($HasilProsesAnggaran->anggaran_yang_digunakan, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ __('Remaining Budget') }}:</span>
                                        <div class="text-end">
                                            @php $sisa = $HasilProsesAnggaran->remaining_budget; @endphp
                                            <h5 class="mb-0 fw-bold {{ $sisa < 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($sisa, 0, ',', '.') }}
                                            </h5>
                                            @if ($sisa < 0)
                                                <span class="badge bg-danger-subtle text-danger small">{{ __('Over Budget') }}</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success small">{{ __('Within Budget') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($burnRate)
        {{-- ─── Burn Rate Alert Card ──────────────────────────────────────── --}}
        <div class="col-lg-12">
            <div class="card-dashboard mb-4 burn-rate-card {{ $burnRate['alert_triggered'] ? 'alert-danger-card' : 'alert-safe-card' }}">
                <div class="card-body p-4">

                    {{-- Header --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded-circle {{ $burnRate['alert_triggered'] ? 'bg-danger-subtle' : 'bg-success-subtle' }}" style="width:48px;height:48px;flex-shrink:0">
                            @if($burnRate['alert_triggered'])
                                <i class="bi bi-fire text-danger fs-4 burn-pulse-icon"></i>
                            @else
                                <i class="bi bi-shield-check text-success fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 {{ $burnRate['alert_triggered'] ? 'text-danger' : 'text-success' }}">
                                {{ __('Burn Rate — Spending Pace') }}
                            </h5>
                            <p class="text-muted small mb-0">
                                @if($burnRate['alert_triggered'])
                                    {{ __('Warning! You are spending your budget too fast.') }}
                                @else
                                    {{ __('Your spending pace is safe and on track.') }}
                                @endif
                            </p>
                        </div>
                        <div class="ms-auto text-end d-none d-md-block">
                            <span class="badge {{ $burnRate['alert_triggered'] ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2" style="font-size:.8rem;">
                                {{ number_format($burnRate['spent_percentage'], 1) }}% {{ __('used') }}
                            </span>
                            <div class="text-muted small mt-1">{{ __('after') }} {{ $burnRate['days_elapsed'] }} {{ __('days elapsed') }}</div>
                        </div>
                    </div>

                    {{-- Progress Gauge --}}
                    @php
                        $fillPct   = min(100, $burnRate['spent_percentage']);
                        $idealPct  = min(99, $burnRate['ideal_percentage']);
                        $fillColor = $burnRate['alert_triggered']
                            ? 'linear-gradient(90deg,#f97316,#ef4444)'
                            : 'linear-gradient(90deg,#22c55e,#16a34a)';
                    @endphp
                    <div class="burn-gauge-wrap mb-1">
                        <div class="burn-gauge-bar">
                            <div class="burn-gauge-fill" style="width:{{ $fillPct }}%; background:{{ $fillColor }}"></div>
                        </div>
                        <div class="burn-ideal-marker" style="left:{{ $idealPct }}%" title="{{ __('Ideal pace') }}"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-4">
                        <span>{{ __('Day') }} {{ $burnRate['days_elapsed'] }}</span>
                        <span class="opacity-75"><i class="bi bi-bar-chart-line me-1"></i>{{ __('Ideal:') }} {{ number_format($burnRate['ideal_percentage'], 1) }}%</span>
                        <span>{{ __('Day') }} {{ $burnRate['total_days'] }} {{ __('(end)') }}</span>
                    </div>

                    {{-- Stat Boxes --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="burn-stat-box">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem;">{{ __('Spend / Day') }}</div>
                                <div class="fw-bold fs-6 {{ $burnRate['alert_triggered'] ? 'text-danger' : 'text-success' }}">Rp {{ number_format($burnRate['daily_rate'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="burn-stat-box">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem;">{{ __('Days Remaining') }}</div>
                                <div class="fw-bold fs-6">{{ $burnRate['days_remaining'] }} {{ __('days') }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="burn-stat-box">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem;">{{ __('Projected Total Spend') }}</div>
                                <div class="fw-bold fs-6 {{ $burnRate['projected_total'] > $burnRate['total_budget'] ? 'text-danger' : 'text-success' }}">Rp {{ number_format($burnRate['projected_total'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="burn-stat-box">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem;">{{ __('Budget Runs Out In') }}</div>
                                <div class="fw-bold fs-6 {{ $burnRate['alert_triggered'] ? 'text-danger' : 'text-success' }}">
                                    @if($burnRate['days_until_out'] !== null && $burnRate['days_until_out'] >= 0)
                                        ± {{ $burnRate['days_until_out'] }} {{ __('days') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert / Safe Message --}}
                    @if($burnRate['alert_triggered'])
                    <div class="alert border-0 mb-0" style="background:rgba(239,68,68,.08);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-danger">{{ __('Your spending rate is too fast!') }}</strong>
                                <p class="mb-0 small text-muted mt-1">
                                    {{ __('After') }} <strong>{{ $burnRate['days_elapsed'] }}</strong> {{ __('days, you have already spent') }}
                                    <strong>{{ number_format($burnRate['spent_percentage'], 1) }}%</strong> {{ __('of your budget.') }}
                                    {{ __('At this pace, your budget will run out in approximately') }}
                                    <strong class="text-danger">
                                        @if($burnRate['days_until_out'] !== null && $burnRate['days_until_out'] >= 0)
                                            {{ $burnRate['days_until_out'] }} {{ __('more days') }}
                                        @else
                                            {{ __('very soon') }}
                                        @endif
                                    </strong>.
                                    {{ __('Consider reducing your daily spending.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert border-0 mb-0" style="background:rgba(34,197,94,.08);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-success">{{ __('Your spending is under control.') }}</strong>
                                <p class="mb-0 small text-muted mt-1">
                                    {{ __('At your current pace, your budget is projected to') }}
                                    <strong class="text-success">{{ __('last through the end of the period') }}</strong>.
                                    {{ __('Keep it up!') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif

        <!-- Detail Table -->
        <div class="col-lg-12">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">{{ __('Related Transaction Details') }}</h5>
                        <div class="search-bar" style="min-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="detailSearch" class="form-control bg-light border-start-0 rounded-end-pill shadow-none" style="font-size: 0.8rem;" placeholder="{{ __('Search transactions...') }}">
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="kalkulator-id" value="{{ $HasilProsesAnggaran->hash }}">

                    <div id="detail-table-container">
                        @include('kalkulator._transaction_table', ['transaksi' => $transaksi])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush