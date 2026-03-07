@extends('layouts.main')

@section('title', 'Edit Asset')

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
    <h1 class="fw-bold mb-1">Edit Asset: {{ $aset->nama_aset }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Edit Asset</li>
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

                    <form action="{{ route('aset.update', $aset->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Code <span class="text-danger">*</span></label>
                                <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', $aset->kode_aset) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="IT" {{ (old('kategori', $aset->kategori) == 'IT') ? 'selected' : '' }}>IT</option>
                                    <option value="Kendaraan" {{ (old('kategori', $aset->kategori) == 'Kendaraan') ? 'selected' : '' }}>Kendaraan</option>
                                    <option value="Furnitur" {{ (old('kategori', $aset->kategori) == 'Furnitur') ? 'selected' : '' }}>Furnitur</option>
                                    <option value="Elektronik" {{ (old('kategori', $aset->kategori) == 'Elektronik') ? 'selected' : '' }}>Elektronik</option>
                                    <option value="Lainnya" {{ (old('kategori', $aset->kategori) == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Brand / Model</label>
                                <input type="text" name="merk_model" class="form-control" value="{{ old('merk_model', $aset->merk_model) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Serial Number</label>
                                <input type="text" name="nomor_seri" class="form-control" value="{{ old('nomor_seri', $aset->nomor_seri) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembelian" class="form-control" value="{{ old('tanggal_pembelian', $aset->tanggal_pembelian->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Purchase Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli', (int)$aset->harga_beli) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Useful Life (Years) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_pakai" class="form-control" value="{{ old('masa_pakai', $aset->masa_pakai) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Residual Value (Nilai Sisa)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_sisa" class="form-control" value="{{ old('nilai_sisa', (int)$aset->nilai_sisa) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Warranty Until</label>
                                <input type="date" name="garansi_sampai" class="form-control" value="{{ old('garansi_sampai', $aset->garansi_sampai ? $aset->garansi_sampai->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Condition <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-select" required>
                                    <option value="Baik" {{ (old('kondisi', $aset->kondisi) == 'Baik') ? 'selected' : '' }}>Baik</option>
                                    <option value="Kurang Baik" {{ (old('kondisi', $aset->kondisi) == 'Kurang Baik') ? 'selected' : '' }}>Kurang Baik</option>
                                    <option value="Rusak Berat" {{ (old('kondisi', $aset->kondisi) == 'Rusak Berat') ? 'selected' : '' }}>Rusak Berat</option>
                                    <option value="Hilang" {{ (old('kondisi', $aset->kondisi) == 'Hilang') ? 'selected' : '' }}>Hilang</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $aset->lokasi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">PIC (Person in Charge)</label>
                                <input type="text" name="pic" class="form-control" value="{{ old('pic', $aset->pic) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asset Photo</label>
                                @if($aset->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$aset->foto) }}" class="img-thumbnail" style="height: 100px;">
                                </div>
                                @endif
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Purchase Invoice / Warranty Doc</label>
                                @if($aset->dokumen)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/'.$aset->dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">View Existing Document</a>
                                </div>
                                @endif
                                <input type="file" name="dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm" onclick="confirmDelete()">Delete Asset</button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('aset.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Update Asset</button>
                            </div>
                        </div>
                    </form>

                    <form id="deleteForm" action="{{ route('aset.destroy', $aset->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        })
    }
</script>
@endpush
