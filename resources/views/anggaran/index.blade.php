@extends('layouts.main')

@section('title', __('Budget List'))

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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
    .card-summary {
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        background: #ffffff;
        color: #2d3436;
        overflow: hidden;
        position: relative;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .check-item { cursor: pointer; }
    .progress { height: 10px !important; }
    .progress-bar { transition: width 0.6s ease-in-out, background-color 0.3s ease !important; }

    .fab-add {
        position: fixed;
        bottom: 2.5rem;
        right: 1.5rem;
        z-index: 1040;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: none; /* Desktop hidden */
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(1, 41, 112, 0.3);
        transition: all 0.3s ease;
        padding: 0;
    }
    .fab-add i { font-size: 1.5rem !important; }

    @media (max-width: 767.98px) {
        .fab-add {
            display: flex;
        }
        .btn-add-desktop {
            display: none;
        }
        
        #anggaranTable, 
        #anggaranTable thead, 
        #anggaranTable tbody, 
        #anggaranTable th, 
        #anggaranTable td, 
        #anggaranTable tr { 
            display: block; 
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        #anggaranTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #anggaranTable tr {
            border: 0;
            margin-bottom: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        #anggaranTable td {
            border: none;
            border-bottom: 1px solid #f8f9fa;
            position: relative;
            padding-left: 45%; 
            padding-top: 1rem;
            padding-bottom: 1rem;
            text-align: right;
            white-space: normal;
            min-height: 3rem;
        }
        
        #anggaranTable td:last-child {
            border-bottom: 0;
        }

        /* Stacked Layout for Expense Types (nth-of-type 5) */
        #anggaranTable td:nth-of-type(5) {
            text-align: left;
            padding-left: 15px;
            padding-top: 2.5rem;
        }

        #anggaranTable td:before { 
            position: absolute;
            top: 1rem;
            left: 15px;
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        /* Column Labels */
        #anggaranTable td:nth-of-type(1):before { content: "Select"; }
        #anggaranTable td:nth-of-type(3):before { content: "Budget Name"; }
        #anggaranTable td:nth-of-type(4):before { content: "Percentage"; }
        #anggaranTable td:nth-of-type(5):before { 
            content: "Expense Types"; 
            width: 100%;
            border-bottom: 1px solid #f8f9fa;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        #anggaranTable td:nth-of-type(8):before { content: "Action"; top: 1.1rem; }

        /* Special handling for the checkbox cell */
        #anggaranTable td:nth-of-type(1) {
            text-align: left;
            padding-left: 10px;
            border-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #anggaranTable td:nth-of-type(1):before {
            position: static;
            width: auto;
            margin-right: 10px;
        }
        
        /* Special handling for Action cell */
        #anggaranTable td:nth-of-type(8) {
             padding-left: 10px;
             text-align: center;
             display: flex;
             justify-content: center;
             gap: 10px;
             border-bottom: 0;
        }
        #anggaranTable td:nth-of-type(8):before { 
            display: none; /* Hide label for action buttons */
        }
        
        /* Hide ID/No/Dates on mobile */
        #anggaranTable td:nth-of-type(2), 
        #anggaranTable td:nth-of-type(6), 
        #anggaranTable td:nth-of-type(7) {
            display: none;
        }
    }

    [data-bs-theme="dark"] .card-summary {
        background: linear-gradient(135deg, #1a1a1a 0%, #084e6a 100%);
    }
    [data-bs-theme="dark"] #anggaranTable tr {
        background-color: #1e1e1e;
        border-color: rgba(255,255,255,0.05);
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Budget List') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Budgets') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Summary / Percentage Report Card -->
        <div class="col-lg-12 mb-4">
            <div class="card card-summary">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <div>
                            <h5 class="card-title mb-1 fw-bold text-dark opacity-75" style="font-size: 1.1rem;">{{ __('Total Budget Allocation') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Percentage allocation across all your budget categories.') }}</p>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-bold mb-0 text-primary" id="totalPersentase">{{ $totalPersentase }}%</h2>
                        </div>
                    </div>

                    <div class="mt-2 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-medium">{{ __('Total distributed percentage') }}</span>
                            <span class="text-dark small fw-bold" id="totalPersentaseLabel">{{ $totalPersentase }}% / 100%</span>
                        </div>
                        <div class="progress rounded-pill bg-light" style="height: 10px;">
                            <div class="progress-bar {{ $totalPersentase > 100 ? 'bg-danger' : 'bg-primary' }} rounded-pill" id="totalAllocationBar" role="progressbar" style="width: {{ min($totalPersentase, 100) }}%;" aria-valuenow="{{ $totalPersentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2 align-items-center">
                            <small class="text-muted italic small">{{ __('Manage your allocation properly to avoid overspending.') }}</small>
                            <span id="exceedMessage" class="badge bg-danger d-none"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('List of Budgets') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage your budget allocations efficiently.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }} (<span id="countSelected">0</span>)
                        </button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm tombol-tambah-anggaran btn-add-desktop" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add New') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- SEARCH BAR -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                        <div class="search-bar" style="min-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="entrySearch" class="form-control bg-light border-start-0 rounded-end-pill shadow-none" style="font-size: 0.8rem;" placeholder="{{ __('Search budget...') }}">
                            </div>
                        </div>
                    </div>

                    <div id="tableContainer">
                        @include('anggaran._table_list')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button for Mobile -->
    <a href="javascript:void(0)" class="btn btn-primary fab-add tombol-tambah-anggaran" title="{{ __('Add Budget') }}">
        <i class="bi bi-plus-lg"></i>
    </a>
</section>

<!-- Include Modal -->
@include('modal.anggaran.index')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endpush