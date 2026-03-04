@extends('layouts.main')

@section('title', 'Asset Detail')

@section('container')
<div class="pagetitle mb-4">
    <h1>Asset Detail: {{ $aset->nama_aset }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Asset Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                @if($aset->foto)
                <img src="{{ asset('storage/'.$aset->foto) }}" class="card-img-top" style="border-radius: 12px 12px 0 0; height: 250px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px; border-radius: 12px 12px 0 0;">
                    <i class="bi bi-box-seam display-1 text-secondary"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $aset->nama_aset }}</h5>
                    <div class="mb-3">
                        <span class="badge bg-primary rounded-pill">{{ $aset->kategori }}</span>
                        @php
                            $badgeClass = 'bg-success';
                            if($aset->kondisi == 'Kurang Baik') $badgeClass = 'bg-warning text-dark';
                            if($aset->kondisi == 'Rusak Berat') $badgeClass = 'bg-danger';
                            if($aset->kondisi == 'Hilang') $badgeClass = 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill">{{ $aset->kondisi }}</span>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">Code</span>
                            <span class="fw-bold">{{ $aset->kode_aset }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">Brand/Model</span>
                            <span class="fw-bold">{{ $aset->merk_model ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">Serial No</span>
                            <span class="fw-bold">{{ $aset->nomor_seri ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">Location</span>
                            <span class="fw-bold">{{ $aset->lokasi ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">PIC</span>
                            <span class="fw-bold">{{ $aset->pic ?: '-' }}</span>
                        </li>
                    </ul>
                    @if($aset->dokumen)
                    <div class="mt-3">
                        <a href="{{ asset('storage/'.$aset->dokumen) }}" target="_blank" class="btn btn-outline-info btn-sm w-100 rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> View Document
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial & Maintenance -->
        <div class="col-lg-8">
            <!-- Financial Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-primary"></i>Financial & Depreciation</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-md-4 border-end">
                            <p class="text-muted small mb-1">Purchase Price</p>
                            <h5 class="fw-bold text-dark">Rp {{ number_format($aset->harga_beli, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4 border-end">
                            <p class="text-muted small mb-1">Current Book Value</p>
                            <h5 class="fw-bold text-primary">Rp {{ number_format($aset->nilai_buku, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Monthly Depr.</p>
                            <h5 class="fw-bold text-danger">Rp {{ number_format($aset->penyusutan_bulanan, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="alert bg-light border-0 small">
                        <i class="bi bi-info-circle me-1 text-info"></i>
                        Depreciation is calculated using the <strong>Straight-Line Method</strong> over {{ $aset->masa_pakai }} years with a residual value of Rp {{ number_format($aset->nilai_sisa, 0, ',', '.') }}.
                    </div>
                    @if(!$aset->is_disposed)
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#disposeModal">
                            <i class="bi bi-trash-fill me-1"></i> Dispose Asset
                        </button>
                    </div>
                    @else
                    <div class="alert alert-warning py-2 mb-0">
                        <strong>Disposed:</strong> This asset was removed on {{ $aset->tanggal_disposal->format('d M Y') }} for: {{ $aset->alasan_disposal }}.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Maintenance Log -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-tools me-2 text-warning"></i>Maintenance History</h5>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Log
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3">Date</th>
                                    <th>Activity</th>
                                    <th>Technician</th>
                                    <th>Cost</th>
                                    <th class="text-end px-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aset->maintenance->sortByDesc('tanggal') as $log)
                                <tr>
                                    <td class="px-3 text-nowrap">{{ $log->tanggal->format('d M Y') }}</td>
                                    <td>{{ $log->kegiatan }}</td>
                                    <td>{{ $log->teknisi ?: '-' }}</td>
                                    <td class="text-nowrap">Rp {{ number_format($log->biaya, 0, ',', '.') }}</td>
                                    <td class="text-end px-3">
                                        <button class="btn btn-sm text-info" title="View details" onclick="showLogDetail('{{ $log->kegiatan }}', '{{ $log->keterangan }}')"><i class="bi bi-info-circle"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No maintenance records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('aset.maintenance.store', $aset->id) }}" method="POST" class="modal-content border-0">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Add Maintenance Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Maintenance Date</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Activity Name</label>
                    <input type="text" name="kegiatan" class="form-control" placeholder="e.g., Routine Service, Part Replacement" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Technician / Vendor</label>
                    <input type="text" name="teknisi" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="biaya" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Description</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Log</button>
            </div>
        </form>
    </div>
</div>

<!-- Disposal Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('aset.dispose', $aset->id) }}" method="POST" class="modal-content border-0">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Asset Disposal / Write-off</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Disposal Date</label>
                    <input type="date" name="tanggal_disposal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Reason for Disposal</label>
                    <select name="alasan_disposal" class="form-select" required>
                        <option value="">Select Reason</option>
                        <option value="Broken">Broken Beyond Repair</option>
                        <option value="Sold">Sold / Traded-in</option>
                        <option value="Donated">Donated / Gifted</option>
                        <option value="Lost">Lost / Stolen</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Sale/Disposal Value (if any)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="nilai_disposal" class="form-control" placeholder="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">Confirm Disposal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showLogDetail(title, desc) {
        Swal.fire({
            title: title,
            text: desc || 'No description available.',
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0d6efd',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        });
    }
</script>
@endpush
