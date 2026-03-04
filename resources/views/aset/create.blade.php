@extends('layouts.main')

@section('title', 'Add New Asset')

@section('container')
<div class="pagetitle mb-4">
    <h1>Add New Asset</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Add Asset</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
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

                    <form action="{{ route('aset.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Code <span class="text-danger">*</span></label>
                                <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', 'AST-'.time()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="IT" {{ old('kategori') == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="Kendaraan" {{ old('kategori') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                                    <option value="Furnitur" {{ old('kategori') == 'Furnitur' ? 'selected' : '' }}>Furnitur</option>
                                    <option value="Elektronik" {{ old('kategori') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                                    <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Brand / Model</label>
                                <input type="text" name="merk_model" class="form-control" value="{{ old('merk_model') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Serial Number</label>
                                <input type="text" name="nomor_seri" class="form-control" value="{{ old('nomor_seri') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembelian" class="form-control" value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Purchase Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Useful Life (Years) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_pakai" class="form-control" value="{{ old('masa_pakai', 5) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Residual Value (Nilai Sisa)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_sisa" class="form-control" value="{{ old('nilai_sisa', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Warranty Until</label>
                                <input type="date" name="garansi_sampai" class="form-control" value="{{ old('garansi_sampai') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Condition <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-select" required>
                                    <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Kurang Baik" {{ old('kondisi') == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik</option>
                                    <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                    <option value="Hilang" {{ old('kondisi') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">PIC (Person in Charge)</label>
                                <input type="text" name="pic" class="form-control" value="{{ old('pic') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Photo</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Purchase Invoice / Warranty Doc</label>
                                <input type="file" name="dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('aset.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Asset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
