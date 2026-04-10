@extends('layouts.main')

@section('title', __('Edit Transaction'))

@section('container')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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

    .ts-control {
        border-radius: 0.5rem !important;
        padding: 0.6rem 1rem !important;
    }

    /* CKEditor Dark Mode Fix */
    [data-bs-theme="dark"] .ck-editor__main > .ck-editor__editable {
        background-color: #1e1e1e !important;
        color: #ffffff !important;
        border-color: #444 !important;
    }

    [data-bs-theme="dark"] .ck.ck-toolbar {
        background-color: #2d2d2d !important;
        border-color: #444 !important;
    }

    [data-bs-theme="dark"] .ck.ck-toolbar .ck-toolbar__items .ck-button {
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .ck.ck-toolbar .ck-toolbar__items .ck-button:hover {
        background-color: #3d3d3d !important;
    }

    [data-bs-theme="dark"] .ck.ck-toolbar .ck-toolbar__items .ck-button.ck-on {
        background-color: #4d4d4d !important;
    }

    [data-bs-theme="dark"] .ck.ck-list {
        background-color: #2d2d2d !important;
    }

    [data-bs-theme="dark"] .ck.ck-list__item .ck-button {
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .ck.ck-list__item .ck-button:hover {
        background-color: #3d3d3d !important;
    }

    /* Income/Expense Card Dark Mode */
    [data-bs-theme="dark"] .income-expense-card {
        background-color: #1e1e1e !important;
        border-color: #2d2d2d !important;
    }
</style>
@endpush

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Edit Transaction') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">{{ __('Transactions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-dashboard">
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

                    @php
                        // Encode ID for the route
                        $hashId = \Vinkla\Hashids\Facades\Hashids::encode($transaksi->id);
                    @endphp

                    <form action="{{ route('transaksi.update', $hashId) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Date Section -->
                        <div class="mb-4">
                            <label for="tgl_transaksi" class="form-label fw-bold small text-uppercase text-muted">{{ __('Transaction Date') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="tgl_transaksi" name="tgl_transaksi" 
                                value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi) }}" required>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- Income Section -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white h-100 income-expense-card" style="border-top: 4px solid #198754 !important;">
                                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-arrow-down-circle me-2"></i> {{ __('Income') }}</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pemasukan" class="form-label small text-muted">{{ __('Category') }}</label>
                                        <select class="form-select" id="pemasukan" name="pemasukan">
                                            <option value="">- {{ __('Select Income') }} -</option>
                                            @foreach ($pemasukan as $item)
                                            <option value="{{ $item->id }}" {{ old('pemasukan', $transaksi->pemasukan) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal_pemasukan" class="form-label small text-muted">{{ __('Amount') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control fw-bold text-success" 
                                                placeholder="0" value="{{ old('nominal_pemasukan', $transaksi->nominal_pemasukan) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Section -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white h-100 income-expense-card" style="border-top: 4px solid #dc3545 !important;">
                                    <h6 class="text-danger fw-bold mb-3"><i class="bi bi-arrow-up-circle me-2"></i> {{ __('Expense') }}</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pengeluaran" class="form-label small text-muted">{{ __('Category') }}</label>
                                        <select class="form-select" id="pengeluaran" name="pengeluaran">
                                            <option value="">- {{ __('Select Expense') }} -</option>
                                            @foreach ($pengeluaran as $item)
                                            <option value="{{ $item->id }}" {{ old('pengeluaran', $transaksi->pengeluaran) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal" class="form-label small text-muted">{{ __('Amount') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal" name="nominal" class="form-control fw-bold text-danger" 
                                                placeholder="0" value="{{ old('nominal', $transaksi->nominal) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Selection -->
                        <div class="mb-4">
                            <label for="dompet_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Select Wallet') }}</label>
                            <select class="form-select form-select-lg" id="dompet_id" name="dompet_id">
                                <option value="">- {{ __('Select Wallet') }} -</option>
                                @foreach ($dompet as $d)
                                <option value="{{ $d->id }}" {{ old('dompet_id', $transaksi->dompet_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} (Rp {{ number_format((float)$d->saldo, 0, ',', '.') }})
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text small">{{ __('Changing the wallet will revert the balance update from the old wallet.') }}</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Description / Notes') }}</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="{{ __('Additional details about this transaction...') }}">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light btn-lg border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> {{ __('Update Transaction') }}
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="transaksi/update"]');
        let editorInstance;

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#keterangan'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote' ]
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        if (form) {
            form.addEventListener('submit', function() {
                if (editorInstance) {
                    editorInstance.updateSourceElement();
                }
            });
        }

        // Initialize TomSelect
        if (typeof TomSelect !== 'undefined') {
             if(document.getElementById('pemasukan')) new TomSelect('#pemasukan', { allowEmptyOption: true, placeholder: '- {{ __('Select Income') }} -' });
             if(document.getElementById('pengeluaran')) new TomSelect('#pengeluaran', { allowEmptyOption: true, placeholder: '- {{ __('Select Expense') }} -' });
        }
    });
</script>
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endpush