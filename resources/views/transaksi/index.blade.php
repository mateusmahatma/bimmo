@extends('layouts.main')

@section('title', __('Transactions'))

{{-- ============================================================
     CSS
     ============================================================ --}}
@push('css')
<link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/tom-select.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('css/transaksi.css') }}?v={{ filemtime(public_path('css/transaksi.css')) }}">
@endpush

{{-- ============================================================
     MAIN CONTENT
     ============================================================ --}}
@section('container')

{{-- Page Title --}}
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Transactions') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="breadcrumb-item active">{{ __('Transactions') }}</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        {{-- ================================================
                 SUMMARY CARDS
                 ================================================ --}}

        {{-- Total Income --}}
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="card-icon transaksi-card-icon transaksi-card-icon--success
                                        rounded-circle d-flex align-items-center justify-content-center
                                        bg-success-light text-success me-3">
                            <i class="bi bi-arrow-down-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">
                                {{ __('Total Income') }}
                            </h6>
                            <h4 class="mb-0 fw-bold text-success">
                                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#incomeDetailsModal"
                            class="small text-decoration-none text-success fw-bold">
                            {{ __('View Details') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Expense --}}
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="card-icon transaksi-card-icon transaksi-card-icon--danger
                                        rounded-circle d-flex align-items-center justify-content-center
                                        bg-danger-light text-danger me-3">
                            <i class="bi bi-arrow-up-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">
                                {{ __('Total Expense') }}
                            </h6>
                            <h4 class="mb-0 fw-bold text-danger">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#expenseDetailsModal"
                            class="small text-decoration-none text-danger fw-bold">
                            {{ __('View Details') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Balance --}}
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon transaksi-card-icon transaksi-card-icon--primary
                                        rounded-circle d-flex align-items-center justify-content-center
                                        bg-primary-light text-primary me-3">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">
                                {{ __('Net Balance') }}
                            </h6>
                            <h4 class="mb-0 fw-bold {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($netIncome, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================
                 PERIOD EXPENSE
                 ================================================ --}}
        <div class="col-12 mb-3">
            <div class="card card-dashboard">
                <div class="card-body py-3">
                    <div class="d-flex flex-column align-items-start text-start">
                        <h6 class="fw-bold text-dark text-uppercase mb-1 transaksi-period-heading">
                            {{ __('Period Expense') }}
                        </h6>
                        <span class="badge bg-light text-secondary border fw-normal  px-2 py-1 mb-2
                                         transaksi-badge-date-range"
                            id="avg-date-range">
                            <i class="bi bi-calendar3 me-1"></i> {{ $dateRange }}
                        </span>
                        <div class="d-flex align-items-center">
                            <div class="text-start">
                                <h4 class="mb-0 fw-bold text-dark transaksi-avg-daily-value" id="avg-daily">
                                    Rp {{ number_format($avgDailyPengeluaran, 0, ',', '.') }}
                                </h4>
                                <span class="text-muted small text-uppercase transaksi-daily-avg-label">
                                    {{ __('Daily Average') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================
                 MAIN CARD
                 ================================================ --}}
        <div class="col-12">
            <div class="card card-dashboard border-0 shadow-sm transaksi-main-card">

                {{-- Card Header --}}
                <div class="card-header bg-white border-bottom py-3
                                d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark transaksi-section-title">
                            {{ __('Transaction Dates') }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1 transaksi-section-desc">
                            {{ __('Click a date to view and add transactions.') }}
                        </p>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ============================================
                             TOOLBAR
                             ============================================ --}}
                    <form action="{{ route('transaksi.index') }}" method="GET"
                        class="transaksi-toolbar mb-4 pt-3">

                        {{-- Row 1: Search + Date Range + Desktop Actions --}}
                        <div class="toolbar-row toolbar-row--primary">

                            {{-- Search --}}
                            <div class="toolbar-search">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                        id="searchTransaksi"
                                        class="form-control bg-light border-start-0 rounded-end-pill
                                                      transaksi-search-input"
                                        placeholder="{{ __('Search transactions...') }}">
                                </div>
                            </div>

                            {{-- Date Range Picker --}}
                            <div class="toolbar-daterange">
                                <input type="hidden" name="start_date" id="transaksi_start_date"
                                    value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" id="transaksi_end_date"
                                    value="{{ request('end_date') }}">
                                <div id="transaksiDaterange"
                                    class="form-control form-control-sm transaksi-daterange
                                                d-flex align-items-center gap-2 "
                                    role="button"
                                    tabindex="0"
                                    title="{{ __('Date Range') }}">
                                    <i class="bi bi-calendar3 text-muted flex-shrink-0"></i>
                                    <span class="transaksi-daterange-label flex-grow-1 text-truncate small">
                                        {{ $dateRange }}
                                    </span>
                                    <i class="bi bi-chevron-down text-muted small flex-shrink-0"></i>
                                </div>
                            </div>

                            {{-- Desktop Action Buttons --}}
                            <div class="toolbar-actions d-none d-lg-flex align-items-center gap-2">

                                {{-- Filter --}}
                                <button class="btn btn-outline-secondary btn-sm  px-3
                                                   d-flex align-items-center gap-1"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#filterCollapse">
                                    <i class="bi bi-sliders"></i>
                                    <span>{{ __('Filter') }}</span>
                                </button>

                                {{-- Export --}}
                                <div class="dropdown">
                                    <button class="btn btn-outline-success btn-sm dropdown-toggle  px-3
                                                       d-flex align-items-center gap-1"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-download"></i>
                                        <span>{{ __('Export') }}</span>
                                    </button>
                                    <ul class="dropdown-menu shadow border-0 rounded-3">
                                        <li>
                                            <a class="dropdown-item no-loader"
                                                id="btnExportExcel"
                                                href="{{ route('transaksi.export.excel', request()->all()) }}">
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item no-loader"
                                                id="btnExportPdf"
                                                href="{{ route('transaksi.export.pdf', request()->all()) }}">
                                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                id="btnExportEmail"
                                                href="#"
                                                data-bs-toggle="modal"
                                                data-bs-target="#emailExportModal">
                                                <i class="bi bi-envelope me-2 text-primary"></i>
                                                {{ __('Export to Email') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Import --}}
                                <button type="button"
                                    class="btn btn-success btn-sm  px-3
                                                   d-flex align-items-center gap-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#importExcelModal">
                                    <i class="bi bi-upload"></i>
                                    <span>{{ __('Import') }}</span>
                                </button>

                                {{-- Download Template --}}
                                <a href="{{ route('transaksi.download.template') }}"
                                    class="btn btn-outline-secondary btn-sm rounded-circle no-loader
                                              transaksi-btn-template"
                                    title="{{ __('Download Template') }}">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                </a>

                                <div class="vr opacity-25 mx-1" style="height: 28px"></div>

                                {{-- Pick Date --}}
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm  px-3
                                                   d-flex align-items-center gap-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#openDateModal">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>{{ __('Pilih Tanggal') }}</span>
                                </button>

                                {{-- Add Transaction --}}
                                <a href="{{ route('transaksi.create') }}"
                                    class="btn btn-primary btn-sm  px-3
                                              d-flex align-items-center gap-1 btn-add-desktop">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>{{ __('Add Transaction') }}</span>
                                </a>

                            </div>{{-- /toolbar-actions --}}
                        </div>{{-- /toolbar-row--primary --}}

                        {{-- Row 2: Quick Date Shortcuts + Mobile Actions --}}
                        <div class="toolbar-row toolbar-row--shortcuts">

                            {{-- Quick Date Shortcuts --}}
                            <div class="toolbar-quick-dates">
                                @php
                                $baseQuery = request()->except(['start', 'end', 'start_date', 'end_date']);
                                $shortcuts = [
                                __('Today') => ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')],
                                __('Yesterday') => ['start_date' => date('Y-m-d', strtotime('-1 day')), 'end_date' => date('Y-m-d', strtotime('-1 day'))],
                                __('This Month') => ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')],
                                __('Last Month') => ['start_date' => date('Y-m-01', strtotime('-1 month')),'end_date' => date('Y-m-t', strtotime('-1 month'))],
                                __('This Year') => ['start_date' => date('Y-01-01'), 'end_date' => date('Y-12-31')],
                                __('Last Year') => ['start_date' => date('Y-01-01', strtotime('-1 year')),'end_date' => date('Y-12-31', strtotime('-1 year'))],
                                ];
                                @endphp

                                @foreach ($shortcuts as $label => $range)
                                <a href="{{ route('transaksi.index', array_merge($baseQuery, $range)) }}"
                                    class="btn btn-outline-secondary btn-xs "
                                    title="{{ $label }}">
                                    {{ $label }}
                                </a>
                                @endforeach
                            </div>

                            {{-- Mobile Action Buttons --}}
                            <div class="toolbar-mobile-actions d-flex d-lg-none align-items-center gap-2 ms-auto">

                                {{-- Filter --}}
                                <button class="btn btn-outline-secondary btn-xs
                                                   d-flex align-items-center gap-1"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#filterCollapse">
                                    <i class="bi bi-sliders"></i>
                                    <span class="d-none d-sm-inline">{{ __('Filter') }}</span>
                                </button>

                                {{-- Export --}}
                                <div class="dropdown">
                                    <button class="btn btn-outline-success btn-xs  dropdown-toggle
                                                       d-flex align-items-center gap-1"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-download"></i>
                                        <span class="d-none d-sm-inline">{{ __('Export') }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        <li>
                                            <a class="dropdown-item no-loader"
                                                id="btnExportExcelMobile"
                                                href="{{ route('transaksi.export.excel', request()->all()) }}">
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item no-loader"
                                                id="btnExportPdfMobile"
                                                href="{{ route('transaksi.export.pdf', request()->all()) }}">
                                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="#"
                                                data-bs-toggle="modal"
                                                data-bs-target="#emailExportModal">
                                                <i class="bi bi-envelope me-2 text-primary"></i>
                                                {{ __('Export to Email') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Import --}}
                                <button type="button"
                                    class="btn btn-success btn-xs  d-flex align-items-center gap-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#importExcelModal">
                                    <i class="bi bi-upload"></i>
                                    <span class="d-none d-sm-inline">{{ __('Import') }}</span>
                                </button>

                                {{-- Pick Date --}}
                                <button type="button"
                                    class="btn btn-outline-primary btn-xs rounded-circle"
                                    data-bs-toggle="modal"
                                    data-bs-target="#openDateModal"
                                    title="{{ __('Pilih Tanggal') }}">
                                    <i class="bi bi-calendar-event"></i>
                                </button>

                            </div>{{-- /toolbar-mobile-actions --}}
                        </div>{{-- /toolbar-row--shortcuts --}}

                    </form>{{-- /toolbar form --}}

                    {{-- ============================================
                             FILTER COLLAPSE
                             ============================================ --}}
                    <div class="collapse mb-4 {{ request()->hasAny(['pemasukan', 'pengeluaran']) ? 'show' : '' }}"
                        id="filterCollapse">
                        <div class="card card-body bg-light border-0 p-4 rounded-4">
                            <form method="GET" class="row g-3">

                                {{-- Preserve date filters --}}
                                <input type="hidden" name="start_date" id="transaksi_filter_form_start"
                                    value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" id="transaksi_filter_form_end"
                                    value="{{ request('end_date') }}">

                                {{-- Income Category --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        {{ __('Income Category') }}
                                    </label>
                                    <select name="pemasukan[]"
                                        id="filter-pemasukan"
                                        class="form-select"
                                        multiple
                                        placeholder="{{ __('Select Income Categories...') }}"
                                        autocomplete="off">
                                        @foreach ($listPemasukan as $item)
                                        <option value="{{ $item->id }}"
                                            @selected(in_array($item->id, (array) request('pemasukan', [])))>
                                            {{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Expense Category --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        {{ __('Expense Category') }}
                                    </label>
                                    <select name="pengeluaran[]"
                                        id="filter-pengeluaran"
                                        class="form-select"
                                        multiple
                                        placeholder="{{ __('Select Expense Categories...') }}"
                                        autocomplete="off">
                                        @foreach ($listPengeluaran as $item)
                                        <option value="{{ $item->id }}"
                                            @selected(in_array($item->id, (array) request('pengeluaran', [])))>
                                            {{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Actions --}}
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('transaksi.index') }}"
                                        class="btn btn-sm btn-link text-decoration-none text-muted"
                                        id="btnResetFilter">
                                        {{ __('Reset Filter') }}
                                    </a>
                                    <button type="button"
                                        id="btnApplyFilter"
                                        class="btn btn-primary btn-sm  px-4">
                                        {{ __('Apply Filter') }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>{{-- /filterCollapse --}}

                    {{-- ============================================
                             DATE CARDS
                             ============================================ --}}
                    <div id="transaction-table-container">
                        @include('transaksi._date_cards', ['groupedByDate' => $groupedByDate])
                    </div>

                </div>{{-- /card-body --}}
            </div>{{-- /card --}}
        </div>{{-- /col-12 --}}

    </div>{{-- /row --}}
</section>

{{-- FAB Mobile --}}
<a href="{{ route('transaksi.create') }}"
    class="btn btn-primary fab-add shadow-lg"
    title="{{ __('Add Transaction') }}">
    <i class="bi bi-plus-lg fs-2"></i>
</a>

{{-- Modals --}}
<x-transaksi.modals
    :summary-pemasukan="$summaryPemasukan"
    :summary-pengeluaran="$summaryPengeluaran"
    :total-pemasukan="$totalPemasukan"
    :total-pengeluaran="$totalPengeluaran" />

@endsection

{{-- ============================================================
     SCRIPTS
     ============================================================ --}}
@push('scripts')
{{-- Page config passed to JS --}}
<script id="transaksi-page-config" type="application/json">
    @json($transaksiPageConfig)
</script>

{{-- Vendor --}}
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('js/vendor/moment.min.js') }}"></script>
<script src="{{ asset('js/vendor/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/vendor/tom-select.complete.min.js') }}"></script>

{{-- App --}}
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>

@endpush
