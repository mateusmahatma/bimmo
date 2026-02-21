@extends('layouts.main')

@section('title', 'Transactions')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    @media screen and (max-width: 768px) {
        /* Force table to not be like tables anymore */
        #transaksiTable, 
        #transaksiTable thead, 
        #transaksiTable tbody, 
        #transaksiTable th, 
        #transaksiTable td, 
        #transaksiTable tr { 
            display: block; 
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        #transaksiTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #transaksiTable tr { 
            border: 1px solid #eef2f7; 
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 0.5rem;
        }

        #transaksiTable td { 
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #f8f9fa; 
            position: relative;
            padding-left: 45%; 
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: right; /* Align text to right */
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        #transaksiTable td:last-child {
            border-bottom: 0;
            justify-content: center;
            padding-left: 0;
            margin-top: 0.5rem;
        }

        #transaksiTable td:before { 
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            left: 0.75rem; 
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            content: attr(data-label);
        }
        
        /* Specific adjustments */
        #transaksiTable td.mobile-checkbox {
            padding-left: 0;
            display: flex;
            justify-content: flex-start;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
            margin: -0.5rem -0.5rem 0.5rem -0.5rem;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #eef2f7;
        }
        
        #transaksiTable td.mobile-checkbox:before {
            display: none;
        }
        
        #transaksiTable td.mobile-checkbox .form-check {
            margin: 0;
        }
        
        /* Adjust Description to allow wrapping */
        #transaksiTable td[data-label="Description"] {
            height: auto;
            align-items: flex-start;
        }
        
         /* No Column - maybe hide it or style it */
        #transaksiTable td[data-label="No"] {
            display: none;
        }
        
        /* Category Badge alignment */
        #transaksiTable td[data-label="Category"] {
            align-items: center;
        }
        
        /* Action buttons */
        #transaksiTable td[data-label="Action"] {
           justify-content: flex-end;
           padding-left: 45%;
        }
        
         .pagination {
            justify-content: center !important;
        }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Transactions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <!-- SUMMARY CARDS -->
        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success me-3" style="width: 48px; height: 48px; background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-arrow-down-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Total Income</h6>
                            <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#incomeDetailsModal" class="small text-decoration-none text-success fw-bold">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center mb-2">
                       <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger me-3" style="width: 48px; height: 48px; background: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-arrow-up-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Total Expense</h6>
                            <h4 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#expenseDetailsModal" class="small text-decoration-none text-danger fw-bold">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary me-3" style="width: 48px; height: 48px; background: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Net Balance</h6>
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
            <div class="card-dashboard border-0 shadow-sm" style="border-radius: 12px; background: #fff;">
                <div class="card-body py-3">
                    <div class="d-flex flex-column align-items-start text-start">
                        <h6 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Period Expense</h6>
                        <span class="badge bg-light text-secondary border fw-normal rounded-pill px-2 py-1 mb-2" id="avg-date-range" style="font-size: 0.7rem;">
                            <i class="bi bi-calendar3 me-1"></i> {{ $dateRange }}
                        </span>

                        <div class="d-flex align-items-center">
                             <div class="text-start">
                                <h4 class="mb-0 fw-bold text-dark" id="avg-daily" style="font-size: 1.25rem;">Rp {{ number_format($avgDailyPengeluaran, 0, ',', '.') }}</h4>
                                <span class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Daily Average</span>
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
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Transaction List</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Manage your income and expense data.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3" id="btnBulkDelete">
                            <i class="bi bi-trash me-1"></i> Delete (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Transaction
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- TOOLBAR -->
                    <form action="{{ route('transaksi.index') }}" method="GET" class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3 pt-3">
                        <!-- Preserve Category Filters -->
                        @if(is_array(request('pemasukan')))
                            @foreach(request('pemasukan') as $p)
                                <input type="hidden" name="pemasukan[]" value="{{ $p }}">
                            @endforeach
                        @else
                            <input type="hidden" name="pemasukan" value="{{ request('pemasukan') }}">
                        @endif

                        @if(is_array(request('pengeluaran')))
                            @foreach(request('pengeluaran') as $p)
                                <input type="hidden" name="pengeluaran[]" value="{{ $p }}">
                            @endforeach
                        @else
                            <input type="hidden" name="pengeluaran" value="{{ request('pengeluaran') }}">
                        @endif
                        
                        <!-- Search & Date Filter Group -->
                        <div class="d-flex gap-2 flex-wrap align-items-center flex-grow-1">
                            <!-- Search (Client-side) -->
                            <div class="search-bar" style="min-width: 200px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="searchTransaksi" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Search transactions..." style="box-shadow: none;">
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
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')])) }}" class="btn btn-outline-secondary rounded-start-pill" title="This Month">This Month</a>
                                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01', strtotime('-1 month')), 'end_date' => date('Y-m-t', strtotime('-1 month'))])) }}" class="btn btn-outline-secondary rounded-end-pill" title="Last Month">Last Month</a>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="bi bi-sliders"></i> Filter
                            </button>
                            
                            <div class="dropdown">
                                <button class="btn btn-outline-success btn-sm dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i> Export
                                </button>
                                <ul class="dropdown-menu shadow border-0 rounded-3">
                                    <li><a class="dropdown-item" id="btnExportExcel" href="{{ route('transaksi.export.excel', request()->all()) }}"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel</a></li>
                                    <li><a class="dropdown-item" id="btnExportPdf" href="{{ route('transaksi.export.pdf', request()->all()) }}"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" id="btnExportEmail" href="#" data-bs-toggle="modal" data-bs-target="#emailExportModal"><i class="bi bi-envelope me-2 text-primary"></i> Send to Email</a></li>
                                </ul>
                            </div>

                             <button type="button" class="btn btn-success btn-sm rounded-pill px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                                <i class="bi bi-upload"></i> Import
                            </button>

                             <a href="{{ route('transaksi.download.template') }}" class="btn btn-outline-secondary btn-sm rounded-circle" title="Download Template" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
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
                                    <label class="form-label small fw-bold text-muted text-uppercase">Income Category</label>
                                    <div class="dropdown">
                                        <button class="btn btn-white w-100 text-start d-flex justify-content-between align-items-center border bg-white rounded-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                            <span>Select Category</span>
                                            <span class="badge bg-secondary ms-2 rounded-pill" id="count-pemasukan">0</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2 shadow border-0 rounded-3" style="max-height: 250px; overflow-y: auto;">
                                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Income Sources</h6></li>
                                            @foreach ($listPemasukan as $item)
                                            <li class="dropdown-item-text">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox-pemasukan" type="checkbox" name="pemasukan[]" value="{{ $item->id }}" id="in_{{ $item->id }}" @checked(in_array($item->id, (array)request('pemasukan', [])))>
                                                    <label class="form-check-label w-100" for="in_{{ $item->id }}">
                                                        {{ $item->nama }}
                                                    </label>
                                                </div>
                                            </li>
                                            @endforeach
                                            @if($listPemasukan->isEmpty())
                                                <li class="text-muted small text-center py-2">No categories found</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Expense Category</label>
                                    <div class="dropdown">
                                        <button class="btn btn-white w-100 text-start d-flex justify-content-between align-items-center border bg-white rounded-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                            <span>Select Category</span>
                                            <span class="badge bg-secondary ms-2 rounded-pill" id="count-pengeluaran">0</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2 shadow border-0 rounded-3" style="max-height: 250px; overflow-y: auto;">
                                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Expense Types</h6></li>
                                            @foreach ($listPengeluaran as $item)
                                            <li class="dropdown-item-text">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox-pengeluaran" type="checkbox" name="pengeluaran[]" value="{{ $item->id }}" id="out_{{ $item->id }}" @checked(in_array($item->id, (array)request('pengeluaran', [])))>
                                                    <label class="form-check-label w-100" for="out_{{ $item->id }}">
                                                        {{ $item->nama }}
                                                    </label>
                                                </div>
                                            </li>
                                            @endforeach
                                              @if($listPengeluaran->isEmpty())
                                                <li class="text-muted small text-center py-2">No categories found</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted">Reset Filter</a>
                                    <button class="btn btn-primary btn-sm rounded-pill px-4">Apply</button>
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

                    <!-- DATA TABLE CONTAINER -->
                    <div id="transaction-table-container">
                        @include('transaksi._table_list')
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Details Modals -->
<div class="modal fade" id="incomeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-success fw-bold">Income Details</h5>
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
                        <li class="list-group-item text-center text-muted py-3">No data available</li>
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
                <h5 class="modal-title text-danger fw-bold">Expense Details</h5>
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
                         <li class="list-group-item text-center text-muted py-3">No data available</li>
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
                <h5 class="modal-title fw-bold">Export to Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Recipient Email</label>
                    <input type="email" id="export_recipient_email" class="form-control" value="{{ Auth::user()->email }}" required>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        Current filtered data will be sent to this email.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btnConfirmExportEmail" class="btn btn-primary rounded-pill px-4">Send</button>
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
                <h5 class="modal-title fw-bold">Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Select Excel File</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text">Supported formats: .xlsx, .xls, .csv</div>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        Use the provided template to ensure correct data formatting.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Import Data</button>
            </div>
        </form>
    </div>
</div>

@include('modal.transaksi.upload')
@endsection

@push('scripts')
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
        
        // Count Badges
        function updateCount(name, badgeId) {
            const checkboxes = document.querySelectorAll(`input[name="${name}"]`);
            const badge = document.getElementById(badgeId);
            if(checkboxes && badge) {
                const count = Array.from(checkboxes).filter(c => c.checked).length;
                badge.textContent = count;
                badge.classList.toggle('bg-primary', count > 0);
                badge.classList.toggle('bg-secondary', count === 0);
            }
        }

        // Initialize Counts
        updateCount('pemasukan[]', 'count-pemasukan');
        updateCount('pengeluaran[]', 'count-pengeluaran');

        // Checkbox Listeners (Categories)
        document.addEventListener('change', function(e) {
            if (e.target.matches('.filter-checkbox-pemasukan')) {
                updateCount('pemasukan[]', 'count-pemasukan');
            }
            if (e.target.matches('.filter-checkbox-pengeluaran')) {
                updateCount('pengeluaran[]', 'count-pengeluaran');
            }
        });

        // Apply Filter Button Click
        const applyFilterBtn = document.querySelector('#filterCollapse button.btn-primary');
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

        // Pagination and Sorting Links (Delegate)
        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a') || e.target.closest('.sort-link');
            if (link) {
                e.preventDefault();
                const url = link.getAttribute('href');
                if(url) {
                    fetchTransactions(url);
                }
            }
            
            // Re-bind Bulk Delete Check All if needed (handled by mutation observer or direct check below)
        });

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
            
            // Categories (Pemasukan)
            const pemCheckboxes = document.querySelectorAll('input[name="pemasukan[]"]:checked');
            // Remove existing array params first to avoid duplicates
            urlObj.searchParams.delete('pemasukan[]'); 
            pemCheckboxes.forEach(cb => {
                urlObj.searchParams.append('pemasukan[]', cb.value);
            });
            
            // Categories (Pengeluaran)
            const pengCheckboxes = document.querySelectorAll('input[name="pengeluaran[]"]:checked');
            urlObj.searchParams.delete('pengeluaran[]'); 
            pengCheckboxes.forEach(cb => {
                urlObj.searchParams.append('pengeluaran[]', cb.value);
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
                
                // Update Summary Modals (Income/Expense Details)
                if(data.modal_pemasukan) {
                     document.getElementById('income-modal-body').innerHTML = data.modal_pemasukan;
                }
                 if(data.modal_pengeluaran) {
                     document.getElementById('expense-modal-body').innerHTML = data.modal_pengeluaran;
                }
                
                // Re-initialize bulk delete listeners
                initBulkDelete();
                
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
        
        // ========================
        // BULK DELETE LOGIC (Re-callable)
        // ========================
        function initBulkDelete() {
            const checkAll = document.getElementById('checkAll');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');
            
            function updateBulkDeleteUI() {
                const checked = document.querySelectorAll('.check-item:checked');
                const count = checked.length;
                
                if (countSelected) countSelected.textContent = count;
                
                if (btnBulkDelete) {
                    if (count > 0) {
                        btnBulkDelete.classList.remove('d-none');
                    } else {
                        btnBulkDelete.classList.add('d-none');
                    }
                }
                
                // Update checkAll state
                const allItems = document.querySelectorAll('.check-item');
                 if (checkAll && allItems.length > 0) {
                    checkAll.checked = checked.length === allItems.length;
                    checkAll.indeterminate = checked.length > 0 && checked.length < allItems.length;
                }
            }
            
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.querySelectorAll('.check-item').forEach(item => {
                        item.checked = isChecked;
                    });
                    updateBulkDeleteUI();
                });
            }
            
            // Delegate for dynamic items
            if(tableContainer) {
                tableContainer.addEventListener('change', function(e) {
                    if(e.target.classList.contains('check-item')) {
                        updateBulkDeleteUI();
                    }
                });
            }
        }

        // Initialize Bulk Delete on Load
        initBulkDelete();
        
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
            document.querySelectorAll('input[name="pemasukan[]"]:checked').forEach(cb => {
                params.append('pemasukan[]', cb.value);
            });
            document.querySelectorAll('input[name="pengeluaran[]"]:checked').forEach(cb => {
                params.append('pengeluaran[]', cb.value);
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
                document.querySelectorAll('input[name="pemasukan[]"]:checked').forEach(cb => {
                    params.append('pemasukan[]', cb.value);
                });
                document.querySelectorAll('input[name="pengeluaran[]"]:checked').forEach(cb => {
                    params.append('pengeluaran[]', cb.value);
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
    });

</script>
@endpush