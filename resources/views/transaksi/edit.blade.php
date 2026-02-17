@extends('layouts.main')

@section('title', 'Edit Transaction')

@section('container')

<div class="pagetitle mb-4">
    <h1>Edit Transaction</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Transactions</a></li>
            <li class="breadcrumb-item active">Edit</li>
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
                            <label for="tgl_transaksi" class="form-label fw-bold small text-uppercase text-muted">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="tgl_transaksi" name="tgl_transaksi" 
                                value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi) }}" required>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- Income Section -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white h-100" style="border-top: 4px solid #198754 !important;">
                                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-arrow-down-circle me-2"></i> Income</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pemasukan" class="form-label small text-muted">Category</label>
                                        <select class="form-select" id="pemasukan" name="pemasukan">
                                            <option value="">- Select Income -</option>
                                            @foreach ($pemasukan as $item)
                                            <option value="{{ $item->id }}" {{ old('pemasukan', $transaksi->pemasukan) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal_pemasukan" class="form-label small text-muted">Amount</label>
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
                                <div class="p-3 border rounded-3 bg-white h-100" style="border-top: 4px solid #dc3545 !important;">
                                    <h6 class="text-danger fw-bold mb-3"><i class="bi bi-arrow-up-circle me-2"></i> Expense</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pengeluaran" class="form-label small text-muted">Category</label>
                                        <select class="form-select" id="pengeluaran" name="pengeluaran">
                                            <option value="">- Select Expense -</option>
                                            @foreach ($pengeluaran as $item)
                                            <option value="{{ $item->id }}" {{ old('pengeluaran', $transaksi->pengeluaran) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal" class="form-label small text-muted">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal" name="nominal" class="form-control fw-bold text-danger" 
                                                placeholder="0" value="{{ old('nominal', $transaksi->nominal) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">Description / Notes</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional details about this transaction...">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light btn-lg border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> Update Transaction
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TomSelect
        if (typeof TomSelect !== 'undefined') {
             if(document.getElementById('pemasukan')) new TomSelect('#pemasukan', { allowEmptyOption: true, placeholder: '- Select Income -' });
             if(document.getElementById('pengeluaran')) new TomSelect('#pengeluaran', { allowEmptyOption: true, placeholder: '- Select Expense -' });
        }
    });
</script>
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endsection