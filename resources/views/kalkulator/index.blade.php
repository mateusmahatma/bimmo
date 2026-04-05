@extends('layouts.main')

@section('title', __('Budget Calculator'))

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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

    .bi-spin {
        animation: spin 1s infinite linear;
        display: inline-block;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Budget Monitoring') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Budget Monitoring') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Input Form -->
        <div class="col-lg-12 mb-4">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Calculate New Budget') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Enter income and period to calculate the Budget.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> {{ __('Instructions') }}
                        </button>
                    </div>
                </div>

                <div class="collapse" id="instructionsCollapse">
                    <div class="card-body bg-light border-bottom">
                        <div class="alert alert-info border-0 bg-white shadow-sm mb-0 text-dark">
                            <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2"></i>{{ __('Steps:') }}</h6>
                            <ol class="mb-0 ps-3 small">
                                <li>{!! __('Select the <strong>Income Types</strong> to include in the calculation.') !!}</li>
                                <li>{!! __('Select the <strong>Date Range</strong> for the Budget period.') !!}</li>
                                <li>{!! __('Click the <strong>Process Budget</strong> button.') !!}</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="post" action="{{ route('kalkulator.store') }}" id="formKalkulator" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <!-- Income Section -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Income Information') }}</label>
                                <div class="mb-3">
                                    <label for="id_pemasukan" class="form-label">{{ __('Income Types') }}</label>
                                    <select name="id_pemasukan[]" id="id_pemasukan" class="form-control" multiple placeholder="{{ __('Select income types...') }}" required>
                                        @foreach ($pemasukans as $pemasukan)
                                        <option value="{{ $pemasukan->id }}" {{ in_array($pemasukan->id, (array) old('id_pemasukan', [])) ? 'selected' : '' }}>
                                            {{ $pemasukan->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text small text-muted">{{ __('Select one or more income categories from your transactions.') }}</div>
                                </div>
                            </div>

                            <!-- Period Section -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Budget Period') }}</label>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Date Range') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                        <div id="daterange" class="form-control" style="cursor: pointer;">
                                            <span></span>
                                        </div>
                                        <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                                        <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">
                                    </div>
                                    <div class="form-text small">{{ __('Select start and end dates for budget calculation.') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light rounded-pill shadow-sm" id="btnReset" style="padding: 2px 15px; font-size: 0.8rem;">{{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary rounded-pill shadow-sm" id="btnProses" style="padding: 2px 15px; font-size: 0.8rem;">
                                <i class="bi bi-gear me-1"></i> {{ __('Process Budget') }}
                                <span id="btnProsesSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Table -->
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                         <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Budget Process History') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('List of your budget calculation history.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm d-none rounded-pill" id="btnBulkSync" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('Sync Selected') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm d-none rounded-pill" id="btnBulkDelete" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }} (<span id="countSelected">0</span>)
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- SEARCH BAR -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                        <div class="search-bar" style="min-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="historySearch" class="form-control bg-light border-start-0 rounded-end-pill shadow-none" style="font-size: 0.8rem;" placeholder="{{ __('Search history...') }}">
                            </div>
                        </div>
                    </div>

                    <div id="history-table-container">
                        @include('kalkulator._table_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush