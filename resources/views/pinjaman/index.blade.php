@extends('layouts.main')

@section('title', 'Loan List')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
            <!-- Summary Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-3">
                     <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Total Outstanding Loans</h6>
                            <h3 class="fw-bold text-primary mb-0" id="totalPinjaman">Rp 0</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                             <select class="form-select form-select-sm" id="filter_status" name="filter_status" style="width: 200px;">
                                <option value="">All Status</option>
                                <option value="belum_lunas">Unpaid</option>
                                <option value="lunas">Paid</option>
                            </select>
                        </div>
                     </div>
                </div>
            </div>

            <div class="card-dashboard">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">List of Loans</h5>
                                <p class="text-muted small mb-0">Manage your loan records here.</p>
                            </div>
                            <button id="btnBulkDelete" class="btn btn-danger btn-sm d-none">
                                <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                            </button>
                        </div>
                        <button type="button" class="btn btn-success">
                            <a href="{{ route('pinjaman.create') }}" class="text-white text-decoration-none">
                                <i class="bi bi-plus-lg me-1"></i> Add New Loan
                            </a>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="pinjamanTable" class="table table-hover table-borderless align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;" class="text-center">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </th>
                                    <th style="width: 5%;">No</th>
                                    <th>Loan Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th style="width: 10%;">Action</th>
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
{{-- Assuming there might be a separate modal for payments if needed, or it's inside index --}}

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
@endpush