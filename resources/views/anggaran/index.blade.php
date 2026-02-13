@extends('layouts.main')

@section('title', 'Budget List')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    /* Custom style for summary card - clean corporate look */
    .card-summary {
        border-radius: 12px;
        border: 0;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }
    .check-item { cursor: pointer; }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Budget List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Budgets</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Summary / Percentage Report Card -->
        <div class="col-lg-12 mb-4">
            <div class="card card-summary shadow-sm">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark" style="font-size: 1.1rem;">Total Budget Allocation</h5>
                        <p class="text-muted small mb-0">Ensure the total allocation does not exceed 100%.</p>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-bold mb-0 text-primary" id="totalPersentase">0%</h2>
                        <span id="exceedMessage" class="badge bg-danger d-none mt-2">Exceeds 100%!</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">List of Budgets</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Manage your budget allocations efficiently.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('anggaran.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add New Budget
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="anggaranTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr style="border-bottom: 2px solid #edf2f9;">
                                    <th style="width: 5%;" class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                                        </div>
                                    </th>
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3">No</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Budget Name</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Percentage</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">Expense Types</th>
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

<!-- Include Modal -->
@include('modal.anggaran.index')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endpush