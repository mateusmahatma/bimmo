@extends('layouts.main')

@section('title', 'Add New Budget')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Add New Budget</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('anggaran.index') }}">Budgets</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Budget Details</h5>
                    
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('anggaran.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Budget Name -->
                            <div class="col-12">
                                <label for="nama_anggaran" class="form-label fw-bold small text-uppercase text-muted">Budget Name</label>
                                <input type="text" class="form-control @error('nama_anggaran') is-invalid @enderror" id="nama_anggaran" name="nama_anggaran" value="{{ old('nama_anggaran') }}" placeholder="e.g., Monthly Grocery, Utilities" required>
                                @error('nama_anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Percentage -->
                            <div class="col-12">
                                <label for="persentase_anggaran" class="form-label fw-bold small text-uppercase text-muted">Wait, Percentage (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('persentase_anggaran') is-invalid @enderror" id="persentase_anggaran" name="persentase_anggaran" value="{{ old('persentase_anggaran') }}" placeholder="e.g., 20" min="0" max="100" step="0.01" required>
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                                @error('persentase_anggaran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Expense Types -->
                            <div class="col-12">
                                <label for="id_pengeluaran" class="form-label fw-bold small text-uppercase text-muted">Expense Types</label>
                                <select name="id_pengeluaran[]" id="id_pengeluaran" class="form-control" multiple placeholder="Select expense types..." required>
                                    @foreach ($pengeluarans as $pengeluaran)
                                    <option value="{{ $pengeluaran->id }}" {{ in_array($pengeluaran->id, (array) old('id_pengeluaran', [])) ? 'selected' : '' }}>
                                        {{ $pengeluaran->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">You can select multiple expense types for this budget.</div>
                                @error('id_pengeluaran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('anggaran.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save Budget
                                </button>
                            </div>
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
        const el = document.querySelector('#id_pengeluaran');
        if (el) {
            new TomSelect(el, {
                plugins: ['remove_button'],
                maxItems: null,
                hideSelected: true,
                closeAfterSelect: false,
                persist: false,
                create: false
            });
        }
    });
</script>
@endpush