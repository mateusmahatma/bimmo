@extends('layouts.main')

@section('title', 'Income Categories')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .check-item { cursor: pointer; }

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
        #pemasukanTable td:nth-of-type(1):before { content: "Select"; }
        #pemasukanTable td:nth-of-type(3):before { content: "Category Name"; }
        #pemasukanTable td:nth-of-type(4):before { content: "Created At"; }
        #pemasukanTable td:nth-of-type(5):before { content: "Last Updated"; }
        #pemasukanTable td:nth-of-type(6):before { content: "Action"; top: 1.5rem; }

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
    <h1>Income Categories</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Income Categories</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">List of Income Categories</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Manage your income classification and sources efficiently.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                        </button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 tombol-tambah-pemasukan shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Category
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="pemasukanTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr style="border-bottom: 2px solid #edf2f9;">
                                    <th style="width: 5%;" class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                                        </div>
                                    </th>
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3">No</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Category Name</th>
                                    <th class="text-center text-secondary small text-uppercase fw-bold py-3">Created At</th>
                                    <th class="text-center text-secondary small text-uppercase fw-bold py-3">Last Updated</th>
                                    <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endpush