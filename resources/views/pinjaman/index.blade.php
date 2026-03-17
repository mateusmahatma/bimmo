@extends('layouts.main')

@section('title', __('Liability List'))

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
        
        #pinjamanTable, 
        #pinjamanTable thead, 
        #pinjamanTable tbody, 
        #pinjamanTable th, 
        #pinjamanTable td, 
        #pinjamanTable tr { 
            display: block; 
        }

        /* Hide table headers */
        #pinjamanTable thead tr { 
            position: absolute;
            top: -9999px; left: -9999px;
        }

        #pinjamanTable tr {
            border: 0;
            margin-bottom: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        #pinjamanTable td {
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
        
        #pinjamanTable td .badge {
            white-space: normal !important;
            text-align: right;
            max-width: 100%;
        }

        #pinjamanTable td:last-child {
             padding-left: 10px;
             text-align: center;
             display: flex !important;
             justify-content: center !important;
             align-items: center !important;
             gap: 10px;
             border-bottom: 0;
             margin-top: 5px;
        }

        #pinjamanTable td:before { 
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
        
        #pinjamanTable td:last-child:before { 
            display: none;
        }
        
        #pinjamanTable td[data-label="No"] {
            display: none;
        }
    }

    [data-bs-theme="dark"] .card-dashboard {
        background-color: #1a1a1a;
        border-color: rgba(255,255,255,0.05);
        color: #e0e0e0;
    }
    [data-bs-theme="dark"] #pinjamanTable tr {
        background-color: #1a1a1a;
        border-color: rgba(255,255,255,0.05);
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Liability List') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Liabilitys') }}</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary me-3" style="width: 48px; height: 48px; background: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-bank fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Liability') }}</h6>
                            <h4 class="mb-0 fw-bold text-primary" id="statTotalOriginal">Rp {{ number_format($totalOriginal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success me-3" style="width: 48px; height: 48px; background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-check-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Total Paid') }}</h6>
                            <h4 class="mb-0 fw-bold text-success" id="statTotalPaid">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger me-3" style="width: 48px; height: 48px; background: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-exclamation-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1 fw-bold">{{ __('Remaining Balance') }}</h6>
                            <h4 class="mb-0 fw-bold text-danger" id="totalPinjaman">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="col-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Liability List') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage and track your Liability records.') }}</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse">
                            <i class="bi bi-info-circle me-1"></i> {{ __('Instructions') }}
                        </button>
                        <div class="vr mx-1 d-none d-lg-block"></div>
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete') }} (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('pinjaman.create') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm btn-add-desktop" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Liability') }}
                        </a>
                    </div>
                </div>

                <div class="collapse" id="instructionsCollapse">
                    <div class="card-body bg-light border-bottom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="alert alert-info border-0 bg-white shadow-sm mb-0 text-dark">
                                    <h6 class="fw-bold mb-2 small text-uppercase"><i class="bi bi-wallet2 me-2"></i>{{ __('How to Pay Liability:') }}</h6>
                                    <ol class="mb-0 ps-3 small">
                                        <li>{{ __('Find the Liability you want to pay in the list below.') }}</li>
                                        <li>{{ __('Click the Blue Eye Icon') }} (<strong><i class="bi bi-eye"></i></strong>) {{ __('to view details.') }}</li>
                                        <li>{{ __('Click the Pay Liability button at the top right of the history section.') }}</li>
                                        <li>{{ __('Enter the amount, date, and optionally upload proof.') }}</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success border-0 bg-white shadow-sm mb-0 text-dark">
                                    <h6 class="fw-bold mb-2 small text-uppercase"><i class="bi bi-upload me-2"></i>{{ __('How to Upload Proof:') }}</h6>
                                    <ol class="mb-0 ps-3 small">
                                        <li>{{ __('You can upload proof while making a payment.') }}</li>
                                        <li>{{ __('To add/update proof later, go to Details view.') }}</li>
                                        <li>{{ __('In the History table, click the Pencil Icon') }} (<strong><i class="bi bi-pencil"></i></strong>) {{ __('on a payment row.') }}</li>
                                        <li>{{ __('Choose your file (JPG, PNG, PDF) and save.') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- Toolbar -->
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3 pt-4">
                        <div class="d-flex gap-2 align-items-center flex-grow-1">
                             <div class="search-bar" style="min-width: 250px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="searchPinjaman" class="form-control bg-light border-start-0 border-end-0 ps-1 shadow-none" placeholder="{{ __('Search by name...') }}" style="font-size: 0.8rem;">
                                    <select class="form-select bg-light border-start-1 rounded-end-pill shadow-none" id="filter_status" style="max-width: 100px; font-size: 0.8rem;">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="belum_lunas">{{ __('Unpaid') }}</option>
                                        <option value="lunas">{{ __('Paid') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                             <a href="{{ route('pinjaman.export.excel') }}" id="btnExportExcel" class="btn btn-outline-success btn-sm rounded-pill d-flex align-items-center gap-2" style="padding: 2px 10px; font-size: 0.75rem;">
                                <i class="bi bi-file-earmark-excel"></i> {{ __('Export Excel') }}
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div id="pinjaman-table-container">
                        @include('pinjaman._table_list')
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Modals -->
@include('modal.pinjaman.index')

<!-- Floating Action Button for Mobile -->
<a href="{{ route('pinjaman.create') }}" class="btn btn-primary fab-add" title="{{ __('Add New Liability') }}">
    <i class="bi bi-plus-lg fs-2"></i>
</a>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endpush
