@extends('layouts.main')

@section('title', __('Emergency Fund'))

@push('css')
<link rel="stylesheet" href="{{ asset('vendors/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dana_darurat.css') }}">
@endpush

@section('container')


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
<script src="{{ asset('vendors/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/dana-darurat.js') }}?v={{ filemtime(public_path('js/dana-darurat.js')) }}"></script>
@endpush
