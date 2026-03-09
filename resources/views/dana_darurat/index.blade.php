@extends('layouts.main')

@section('title', __('Emergency Fund'))

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
</style>
@endpush

@section('container')

<style>
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
    .balance-amount {
        color: #f5576c;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
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
        box-shadow: 0 8px 16px rgba(245, 87, 108, 0.4);
    }

    @media (max-width: 767.98px) {
        .fab-add {
            display: flex;
        }
        .btn-add-desktop {
            display: none;
        }
        
        #danaDaruratTable, 
        #danaDaruratTable thead, 
        #danaDaruratTable tbody, 
        #danaDaruratTable th, 
        #danaDaruratTable td, 
        #danaDaruratTable tr { 
            display: block; 
        }

        /* Hide table headers */
        #danaDaruratTable thead tr { 
            position: absolute;
            top: -9999px; left: -9999px;
        }

        #danaDaruratTable tr {
            border: 0;
            margin-bottom: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background-color: #fff;
            padding: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        #danaDaruratTable td {
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
        
        #danaDaruratTable td:last-child {
            border-bottom: 0;
        }

        #danaDaruratTable td:before { 
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
        #danaDaruratTable td:nth-of-type(1):before { content: "Select"; }
        #danaDaruratTable td:nth-of-type(2):before { content: "No"; }
        #danaDaruratTable td:nth-of-type(3):before { content: "Date"; }
        #danaDaruratTable td:nth-of-type(4):before { content: "Type"; }
        #danaDaruratTable td:nth-of-type(5):before { content: "Amount"; }
        #danaDaruratTable td:nth-of-type(6):before { content: "Note"; }
        #danaDaruratTable td:nth-of-type(9):before { content: "Action"; top: 1.1rem; }

        #danaDaruratTable td:nth-of-type(9) {
             padding-left: 10px;
             text-align: center;
             display: flex;
             justify-content: center;
             gap: 10px;
             border-bottom: 0;
        }
        #danaDaruratTable td:nth-of-type(9):before { 
            display: none;
        }
        
        /* Hide ID/Created/Updated on mobile */
        #danaDaruratTable td:nth-of-type(2), 
        #danaDaruratTable td:nth-of-type(7), 
        #danaDaruratTable td:nth-of-type(8) {
            display: none;
        }
    }

    [data-bs-theme="dark"] .card-summary {
        background: linear-gradient(135deg, #3d0b0b 0%, #611124 100%);
    }
    [data-bs-theme="dark"] #danaDaruratTable tr {
        background-color: #1e1e1e;
        border-color: rgba(255,255,255,0.05);
    }
</style>

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Emergency Fund') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Emergency Fund') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
             <div class="card card-summary mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <div>
                            <h5 class="card-title mb-1 fw-bold text-dark opacity-75" style="font-size: 1.1rem;">{{ __('Total Emergency Fund') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Prepared funds for your unexpected financial needs.') }}</p>
                        </div>
                        <div class="text-end">
                            <h2 class="balance-amount mb-0" id="totalDanaDarurat">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h2>
                        </div>
                    </div>

                    <div class="mt-2 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-medium">{{ __('Target achievement progress') }}</span>
                            <span class="text-dark small fw-bold"><span id="targetPercentage">{{ $percentage }}</span>%</span>
                        </div>
                        <div class="progress rounded-pill bg-light" style="height: 10px;">
                            <div class="progress-bar bg-danger rounded-pill" id="progressBar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">{{ __('Target') }}: <span class="text-dark fw-semibold" id="targetDanaDaruratAmount">Rp {{ number_format($targetDanaDarurat, 0, ',', '.') }}</span></small>
                            <small class="text-muted italic small text-end">{{ __('Balanced for your security') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Emergency Fund Transaction List') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage history of emergency fund additions or reductions.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAturTarget">
                            <i class="bi bi-gear me-1"></i> {{ __('Set Target') }}
                        </button>
                        <button id="btnBulkDelete" class="btn btn-outline-danger btn-sm d-none rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete') }} (<span id="countSelected">0</span>)
                        </button>
                        <a href="{{ route('dana-darurat.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm btn-add-desktop">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Data') }}
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
                                    <th style="width: 5%;" class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('No') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Date') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Type') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Amount') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Note') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Created') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Updated') }}</th>
                                    <th style="width: 10%;" class="text-center text-secondary small text-uppercase fw-bold py-3">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button for Mobile -->
    <a href="{{ route('dana-darurat.create') }}" class="btn btn-primary fab-add" title="{{ __('Add Data') }}">
        <i class="bi bi-plus-lg fs-2"></i>
    </a>
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
