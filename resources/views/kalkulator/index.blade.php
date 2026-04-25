@extends('layouts.main')

@section('title', __('Budget Monitoring'))

@push('css')
<link href="{{ asset('css/kalkulator.css') }}?v={{ filemtime(public_path('css/kalkulator.css')) }}" rel="stylesheet">
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Budget Monitoring') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Budget Monitoring') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Proses Budget (By Periode) -->
        <div class="col-lg-12 mb-4">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Proses Budget') }}</h5>
                        <p class="text-muted small mb-0 mt-1">{{ __('Pilih periode anggaran yang sudah dibuat, lalu proses budget.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#prosesPeriodeModal" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-gear me-1"></i> {{ __('Proses Budget') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(empty($periods) || count($periods) === 0)
                    <div class="alert alert-warning mb-0">
                        {{ __('Belum ada periode anggaran. Silakan buat periode di menu Anggaran terlebih dahulu.') }}
                        <a class="ms-2" href="{{ route('anggaran.index') }}">{{ __('Buat Periode') }}</a>
                    </div>
                    @else
                    <div class="text-muted small">
                        {{ __('Klik tombol Proses Budget, pilih periode, lalu klik Proses.') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- History -->
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Budget Process History') }}</h5>
                        <p class="text-muted small mb-0 mt-1">{{ __('List of your budget calculation history.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm d-none rounded-pill" id="btnBulkSync" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('Sync Selected') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm d-none rounded-pill" id="btnBulkDelete" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }} (<span id="countSelected">0</span>)
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                        <div class="search-bar" style="min-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="historySearch" class="form-control bg-light border-start-0 rounded-end-pill shadow-none" style="font-size: 0.8rem;" placeholder="{{ __('Search history...') }}">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label for="historyPeriodeFilter" class="text-muted small mb-0">{{ __('Periode') }}</label>
                            <select id="historyPeriodeFilter" class="form-select form-select-sm" style="min-width: 260px;">
                                <option value="">{{ __('Semua') }}</option>
                                @foreach(($periods ?? []) as $p)
                                <option value="{{ $p->id_periode_anggaran }}" {{ (string) request('id_periode_anggaran') === (string) $p->id_periode_anggaran ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} ({{ optional($p->tanggal_mulai)->format('Y-m-d') }} — {{ optional($p->tanggal_selesai)->format('Y-m-d') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="history-table-container">
                        @include('kalkulator._table_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: Proses Budget by Periode -->
<div class="modal fade" id="prosesPeriodeModal" tabindex="-1" aria-labelledby="prosesPeriodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="prosesPeriodeModalLabel">{{ __('Proses Budget') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <label for="id_periode_anggaran" class="form-label fw-medium small text-uppercase text-muted required">{{ __('Pilih Periode Anggaran') }}</label>
                    <select id="id_periode_anggaran" class="form-select">
                        <option value="">{{ __('Pilih...') }}</option>
                        @foreach(($periods ?? []) as $p)
                        <option value="{{ $p->id_periode_anggaran }}">
                            {{ $p->nama_periode }} ({{ optional($p->tanggal_mulai)->format('Y-m-d') }} — {{ optional($p->tanggal_selesai)->format('Y-m-d') }})
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text small">{{ __('Periode ini akan digunakan sebagai rentang tanggal proses budget.') }}</div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                <button type="button" class="btn btn-primary px-4" id="btnProsesPeriode">
                    <span class="me-1"><i class="bi bi-gear"></i></span> {{ __('Proses') }}
                    <span id="btnProsesPeriodeSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.KALKULATOR_PROCESS_PERIODE_URL = @json(route('kalkulator.processPeriode'));
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush