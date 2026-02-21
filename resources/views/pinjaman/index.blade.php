@extends('layouts.main')

@section('title', 'Loan List')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    @media screen and (max-width: 768px) {
        /* Force table to not be like tables anymore */
        #pinjamanTable, 
        #pinjamanTable thead, 
        #pinjamanTable tbody, 
        #pinjamanTable th, 
        #pinjamanTable td, 
        #pinjamanTable tr { 
            display: block; 
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        #pinjamanTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #pinjamanTable tr { 
            border: 1px solid #eef2f7; 
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 0.5rem;
        }

        #pinjamanTable td { 
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
        
        #pinjamanTable td:last-child {
            border-bottom: 0;
            justify-content: center;
            padding-left: 0;
            margin-top: 0.5rem;
        }

        #pinjamanTable td:before { 
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
        
        /* Actions button centering */
        #pinjamanTable td[data-label="Action"] {
           justify-content: flex-end;
           padding-left: 45%;
        }
        
        #pinjamanTable td[data-label="No"] {
            display: none;
        }
        
         .pagination {
            justify-content: center !important;
        }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Loan List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Loans</li>
        </ol>
    </nav>
</div>

@php
    $totalRemaining = $pinjaman->sum('jumlah_pinjaman');
    $totalPaid = $pinjaman->sum(fn($p) => $p->bayar_pinjaman->sum('jumlah_bayar'));
    $totalOriginal = $totalRemaining + $totalPaid;
@endphp

<section class="section dashboard">
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary me-3" style="width: 48px; height: 48px; background: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-bank fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Total Loan</h6>
                            <h4 class="mb-0 fw-bold text-primary" id="statTotalOriginal">Rp {{ number_format($totalOriginal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success me-3" style="width: 48px; height: 48px; background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-check-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Total Paid</h6>
                            <h4 class="mb-0 fw-bold text-success" id="statTotalPaid">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger me-3" style="width: 48px; height: 48px; background: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-exclamation-circle fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Remaining Balance</h6>
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
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">Loan List</h5>
                        <p class="text-muted small mb-0 mt-1">Manage and track your loan records.</p>
                    </div>
                    <div class="d-flex gap-2">
                         <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Delete (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('pinjaman.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add New Loan
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- Toolbar -->
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3 pt-3">
                        <div class="d-flex gap-2 align-items-center flex-grow-1">
                             <div class="search-bar" style="min-width: 250px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                    {{-- DataTables default search will be used, but we can style a custom one if needed. 
                                         However, JS is set to use the built-in search. Let's just keep the status filter for now. --}}
                                    <select class="form-select bg-light border-start-0 rounded-end-pill" id="filter_status" style="box-shadow: none;">
                                        <option value="">All Status</option>
                                        <option value="belum_lunas">Unpaid Only</option>
                                        <option value="lunas">Paid Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                             <a href="{{ route('pinjaman.export.excel') }}" id="btnExportExcel" class="btn btn-outline-success btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="pinjamanTable" class="table table-hover table-borderless align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;" class="text-center">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </th>
                                    <th style="width: 5%;">No</th>
                                    <th>Loan Name</th>
                                    <th>Notes</th>
                                    <th class="text-end">Total Loan</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Remaining Balance</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this --}}
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Modals -->
@include('modal.pinjaman.index')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endpush
