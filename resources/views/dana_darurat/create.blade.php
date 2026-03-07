@extends('layouts.main')

@section('title', __('Add Emergency Fund'))

@push('css')
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

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Add Emergency Fund') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dana-darurat.index') }}">{{ __('Emergency Fund') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Add New') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">{{ __('Add Emergency Fund Data') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Fill in the details for your emergency fund transaction.') }}</p>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('dana-darurat.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="tgl_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Transaction Date') }} <span class="text-danger">*</span></label>
                            <input name="tgl_transaksi_dana_darurat" type="date" class="form-control form-control-lg" id="tgl_transaksi_dana_darurat" 
                                value="{{ old('tgl_transaksi_dana_darurat', date('Y-m-d')) }}" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="jenis_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Transaction Type') }} <span class="text-danger">*</span></label>
                                <select name="jenis_transaksi_dana_darurat" id="jenis_transaksi_dana_darurat" class="form-select form-select-lg" required>
                                    <option value="">-- {{ __('Select Status') }} --</option>
                                    <option value="1" {{ old('jenis_transaksi_dana_darurat') == '1' ? 'selected' : '' }}>{{ __('Deposit') }}</option>
                                    <option value="2" {{ old('jenis_transaksi_dana_darurat') == '2' ? 'selected' : '' }}>{{ __('Withdrawal') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="nominal_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Amount (Rp)') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="number" class="form-control" name="nominal_dana_darurat" id="nominal_dana_darurat" placeholder="0"
                                        value="{{ old('nominal_dana_darurat') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Note') }}</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="{{ __('Add a note...') }}"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('dana-darurat.index') }}" class="btn btn-light btn-lg px-4 rounded-pill">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> {{ __('Save Data') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
