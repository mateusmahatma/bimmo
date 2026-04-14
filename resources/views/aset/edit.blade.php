@extends('layouts.main')

@section('title', __('Edit Asset'))

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
    <h1 class="fw-bold mb-1">{{ __('Edit Asset') }}: {{ $aset->nama_aset }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">{{ __('Assets') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Asset') }}</li>
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
                                <label class="form-label fw-bold">{{ __('Asset Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', $aset->kode_aset) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Asset Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">{{ __('Select Category') }}</option>
                                    <option value="IT" {{ (old('kategori', $aset->kategori) == 'IT') ? 'selected' : '' }}>IT</option>
                                    <option value="Kendaraan" {{ (old('kategori', $aset->kategori) == 'Kendaraan') ? 'selected' : '' }}>{{ __('Vehicle') }}</option>
                                    <option value="Furnitur" {{ (old('kategori', $aset->kategori) == 'Furnitur') ? 'selected' : '' }}>{{ __('Furniture') }}</option>
                                    <option value="Elektronik" {{ (old('kategori', $aset->kategori) == 'Elektronik') ? 'selected' : '' }}>{{ __('Electronic') }}</option>
                                    <option value="Investasi / Emas" {{ (old('kategori', $aset->kategori) == 'Investasi / Emas') ? 'selected' : '' }}>{{ __('Investment / Gold') }}</option>
                                    <option value="Lainnya" {{ (old('kategori', $aset->kategori) == 'Lainnya') ? 'selected' : '' }}>{{ __('Others') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="berat_container" style="display: none;">
                                <label class="form-label fw-bold">{{ __('Weight (Grams)') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="berat" id="berat" class="form-control" value="{{ old('berat', $aset->berat) }}">
                                    <span class="input-group-text">g</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Brand / Model') }}</label>
                                <input type="text" name="merk_model" class="form-control" value="{{ old('merk_model', $aset->merk_model) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Serial Number') }}</label>
                                <input type="text" name="nomor_seri" class="form-control" value="{{ old('nomor_seri', $aset->nomor_seri) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Purchase Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembelian" class="form-control" value="{{ old('tanggal_pembelian', $aset->tanggal_pembelian->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Purchase Price') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli', (int)$aset->harga_beli) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4" id="masa_pakai_container">
                                <label class="form-label fw-bold">{{ __('Useful Life (Years)') }} <span class="text-danger">*</span></label>
                                <input type="number" name="masa_pakai" id="masa_pakai" class="form-control" value="{{ old('masa_pakai', $aset->masa_pakai) }}" required>
                            </div>
                            <div class="col-md-4" id="nilai_sisa_container">
                                <label class="form-label fw-bold">{{ __('Residual Value (Nilai Sisa)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_sisa" class="form-control" value="{{ old('nilai_sisa', (int)$aset->nilai_sisa) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Warranty Until') }}</label>
                                <input type="date" name="garansi_sampai" class="form-control" value="{{ old('garansi_sampai', $aset->garansi_sampai ? $aset->garansi_sampai->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('Condition') }} <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-select" required>
                                    <option value="Baik" {{ (old('kondisi', $aset->kondisi) == 'Baik') ? 'selected' : '' }}>{{ __('Good') }}</option>
                                    <option value="Kurang Baik" {{ (old('kondisi', $aset->kondisi) == 'Kurang Baik') ? 'selected' : '' }}>{{ __('Fair') }}</option>
                                    <option value="Rusak Berat" {{ (old('kondisi', $aset->kondisi) == 'Rusak Berat') ? 'selected' : '' }}>{{ __('Bad') }}</option>
                                    <option value="Hilang" {{ (old('kondisi', $aset->kondisi) == 'Hilang') ? 'selected' : '' }}>{{ __('Lost') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Location') }}</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $aset->lokasi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('PIC (Person in Charge)') }}</label>
                                <input type="text" name="pic" class="form-control" value="{{ old('pic', $aset->pic) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Asset Photo') }}</label>
                                @if($aset->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$aset->foto) }}" class="img-thumbnail" style="height: 100px;">
                                </div>
                                @endif
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Purchase Invoice / Warranty Doc') }}</label>
                                @if($aset->dokumen)
                                <div class="mb-2">
                                    <a href="{{ route('storage.aset_document', ['filename' => basename($aset->dokumen)]) }}" target="_blank" class="btn btn-sm btn-outline-info">{{ __('View Existing Document') }}</a>
                                </div>
                                @endif
                                <input type="file" name="dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm" onclick="confirmDelete()">{{ __('Delete Asset') }}</button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('aset.index') }}" class="btn btn-light rounded-pill px-4">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">{{ __('Update Asset') }}</button>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kategoriSelect = document.querySelector('select[name="kategori"]');
        const beratContainer = document.getElementById('berat_container');
        const masaPakaiContainer = document.getElementById('masa_pakai_container');
        const nilaiSisaContainer = document.getElementById('nilai_sisa_container');
        const beratInput = document.getElementById('berat');
        const masaPakaiInput = document.getElementById('masa_pakai');

        function toggleFields() {
            if (kategoriSelect.value === 'Investasi / Emas') {
                beratContainer.style.display = 'block';
                beratInput.setAttribute('required', 'required');
                
                masaPakaiContainer.style.display = 'none';
                masaPakaiInput.removeAttribute('required');
                nilaiSisaContainer.style.display = 'none';
            } else {
                beratContainer.style.display = 'none';
                beratInput.removeAttribute('required');
                
                masaPakaiContainer.style.display = 'block';
                masaPakaiInput.setAttribute('required', 'required');
                nilaiSisaContainer.style.display = 'block';
            }
        }

        // Run on load to handle old() values or page refresh
        toggleFields();

        // Run on change
        kategoriSelect.addEventListener('change', toggleFields);
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        window.confirmAction({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Deleted data cannot be recovered!") }}',
            onConfirm: () => {
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
@endpush
