@extends('layouts.main')

@section('title', __('Transactions'))

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css">
<style>
    /* Header Enhancements */
    /* TomSelect Refining */
    .ts-control {
        border-radius: 0.5rem !important;
        padding: 0.5rem 0.75rem !important;
        border-color: #dee2e6 !important;
        box-shadow: none !important;
    }
    [data-bs-theme="dark"] .ts-control {
        background-color: #2b3035 !important;
        border-color: #495057 !important;
        color: #e0e0e0 !important;
    }
    .ts-dropdown {
        border-radius: 0.5rem !important;
        margin-top: 5px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    [data-bs-theme="dark"] .ts-dropdown {
        background-color: #2b3035 !important;
        border-color: #495057 !important;
    }
    [data-bs-theme="dark"] .ts-dropdown .option {
        color: #e0e0e0 !important;
    }
    [data-bs-theme="dark"] .ts-dropdown .active {
        background-color: #343a40 !important;
    }
    /* PWA & Premium Enhancements (White Theme) */
    .card-dashboard {
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        background: #ffffff;
        color: #2d3436;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: none !important;
    }
    
    .card-icon {
        transition: none !important;
    }

    .fab-add {
        position: fixed;
        bottom: 2rem;
        right: 1.5rem;
        z-index: 1040;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: none; /* Desktop hidden */
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(13, 110, 253, 0.4);
        transition: none !important;
    }

    @media (max-width: 767.98px) {
        .fab-add {
            display: flex;
        }
        .btn-add-desktop {
            display: none;
        }
        
        #transaksiTable, 
        #transaksiTable thead, 
        #transaksiTable tbody, 
        #transaksiTable th, 
        #transaksiTable td, 
        #transaksiTable tr { 
            display: block; 
        }

        /* Hide table headers */
        #transaksiTable thead tr { 
            position: absolute;
            top: -9999px; left: -9999px;
        }

        #transaksiTable tr {
            border: 0;
            margin-bottom: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        #transaksiTable td {
            border: none;
            border-bottom: 1px solid #f8f9fa;
            position: relative;
            padding-left: 45%; 
            padding-top: 1rem;
            padding-bottom: 1rem;
            text-align: right;
            white-space: normal;
            min-height: 3.5rem;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
        }
        
        #transaksiTable td .badge {
            white-space: normal !important;
            text-align: right;
            max-width: 100%;
        }

        #transaksiTable td:last-child {
            border-bottom: 0;
        }

        #transaksiTable td:before { 
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            content: attr(data-label);
        }

        #transaksiTable td:last-child {
             padding-left: 10px;
             text-align: center;
             display: none !important; /* Action hidden by default on mobile detail */
             justify-content: center !important;
             align-items: center !important;
             gap: 10px;
             border-bottom: 0;
             margin-top: 5px;
        }

        /* NEW COLLAPSE LOGIC */
        .mobile-collapsible-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-detail {
            display: none !important;
        }

        .mobile-collapsible-row.expanded .mobile-detail {
            display: flex !important;
        }

        .mobile-collapsible-row.expanded {
            border: 1px solid rgba(13, 110, 253, 0.2) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .mobile-collapsible-row.expanded .mobile-toggle-icon i {
            transform: rotate(180deg);
        }

        .mobile-toggle-icon i {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        /* Ensure amount and category look good in header */
        #transaksiTable tr.mobile-collapsible-row td.mobile-header {
            border-bottom: 0;
        }
        
        #transaksiTable tr.mobile-collapsible-row.expanded td.mobile-header {
            border-bottom: 1px solid #f8f9fa;
        }

        /* Hide ID/Extra on mobile */
        #transaksiTable td:nth-of-type(2) {
            display: none;
        }
    }

    [data-bs-theme="dark"] .card-dashboard {
        background-color: #1e1e1e;
        border-color: rgba(255,255,255,0.05);
    }
    /* Date Cards */
    .date-card-link:hover .date-card { box-shadow: 0 6px 20px rgba(0,0,0,0.1); border-color: rgba(13,110,253,0.2); }
    .date-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    [data-bs-theme="dark"] .date-card { background: #1a1a1a; border-color: rgba(255,255,255,0.06); }
    [data-bs-theme="dark"] .date-card-link:hover .date-card { border-color: rgba(96,165,250,0.3); }
    .date-badge {
        width: 52px; min-width: 52px;
        background: linear-gradient(135deg, #0d6efd, #0984e3);
        border-radius: 12px;
        color: #fff;
        padding: 6px 4px;
    }
    .date-badge-month { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.85; }
    .date-badge-day   { font-size: 1.35rem; font-weight: 700; line-height: 1.1; }
    .date-badge-dow   { font-size: 0.6rem; opacity: 0.85; }
    .date-card-title  { font-size: 0.92rem; }

    [data-bs-theme="dark"] #transaksiTable tr { background-color: #1a1a1a; border-color: rgba(255,255,255,0.05); }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Transactions') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Transactions') }}</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <!-- SUMMARY CARDS -->
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success me-3" style="width: 48px; height: 48px; background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-arrow-down-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Income') }}</h6>
                            <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#incomeDetailsModal" class="small text-decoration-none text-success fw-bold">
                            {{ __('View Details') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                       <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger me-3" style="width: 48px; height: 48px; background: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-arrow-up-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Expense') }}</h6>
                            <h4 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#expenseDetailsModal" class="small text-decoration-none text-danger fw-bold">
                            {{ __('View Details') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary me-3" style="width: 48px; height: 48px; background: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Net Balance') }}</h6>
                            <h4 class="mb-0 fw-bold {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($netIncome, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Expense Row -->
        <div class="col-12 mb-3">
            <div class="card card-dashboard">
                <div class="card-body py-3">
                    <div class="d-flex flex-column align-items-start text-start">
                        <h6 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ __('Period Expense') }}</h6>
                        <span class="badge bg-light text-secondary border fw-normal rounded-pill px-2 py-1 mb-2" id="avg-date-range" style="font-size: 0.7rem;">
                            <i class="bi bi-calendar3 me-1"></i> {{ $dateRange }}
                        </span>

                        <div class="d-flex align-items-center">
                             <div class="text-start">
                                <h4 class="mb-0 fw-bold text-dark" id="avg-daily" style="font-size: 1.25rem;">Rp {{ number_format($avgDailyPengeluaran, 0, ',', '.') }}</h4>
                                <span class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ __('Daily Average') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="col-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Transaction Dates') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Click a date to view and add transactions.') }}</p>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- TOOLBAR -->
                    <form action="{{ route('transaksi.index') }}" method="GET" class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3 pt-3">
                        
                        <!-- Search & Date Filter Group -->
                        <div class="d-flex gap-2 flex-wrap align-items-center flex-grow-1">
                            <!-- Search (Client-side) -->
                            <div class="search-bar" style="min-width: 200px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="searchTransaksi" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="{{ __('Search transactions...') }}" style="box-shadow: none;">
                                </div>
                            </div>

                            <!-- Date Filters -->
                            <div class="d-flex gap-2 align-items-center ms-lg-2">
                                <input type="date" name="start_date" class="form-control form-control-sm rounded-pill" value="{{ request('start_date') }}" title="Start Date">
                                <span class="text-muted">-</span>
                                <input type="date" name="end_date" class="form-control form-control-sm rounded-pill text-muted" value="{{ request('end_date') }}" title="End Date">
                                <button class="btn btn-light btn-sm rounded-circle border" title="Apply Filter"><i class="bi bi-arrow-right-short"></i></button>
                            </div>
                            
                            <!-- Quick Dates -->
                            <div class="btn-group btn-group-sm ms-lg-2">
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')])) }}" class="btn btn-outline-secondary rounded-start-pill" title="{{ __('Today') }}">{{ __('Today') }}</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-d', strtotime('-1 day')), 'end_date' => date('Y-m-d', strtotime('-1 day'))])) }}" class="btn btn-outline-secondary" title="{{ __('Yesterday') }}">{{ __('Yesterday') }}</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')])) }}" class="btn btn-outline-secondary" title="{{ __('This Month') }}">{{ __('This Month') }}</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01', strtotime('-1 month')), 'end_date' => date('Y-m-t', strtotime('-1 month'))])) }}" class="btn btn-outline-secondary" title="{{ __('Last Month') }}">{{ __('Last Month') }}</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-01-01'), 'end_date' => date('Y-12-31')])) }}" class="btn btn-outline-secondary" title="{{ __('This Year') }}">{{ __('This Year') }}</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-01-01', strtotime('-1 year')), 'end_date' => date('Y-12-31', strtotime('-1 year'))])) }}" class="btn btn-outline-secondary rounded-end-pill" title="{{ __('Last Year') }}">{{ __('Last Year') }}</a>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="bi bi-sliders"></i> {{ __('Filter') }}
                            </button>
                            
                            <div class="dropdown">
                                <button class="btn btn-outline-success btn-sm dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i> {{ __('Export') }}
                                </button>
                                <ul class="dropdown-menu shadow border-0 rounded-3">
                                    <li><a class="dropdown-item no-loader" id="btnExportExcel" href="{{ route('transaksi.export.excel', request()->all()) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel</a></li>
                                    <li><a class="dropdown-item no-loader" id="btnExportPdf" href="{{ route('transaksi.export.pdf', request()->all()) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" id="btnExportEmail" href="#" data-bs-toggle="modal" data-bs-target="#emailExportModal"><i class="bi bi-envelope me-2 text-primary"></i> {{ __('Export to Email') }}</a></li>
                                </ul>
                            </div>

                             <button type="button" class="btn btn-success btn-sm rounded-pill px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                                <i class="bi bi-upload"></i> {{ __('Import') }}
                            </button>

                             <a href="{{ route('transaksi.download.template') }}" class="btn btn-outline-secondary btn-sm rounded-circle no-loader" title="Download Template" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                            </a>

                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#openDateModal">
                                <i class="bi bi-calendar-event"></i> {{ __('Pilih Tanggal') }}
                            </button>

                            <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2 btn-add-desktop ms-lg-2">
                                <i class="bi bi-plus-lg"></i> {{ __('Add Transaction') }}
                            </a>
                        </div>
                    </form>

                    <!-- FILTER COLLAPSE -->
                    <div class="collapse mb-4 {{ request()->hasAny(['pemasukan', 'pengeluaran']) ? 'show' : '' }}" id="filterCollapse">
                        <div class="card card-body bg-light border-0 p-4 rounded-4">
                            <form method="GET" class="row g-3">
                                <!-- Preserve Date Filters if set -->
                                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('Income Category') }}</label>
                                    <select name="pemasukan[]" id="filter-pemasukan" class="form-select" multiple placeholder="{{ __('Select Income Categories...') }}" autocomplete="off">
                                        @foreach ($listPemasukan as $item)
                                            <option value="{{ $item->id }}" @selected(in_array($item->id, (array)request('pemasukan', [])))>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('Expense Category') }}</label>
                                    <select name="pengeluaran[]" id="filter-pengeluaran" class="form-select" multiple placeholder="{{ __('Select Expense Categories...') }}" autocomplete="off">
                                        @foreach ($listPengeluaran as $item)
                                            <option value="{{ $item->id }}" @selected(in_array($item->id, (array)request('pengeluaran', [])))>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted" id="btnResetFilter">{{ __('Reset Filter') }}</a>
                                    <button type="button" id="btnApplyFilter" class="btn btn-primary btn-sm rounded-pill px-4">{{ __('Apply Filter') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- DATE CARDS CONTAINER -->
                    <div id="transaction-table-container">
                        @include('transaksi._date_cards', ['groupedByDate' => $groupedByDate])
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAB Mobile --}}
<a href="{{ route('transaksi.create') }}" class="btn btn-primary fab-add shadow-lg" title="{{ __('Add Transaction') }}">
    <i class="bi bi-plus-lg fs-2"></i>
</a>

<!-- Details Modals -->
<div class="modal fade" id="incomeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-success fw-bold">{{ __('Income Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2" id="income-modal-body">
                 <ul class="list-group list-group-flush">
                    @forelse($summaryPemasukan as $row)
                        @php
                            $percentage = $totalPemasukan > 0 ? ($row->total / $totalPemasukan) * 100 : 0;
                        @endphp
                        <li class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium text-dark">{{ $row->pemasukanRelation->nama ?? 'Others' }}</span>
                                <span class="fw-bold text-success small">Rp {{ number_format($row->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-end text-muted" style="font-size: 10px;">{{ number_format($percentage, 1) }}%</div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-3">{{ __('No data available') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expenseDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
             <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-danger fw-bold">{{ __('Expense Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2" id="expense-modal-body">
                <ul class="list-group list-group-flush">
                    @forelse($summaryPengeluaran as $row)
                         @php
                            $percentage = $totalPengeluaran > 0 ? ($row->total / $totalPengeluaran) * 100 : 0;
                        @endphp
                        <li class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium text-dark">{{ $row->pengeluaranRelation->nama ?? 'Others' }}</span>
                                <span class="fw-bold text-danger small">Rp {{ number_format($row->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                             <div class="text-end text-muted" style="font-size: 10px;">{{ number_format($percentage, 1) }}%</div>
                        </li>
                    @empty
                         <li class="list-group-item text-center text-muted py-3">{{ __('No data available') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Email Export Modal -->
<div class="modal fade" id="emailExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Export to Email') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Recipient Email') }}</label>
                    <input type="email" id="export_recipient_email" class="form-control" value="{{ Auth::user()->email }}" required>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        {{ __('Current filtered data will be sent to this email.') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btnConfirmExportEmail" class="btn btn-primary rounded-pill px-4">{{ __('Send') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Open Date Modal -->
<div class="modal fade" id="openDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Pilih Tanggal') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Tanggal') }}</label>
                    <input type="date" id="input_open_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                <button type="button" id="btnGoToDate" class="btn btn-primary rounded-pill px-4">{{ __('Buka') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('transaksi.importTest') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Import Excel') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Select Excel File') }}</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text">{{ __('Supported formats: .xlsx, .xls, .csv') }}</div>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        {{ __('Use the provided template to ensure correct data formatting.') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Import Data') }}</button>
            </div>
        </form>
    </div>
</div>

@include('modal.transaksi.upload')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // State
        let debounceTimer;
        
        // Elements
        const searchInput = document.getElementById('searchTransaksi');
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const applyDateBtn = document.querySelector('button[title="Apply Filter"]');
        const tableContainer = document.getElementById('transaction-table-container');
        const btnResetFilter = document.getElementById('btnResetFilter');
        
        // Initialize TomSelect (Synchronized with anggaran/create style)
        const tomSettings = {
            plugins: ['remove_button'],
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
            persist: false,
            create: false
        };
        const tomPemasukan = new TomSelect('#filter-pemasukan', tomSettings);
        const tomPengeluaran = new TomSelect('#filter-pengeluaran', tomSettings);

        // Reset Filter Button Click
        if(btnResetFilter) {
            btnResetFilter.addEventListener('click', function(e) {
                e.preventDefault();
                // Clear inputs
                if(searchInput) searchInput.value = '';
                if(startDateInput) startDateInput.value = '';
                if(endDateInput) endDateInput.value = '';
                // Clear TomSelect
                tomPemasukan.clear();
                tomPengeluaran.clear();
                // Fetch
                fetchTransactions();
            });
        }

        // Apply Filter Button Click
        const applyFilterBtn = document.getElementById('btnApplyFilter');
        if(applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchTransactions();
            });
        }
        
        // Date Filter Apply Button
        if(applyDateBtn) {
            applyDateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchTransactions();
            });
        }
        
        // Search Input
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchTransactions();
                }, 500); 
            });
        }

        // Date card links inside container are plain anchors — no special JS needed.

        // Main Fetch Function
        function fetchTransactions(url = "{{ route('transaksi.index') }}") {
            // Collect Parameters
            const urlObj = new URL(url);
            
            // Search
            const searchQuery = searchInput ? searchInput.value : '';
            if(searchQuery) urlObj.searchParams.set('search', searchQuery);
            
            // Dates
            if(startDateInput && startDateInput.value) urlObj.searchParams.set('start_date', startDateInput.value);
            if(endDateInput && endDateInput.value) urlObj.searchParams.set('end_date', endDateInput.value);
            
            // Categories (TomSelect)
            urlObj.searchParams.delete('pemasukan[]'); 
            tomPemasukan.getValue().forEach(val => {
                if(val) urlObj.searchParams.append('pemasukan[]', val);
            });
            
            urlObj.searchParams.delete('pengeluaran[]'); 
            tomPengeluaran.getValue().forEach(val => {
                if(val) urlObj.searchParams.append('pengeluaran[]', val);
            });

            // Show loading
            tableContainer.style.opacity = '0.5';

            fetch(urlObj.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableContainer.style.opacity = '1';
                
                // Update Table
                tableContainer.innerHTML = data.html;
                
                // Update Summary Cards
                if(data.stats) {
                    updateSummaryCards(data.stats);
                }
                
                // Update Summary Modals
                if(data.modal_pemasukan) {
                     document.getElementById('income-modal-body').innerHTML = data.modal_pemasukan;
                }
                 if(data.modal_pengeluaran) {
                     document.getElementById('expense-modal-body').innerHTML = data.modal_pengeluaran;
                }
                
                // Update Export Links
                updateExportLinks();
            })
            .catch(error => {
                console.error('Error fetching transactions:', error);
                tableContainer.style.opacity = '1';
                alert('Failed to load data. Please try again.');
            });
        }

        function updateSummaryCards(stats) {
            // Update Income
            const incomeEl = document.querySelector('.text-success.fw-bold'); // Be specific if needed
            if(incomeEl) incomeEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.totalPemasukan);
            
            // Update Expense
            const expenseEl = document.querySelector('.text-danger.fw-bold');
            if(expenseEl) expenseEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.totalPengeluaran);
            
            // Update Net
            const netEl = document.querySelector('h4.mb-0.fw-bold:not(.text-success):not(.text-danger)');
            // The selector above is tricky because net can be success or danger.
            // Better to select the Net Balance card explicitly
            
            // Let's refine selectors based on structure
            // Income Card
            const cardIncome = document.querySelectorAll('.card-dashboard')[0];
            if(cardIncome) cardIncome.querySelector('h4').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.totalPemasukan);
            
            // Expense Card
            const cardExpense = document.querySelectorAll('.card-dashboard')[1];
            if(cardExpense) cardExpense.querySelector('h4').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.totalPengeluaran);
            
            // Net Card
            const cardNet = document.querySelectorAll('.card-dashboard')[2];
            if(cardNet) {
                const netH4 = cardNet.querySelector('h4');
                netH4.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.netIncome);
                netH4.classList.remove('text-success', 'text-danger');
                netH4.classList.add(stats.netIncome >= 0 ? 'text-success' : 'text-danger');
            }

            // Update Averages (Daily & Monthly)
            const dailyEl = document.getElementById('avg-daily');
            const monthlyEl = document.getElementById('avg-monthly');
            
            if(dailyEl && stats.avgDailyPengeluaran !== undefined) {
                dailyEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(stats.avgDailyPengeluaran);
            }

            // Update Date Range
            const dateRangeEl = document.getElementById('avg-date-range');
            if(dateRangeEl && stats.dateRange) {
                dateRangeEl.innerHTML = '<i class="bi bi-calendar3 me-1"></i> ' + stats.dateRange;
            }
        }
        
        // Export Links Logic
        const btnExportExcel = document.getElementById('btnExportExcel');
        const btnExportPdf = document.getElementById('btnExportPdf');
        const btnExportEmail = document.getElementById('btnExportEmail');

        function updateExportLinks() {
            const params = new URLSearchParams();

            // Search
            if(searchInput && searchInput.value) params.append('search', searchInput.value);

            // Dates
            if(startDateInput && startDateInput.value) params.append('start_date', startDateInput.value);
            if(endDateInput && endDateInput.value) params.append('end_date', endDateInput.value);

            // Categories
            tomPemasukan.getValue().forEach(val => {
                if(val) params.append('pemasukan[]', val);
            });
            tomPengeluaran.getValue().forEach(val => {
                if(val) params.append('pengeluaran[]', val);
            });

            // Helper to update href
            const updateLink = (link) => {
                if(!link) return;
                const url = new URL(link.dataset.baseUrl || link.href);
                // Store base URL if not already stored
                if(!link.dataset.baseUrl) link.dataset.baseUrl = link.href;
                
                link.href = `${url.origin}${url.pathname}?${params.toString()}`;
            };

            updateLink(btnExportExcel);
            updateLink(btnExportPdf);
            // btnExportEmail is now a modal trigger, so we'll handle its URL during click
        }

        // Handle Confirm Email Export
        const btnConfirmExportEmail = document.getElementById('btnConfirmExportEmail');
        if (btnConfirmExportEmail) {
            btnConfirmExportEmail.addEventListener('click', function() {
                const recipientEmail = document.getElementById('export_recipient_email').value;
                if (!recipientEmail) {
                    alert('Please enter a valid email address.');
                    return;
                }

                const params = new URLSearchParams();
                if(searchInput && searchInput.value) params.append('search', searchInput.value);
                if(startDateInput && startDateInput.value) params.append('start_date', startDateInput.value);
                if(endDateInput && endDateInput.value) params.append('end_date', endDateInput.value);
                tomPemasukan.getValue().forEach(val => {
                    if(val) params.append('pemasukan[]', val);
                });
                tomPengeluaran.getValue().forEach(val => {
                    if(val) params.append('pengeluaran[]', val);
                });
                params.append('email', recipientEmail);

                const exportUrl = "{{ route('transaksi.export.email') }}?" + params.toString();
                
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                this.disabled = true;

                // We use window.location.href for simplicity as the controller returns back()
                window.location.href = exportUrl;
                
                // Close modal after a short delay (enough for the browser to trigger the GET)
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('emailExportModal'));
                    if (modal) modal.hide();
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 1000);
            });
        }

        // Call on load to set initial state (including defaults)
        updateExportLinks();

        // Bulk Delete Action
        const btnBulkDelete = document.getElementById('btnBulkDelete');
         if (btnBulkDelete) {
            btnBulkDelete.addEventListener('click', function() {
                const checked = document.querySelectorAll('.check-item:checked');
                const ids = Array.from(checked).map(cb => cb.value);

                if (ids.length === 0) return;

                if (confirm(`Are you sure you want to delete ${ids.length} transactions?`)) {
                    // Show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                    this.disabled = true;

                    fetch("{{ route('transaksi.bulkDelete') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Something went wrong');
                    })
                    .then(data => {
                        // Refresh data instead of reload
                        fetchTransactions();
                         // Reset button
                        this.innerHTML = originalText;
                        this.disabled = false;
                        this.classList.add('d-none');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete transactions. Please try again.');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
                }
            });
        }

        // AJAX Deletion for Individual Items
        tableContainer.addEventListener('submit', function(e) {
            if (e.target.classList.contains('form-delete')) {
                e.preventDefault();
                
                if (confirm('Yakin ingin menghapus transaksi ini?')) {
                    const form = e.target;
                    const url = form.getAttribute('action');
                    const btn = form.querySelector('button');
                    const originalContent = btn.innerHTML;

                    // Show loading
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    btn.disabled = true;

                    fetch(url, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchTransactions(); // Refresh table
                        } else {
                            alert(data.message || 'Gagal menghapus data');
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus data');
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    });
                }
            }
        });
        // ========================
        // UPLOAD FILE LOGIC
        // ========================
        const uploadModal = document.getElementById('uploadModal');
        const uploadForm = document.getElementById('uploadForm');
        const transaksiIdInput = document.getElementById('transaksiId');

        // Handle Upload Button Click to pass ID
        tableContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-upload');
            if (btn) {
                const id = btn.getAttribute('data-id');
                if (transaksiIdInput) transaksiIdInput.value = id;
            }
        });

        // Handle AJAX Upload
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading...';
                submitBtn.disabled = true;

                fetch("{{ route('upload') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(uploadModal);
                        if (modal) modal.hide();
                        
                        // Reset form
                        uploadForm.reset();
                        
                        // Refresh data
                        fetchTransactions();
                        
                        // Show success alert (optional, fetchTransactions might be enough visual feedback)
                    } else {
                        alert(data.message || 'Gagal mengupload file');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengupload file');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }

        // Handle File Deletion
        tableContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete-file');
            if (btn) {
                const id = btn.getAttribute('data-id');
                if (confirm('Yakin ingin menghapus file bukti ini?')) {
                    const originalContent = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    btn.disabled = true;

                    fetch(`{{ url('transaksi') }}/${id}/file`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchTransactions();
                        } else {
                            alert(data.message || 'Gagal menghapus file');
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus file');
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    });
                }
            }
        });
        // Handle Open Date
        const btnGoToDate = document.getElementById('btnGoToDate');
        const inputOpenDate = document.getElementById('input_open_date');
        if (btnGoToDate && inputOpenDate) {
            btnGoToDate.addEventListener('click', function() {
                const date = inputOpenDate.value;
                if (date) {
                    window.location.href = `{{ url('transaksi/date') }}/${date}`;
                }
            });
        }
    });

</script>
@endpush