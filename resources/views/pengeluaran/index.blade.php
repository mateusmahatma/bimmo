@extends('layouts.main')

@section('title', __('Expense'))

@push('css')
<style>
    .check-item { cursor: pointer; }

    /* Responsive Table to Card View */
    @media (max-width: 768px) {
        #pengeluaranTable, 
        #pengeluaranTable thead, 
        #pengeluaranTable tbody, 
        #pengeluaranTable th, 
        #pengeluaranTable td, 
        #pengeluaranTable tr { 
            display: block; 
        }

        #pengeluaranTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #pengeluaranTable tr { 
            border: 1px solid #ccc; 
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 10px;
        }

        #pengeluaranTable td { 
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 45%; 
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            text-align: right;
            white-space: normal; 
        }

        #pengeluaranTable td:before { 
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
        #pengeluaranTable td:nth-of-type(1):before { content: "{{ __('Select') }}"; }
        #pengeluaranTable td:nth-of-type(3):before { content: "{{ __('Category Name') }}"; }
        #pengeluaranTable td:nth-of-type(4):before { content: "{{ __('Created At') }}"; }
        #pengeluaranTable td:nth-of-type(5):before { content: "{{ __('Last Updated') }}"; }
        #pengeluaranTable td:nth-of-type(6):before { content: "{{ __('Action') }}"; top: 1.5rem; }

        /* Special handling for the checkbox cell */
        #pengeluaranTable td:nth-of-type(1) {
            text-align: left;
            padding-left: 10px;
            border-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #pengeluaranTable td:nth-of-type(1):before {
            position: static;
            width: auto;
            margin-right: 10px;
        }
        
        /* Special handling for Action cell */
        #pengeluaranTable td:nth-of-type(6) {
             padding-left: 10px;
             text-align: center;
             display: flex;
             justify-content: center;
             gap: 10px;
             border-bottom: 0;
        }
        #pengeluaranTable td:nth-of-type(6):before { 
            display: none; 
        }
        
        /* Hide ID/No if needed */
        #pengeluaranTable td:nth-of-type(2) { display: none; }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Expense') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Expense') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('List of Expense') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage your expense classification and types efficiently.') }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="search-container position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="searchPengeluaran" class="form-control form-control-sm rounded-pill ps-5" placeholder="{{ __('Search Categories...') }}" style="min-width: 200px; font-size: 0.8rem;">
                        </div>
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }} (<span id="countSelected">0</span>)
                        </button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm tombol-tambah-pengeluaran" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Category') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0" id="pengeluaran-table-container">
                    @include('pengeluaran._table_list')
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Modals -->
@include('modal.pengeluaran.index')
@include('modal.pengeluaran.delete')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pengeluaran.js') }}?v={{ filemtime(public_path('js/pengeluaran.js')) }}"></script>
@endpush