@extends('layouts.main')

@section('title', 'Emergency Fund')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Emergency Fund</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Emergency Fund</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
             <!-- Total Card -->
             <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border-left: 5px solid #198754; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="text-secondary text-uppercase fw-bold mb-0" style="font-size: 0.75rem; letter-spacing: 1.2px;">Total Emergency Fund</h6>
                                <span class="badge bg-success bg-opacity-10 text-success ms-2 border border-success border-opacity-25" style="font-weight: 500;">Secured</span>
                            </div>
                            <h1 class="display-6 fw-bold mb-0 text-dark" style="color: #012970;" id="totalDanaDarurat">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h1>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small fw-medium">Target achievement progress</span>
                                    <span class="text-dark small fw-bold">{{ $percentage }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">Target: <span class="text-dark fw-semibold">Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</span></small>
                                    <small class="text-muted italic">Prepared for the unexpected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-4 p-4 d-inline-flex">
                                <i class="bi bi-shield-check fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">Emergency Fund Transaction List</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Manage history of emergency fund additions or reductions.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAturTarget">
                            <i class="bi bi-gear me-1"></i> Set Target
                        </button>
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Delete Selected (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('dana-darurat.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Data
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="danaDaruratTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr style="border-bottom: 2px solid #edf2f9;">
                                    <th style="width: 5%;" class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll" style="cursor: pointer;">
                                        </div>
                                    </th>
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">No</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Date</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Type</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Amount</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Note</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Created</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">Updated</th>
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

@include('modal.dana_darurat.index')
@include('dana_darurat.target_modal')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endpush
