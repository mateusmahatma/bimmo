@extends('layouts.main')

@section('title', __('Add Income Category'))

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
    <h1 class="fw-bold mb-1">{{ __('Add Income Category') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pemasukan.index') }}">{{ __('Income') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Add New') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">{{ __('New Category Details') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Specify the name for your new income category.') }}</p>
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

                    <form action="{{ route('pemasukan.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nama" class="form-label fw-bold small text-uppercase text-muted">{{ __('Category Name') }} <span class="text-danger">*</span></label>
                            <input name="nama" type="text" class="form-control form-control-lg" id="nama" placeholder="{{ __('e.g. Salary, Freelance, Investment') }}"
                                value="{{ old('nama') }}" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('pemasukan.index') }}" class="btn btn-light btn-lg px-4 rounded-pill">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> {{ __('Save Category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/pemasukan.js') }}?v={{ filemtime(public_path('js/pemasukan.js')) }}"></script>
@endpush