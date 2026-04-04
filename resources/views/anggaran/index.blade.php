@extends('layouts.main')

@section('title', __('Budget List'))

@push('css')
<link href="{{ asset('css/tom-select.bootstrap5.min.css') }}?v={{ filemtime(public_path('css/tom-select.bootstrap5.min.css')) }}" rel="stylesheet">
<link href="{{ asset('css/anggaran.css') }}?v={{ filemtime(public_path('css/anggaran.css')) }}" rel="stylesheet">
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
<x-anggaran-modal :pengeluarans="$pengeluarans" />

@endsection

@push('scripts')
<script src="{{ asset('js/jquery-3.7.0.min.js') }}?v={{ filemtime(public_path('js/jquery-3.7.0.min.js')) }}"></script>
<script src="{{ asset('js/vendor/tom-select.complete.min.js') }}?v={{ filemtime(public_path('js/vendor/tom-select.complete.min.js')) }}"></script>
<script src="{{ asset('js/vendor/sweetalert2.js') }}?v={{ filemtime(public_path('js/vendor/sweetalert2.js')) }}"></script>
<script src="{{ asset('js/anggaran.js') }}?v={{ filemtime(public_path('js/anggaran.js')) }}"></script>
@endpush