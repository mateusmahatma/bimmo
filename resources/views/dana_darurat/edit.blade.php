@extends('layouts.main')

@section('title', 'Edit Emergency Fund')

@section('container')

<div class="pagetitle mb-4">
    <h1>Edit Emergency Fund</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dana-darurat.index') }}">Emergency Fund</a></li>
            <li class="breadcrumb-item active">Edit Data</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">Edit Emergency Fund Data</h5>
                    <p class="text-muted small mb-0 mt-1">Modify the details for this emergency fund transaction.</p>
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

                    <form action="{{ route('dana-darurat.update', $dana->id_dana_darurat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="tgl_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">Transaction Date <span class="text-danger">*</span></label>
                            <input name="tgl_transaksi_dana_darurat" type="date" class="form-control form-control-lg" id="tgl_transaksi_dana_darurat" 
                                value="{{ old('tgl_transaksi_dana_darurat', $dana->tgl_transaksi_dana_darurat) }}" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="jenis_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">Transaction Type <span class="text-danger">*</span></label>
                                <select name="jenis_transaksi_dana_darurat" id="jenis_transaksi_dana_darurat" class="form-select form-select-lg" required>
                                    <option value="1" {{ old('jenis_transaksi_dana_darurat', $dana->status) == '1' ? 'selected' : '' }}>Deposit</option>
                                    <option value="2" {{ old('jenis_transaksi_dana_darurat', $dana->status) == '2' ? 'selected' : '' }}>Withdrawal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="nominal_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">Amount (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="number" class="form-control" name="nominal_dana_darurat" id="nominal_dana_darurat" placeholder="0"
                                        value="{{ old('nominal_dana_darurat', $dana->nominal_dana_darurat) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">Note</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="Add a note...">{{ old('keterangan', $dana->keterangan)}}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('dana-darurat.index') }}" class="btn btn-light btn-lg px-4 rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
