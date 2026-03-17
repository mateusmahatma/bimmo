@extends('layouts.main')

@section('title', __('Income Categories'))

@push('css')
<style>
    .check-item { cursor: pointer; }

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

    /* Responsive Table to Card View */
    @media (max-width: 768px) {
        #pemasukanTable, 
        #pemasukanTable thead, 
        #pemasukanTable tbody, 
        #pemasukanTable th, 
        #pemasukanTable td, 
        #pemasukanTable tr { 
            display: block; 
        }

        #pemasukanTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #pemasukanTable tr { 
            border: 1px solid #ccc; 
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 10px;
        }

        #pemasukanTable td { 
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 45%; 
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            text-align: right;
            white-space: normal; 
        }

        #pemasukanTable td:before { 
            position: absolute;
            top: 0.8rem;
            left: 10px;
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #6c757d;
        }

        /* Labeling the data based on th order */
        #pemasukanTable td:nth-of-type(1):before { content: "{{ __('Select') }}"; }
        #pemasukanTable td:nth-of-type(3):before { content: "{{ __('Category Name') }}"; }
        #pemasukanTable td:nth-of-type(4):before { content: "{{ __('Created At') }}"; }
        #pemasukanTable td:nth-of-type(5):before { content: "{{ __('Last Updated') }}"; }
        #pemasukanTable td:nth-of-type(6):before { content: "{{ __('Action') }}"; top: 1.5rem; }

        /* Special handling for the checkbox cell */
        #pemasukanTable td:nth-of-type(1) {
            text-align: left;
            padding-left: 10px;
            border-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #pemasukanTable td:nth-of-type(1):before {
            position: static;
            width: auto;
            margin-right: 10px;
        }
        
        /* Special handling for Action cell */
        #pemasukanTable td:nth-of-type(6) {
             padding-left: 10px;
             text-align: center;
             display: flex;
             justify-content: center;
             gap: 10px;
             border-bottom: 0;
        }
        #pemasukanTable td:nth-of-type(6):before { 
            display: none; 
        }
        
        /* Hide ID/No if needed */
        #pemasukanTable td:nth-of-type(2) { display: none; }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Income') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Income') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('List of Income Categories') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage your income classification and sources efficiently.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }} (<span id="countSelected">0</span>)
                        </button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill tombol-tambah-pemasukan shadow-sm" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Income') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- SEARCH BAR -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                        <div class="search-bar" style="min-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="searchPemasukan" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="{{ __('Search categories...') }}" style="box-shadow: none; font-size: 0.8rem;">
                            </div>
                        </div>
                    </div>

                    <div id="pemasukan-table-container">
                        @include('pemasukan._table_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('modal.pemasukan.index')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endpush