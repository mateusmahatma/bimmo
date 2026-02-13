@extends('layouts.main')

@section('title', 'Add Transaction')

@section('container')

<div class="pagetitle mb-4">
    <h1>Add Transaction</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Transactions</a></li>
            <li class="breadcrumb-item active">Add New</li>
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

                    <form action="{{ route('transaksi.store') }}" method="POST">
                        @csrf
                        
                        <!-- Date Section -->
                        <div class="mb-4">
                            <label for="tgl_transaksi" class="form-label fw-bold small text-uppercase text-muted">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="tgl_transaksi" name="tgl_transaksi" value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi ?? date('Y-m-d')) }}" required>
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
                                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal_pemasukan" class="form-label small text-muted">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control fw-bold text-success" placeholder="0">
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
                                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal" class="form-label small text-muted">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal" name="nominal" class="form-control fw-bold text-danger" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">Description / Notes</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional details about this transaction..."></textarea>
                        </div>

                        <!-- Advanced Options -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center" role="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                                <span class="fw-bold text-muted small"><i class="bi bi-gear-fill me-2"></i> Advanced Options (Assets & Emergency Fund)</span>
                                <i class="bi bi-chevron-down text-muted"></i>
                            </div>
                            <div class="collapse show" id="advancedOptions">
                                <div class="card-body pt-0">
                                    
                                    <!-- Asset Option -->
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="checkAssetList" name="kategori[]" value="asset_list">
                                        <label class="form-check-label fw-medium" for="checkAssetList">Add to Asset List</label>
                                    </div>
                                    <div class="ms-4 mb-3 ps-3 border-start border-primary" id="selectBarangContainer" style="display: none;">
                                        <label for="barang_id" class="form-label small text-muted">Select Asset Item</label>
                                        <select class="form-select w-100" id="barang_id" name="barang_id">
                                            <option value="">- Select Asset -</option>
                                            @foreach ($barang as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr class="text-muted opacity-25">

                                    <!-- Emergency Fund Option -->
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="checkEmergencyFund" name="kategori[]" value="emergency_fund">
                                        <label class="form-check-label fw-medium" for="checkEmergencyFund">Add/Withdraw Emergency Fund</label>
                                    </div>
                                    <div class="ms-4 mb-3 ps-3 border-start border-danger" id="danaDaruratContainer" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Type</label>
                                                <select name="jenis_transaksi_dana_darurat" class="form-select">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="1">Fund In</option>
                                                    <option value="2">Fund Out</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Amount</label>
                                                <input type="number" name="nominal_dana_darurat" class="form-control" placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Note</label>
                                                <textarea name="keterangan_dana_darurat" class="form-control" rows="1" placeholder="Note..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> Save Transaction
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
        // Toggle Logic for Advanced Options
        
        // Assets
        const checkAsset = document.getElementById('checkAssetList');
        const assetContainer = document.getElementById('selectBarangContainer');
        if(checkAsset) {
            checkAsset.addEventListener('change', function() {
                // Determine if checked
                const isChecked = this.checked;
                assetContainer.style.display = isChecked ? 'block' : 'none';
            });
        }

        // Emergency Fund
        const checkEmergency = document.getElementById('checkEmergencyFund');
        const emergencyContainer = document.getElementById('danaDaruratContainer');

        if(checkEmergency) {
             checkEmergency.addEventListener('change', function() {
                const isChecked = this.checked;
                emergencyContainer.style.display = isChecked ? 'block' : 'none';
            });
        }

        // Initialize TomSelect if validation didn't fail and elements exist
        if (typeof TomSelect !== 'undefined') {
             if(document.getElementById('pemasukan')) new TomSelect('#pemasukan', { allowEmptyOption: true, placeholder: '- Select Income -' });
             if(document.getElementById('pengeluaran')) new TomSelect('#pengeluaran', { allowEmptyOption: true, placeholder: '- Select Expense -' });
             if(document.getElementById('barang_id')) new TomSelect('#barang_id', { allowEmptyOption: true, placeholder: '- Select Asset -' });
        }
    });
</script>
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endsection