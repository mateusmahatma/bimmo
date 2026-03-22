@extends('layouts.main')

@section('title', __('Asset Detail'))

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
    <h1 class="fw-bold mb-1">{{ __('Asset Detail') }}: {{ $aset->nama_aset }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">{{ __('Assets') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Detail') }}</li>
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
                            <span class="text-muted">{{ __('Code') }}</span>
                            <span class="fw-bold">{{ $aset->kode_aset }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">{{ __('Brand/Model') }}</span>
                            <span class="fw-bold">{{ $aset->merk_model ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">{{ __('Serial No') }}</span>
                            <span class="fw-bold">{{ $aset->nomor_seri ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">{{ __('Location') }}</span>
                            <span class="fw-bold">{{ $aset->lokasi ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                            <span class="text-muted">{{ __('PIC') }}</span>
                            <span class="fw-bold">{{ $aset->pic ?: '-' }}</span>
                        </li>
                        @if($aset->kategori === 'Investasi / Emas')
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 mt-2 border-top">
                            <span class="text-muted"><i class="bi bi-heptagon-half text-warning me-1"></i> {{ __('Weight') }}</span>
                            <span class="fw-bold fs-5 text-dark">{{ $aset->berat }} g</span>
                        </li>
                        @endif
                    </ul>
                    @if($aset->dokumen)
                    <div class="mt-3">
                        <a href="{{ route('storage.aset_document', ['filename' => basename($aset->dokumen)]) }}" target="_blank" class="btn btn-outline-info btn-sm w-100 rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> {{ __('View Document') }}
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
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-primary"></i>{{ __('Financial & Depreciation') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-md-4 border-end">
                            <p class="text-muted small mb-1">{{ __('Purchase Price') }}</p>
                            <h5 class="fw-bold text-dark">Rp {{ number_format($aset->harga_beli, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4 border-end">
                            <p class="text-muted small mb-1">{{ __('Current Book Value') }}</p>
                            <h5 class="fw-bold text-primary">Rp {{ number_format($aset->nilai_buku, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">{{ __('Monthly Depr.') }}</p>
                            <h5 class="fw-bold text-danger">Rp {{ number_format($aset->penyusutan_bulanan, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="alert bg-light border-0 small">
                        <i class="bi bi-info-circle me-1 text-info"></i>
                        @if($aset->kategori === 'Investasi / Emas')
                            {!! __('Gold Book Value is updated automatically based on current live market price (Rp :livePrice / gram). No depreciation applied.', [
                                'livePrice' => number_format(\App\Services\GoldPriceService::getPricePerGram(), 0, ',', '.')
                            ]) !!}
                            <hr class="my-2">
                            <div class="d-flex align-items-center opacity-75" style="font-size: 0.75rem;">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                <span>
                                    {{ __('Note: This value is based on global spot prices (XAU/USD) from') }} 
                                    <a href="https://www.goldapi.io" target="_blank" class="text-decoration-none fw-bold">GoldAPI.io</a> 
                                    {{ __('and converted to IDR via') }}
                                    <a href="https://open.er-api.com" target="_blank" class="text-decoration-none fw-bold">Exchange Rate API</a>.
                                    {{ __('Prices may vary from local physical gold stores (e.g., Antam, Pegadaian).') }}
                                </span>
                            </div>
                        @else
                            {!! __('Depreciation is calculated using the :method over :years years with a residual value of Rp :residual.', [
                                'method' => '<strong>' . __('Straight-Line Method') . '</strong>',
                                'years' => $aset->masa_pakai,
                                'residual' => number_format($aset->nilai_sisa, 0, ',', '.')
                            ]) !!}
                        @endif
                    </div>
                    @if(!$aset->is_disposed)
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#disposeModal">
                            <i class="bi bi-trash-fill me-1"></i> {{ __('Dispose Asset') }}
                        </button>
                    </div>
                    @else
                    <div class="alert alert-warning py-2 mb-0">
                        <strong>{{ __('Disposed') }}:</strong> {{ __('This asset was removed on :date for: :reason.', ['date' => $aset->tanggal_disposal->format('d M Y'), 'reason' => $aset->alasan_disposal]) }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Maintenance Log -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-tools me-2 text-warning"></i>{{ __('Maintenance History') }}</h5>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Log') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3">{{ __('Date') }}</th>
                                    <th>{{ __('Activity') }}</th>
                                    <th>{{ __('Technician') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th class="text-end px-3">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aset->maintenance->sortByDesc('tanggal') as $log)
                                <tr>
                                    <td class="px-3 text-nowrap">{{ $log->tanggal->format('d M Y') }}</td>
                                    <td>{{ $log->kegiatan }}</td>
                                    <td>{{ $log->teknisi ?: '-' }}</td>
                                    <td class="text-nowrap">Rp {{ number_format($log->biaya, 0, ',', '.') }}</td>
                                    <td class="text-end px-3 text-nowrap">
                                        @if($log->dokumen)
                                        <a href="{{ route('storage.maintenance_document', ['filename' => basename($log->dokumen)]) }}" target="_blank" class="btn btn-sm text-secondary" title="{{ __('View Document') }}"><i class="bi bi-file-earmark-arrow-down"></i></a>
                                        @endif
                                        <button class="btn btn-sm text-info" title="{{ __('View details') }}" onclick="showLogDetail('{{ $log->kegiatan }}', '{{ $log->keterangan }}')"><i class="bi bi-info-circle"></i></button>
                                        <button class="btn btn-sm text-primary" title="{{ __('Edit') }}" onclick="editMaintenance({{ $log->id }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm text-danger" title="{{ __('Delete') }}" onclick="deleteMaintenance({{ $log->id }})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No maintenance records found.') }}</td>
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
        <form action="{{ route('aset.maintenance.store', $aset->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">{{ __('Add Maintenance Log') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Maintenance Date') }}</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Activity Name') }}</label>
                    <input type="text" name="kegiatan" class="form-control" placeholder="e.g., Routine Service, Part Replacement" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Technician / Vendor') }}</label>
                    <input type="text" name="teknisi" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Cost') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="biaya" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Description') }}</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Document') }} (JPG, PNG, PDF)</label>
                    <input type="file" name="dokumen" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">{{ __('Save Log') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Maintenance Modal -->
<div class="modal fade" id="editMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editMaintenanceForm" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">{{ __('Edit Maintenance Log') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Maintenance Date') }}</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Activity Name') }}</label>
                    <input type="text" name="kegiatan" id="edit_kegiatan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Technician / Vendor') }}</label>
                    <input type="text" name="teknisi" id="edit_teknisi" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Cost') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="biaya" id="edit_biaya" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Description') }}</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Document') }} (JPG, PNG, PDF)</label>
                    <input type="file" name="dokumen" class="form-control">
                    <div id="edit_dokumen_preview" class="mt-2 small text-muted"></div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">{{ __('Update Log') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Maintenance Form -->
<form id="deleteMaintenanceForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>


<!-- Disposal Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('aset.dispose', $aset->id) }}" method="POST" class="modal-content border-0">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">{{ __('Asset Disposal / Write-off') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Disposal Date') }}</label>
                    <input type="date" name="tanggal_disposal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Reason for Disposal') }}</label>
                    <select name="alasan_disposal" class="form-select" required>
                        <option value="">{{ __('Select Reason') }}</option>
                        <option value="Broken">{{ __('Broken Beyond Repair') }}</option>
                        <option value="Sold">{{ __('Sold / Traded-in') }}</option>
                        <option value="Donated">{{ __('Donated / Gifted') }}</option>
                        <option value="Lost">{{ __('Lost / Stolen') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">{{ __('Sale/Disposal Value (if any)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="nilai_disposal" class="form-control" placeholder="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">{{ __('Confirm Disposal') }}</button>
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
            text: desc || "{{ __('No description available.') }}",
            icon: 'info',
            confirmButtonText: "{{ __('OK') }}",
            confirmButtonColor: '#0d6efd',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        });
    }

    function editMaintenance(id) {
        fetch(`/aset/maintenance/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                const editForm = document.getElementById('editMaintenanceForm');
                editForm.action = `/aset/maintenance/${id}`;
                
                // Format date for input: YYYY-MM-DD
                const date = data.tanggal ? data.tanggal.split('T')[0] : '';
                
                document.getElementById('edit_tanggal').value = date;
                document.getElementById('edit_kegiatan').value = data.kegiatan;
                document.getElementById('edit_teknisi').value = data.teknisi || '';
                document.getElementById('edit_biaya').value = Math.round(data.biaya); // Use integer for cost input
                document.getElementById('edit_keterangan').value = data.keterangan || '';
                
                const preview = document.getElementById('edit_dokumen_preview');
                if (data.dokumen) {
                    preview.innerHTML = `<i class="bi bi-file-check me-1"></i> ${data.dokumen.split('/').pop()} <span class="text-danger small">(upload again to replace)</span>`;
                } else {
                    preview.innerHTML = '';
                }
                
                const modal = new bootstrap.Modal(document.getElementById('editMaintenanceModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching maintenance log details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ __("Failed to fetch data") }}'
                });
            });
    }

    function deleteMaintenance(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('You will not be able to revert this!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, delete it!') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteMaintenanceForm');
                form.action = `/aset/maintenance/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
