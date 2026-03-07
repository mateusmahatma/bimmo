@extends('layouts.main')

@section('title', __('Asset Inventory'))

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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

    .aset-card { transition: all 0.3s ease; border: none; border-radius: 12px; }
    .aset-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .status-badge { border-radius: 20px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; }
    .bg-baik { background-color: #d1e7dd; color: #0f5132; }
    .bg-kurang { background-color: #fff3cd; color: #664d03; }
    .bg-rusak { background-color: #f8d7da; color: #842029; }
    .bg-hilang { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Asset Inventory') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Assets') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex gap-2">
                        <select id="filterKondisi" class="form-select form-select-sm" style="width: 150px;">
                            <option value="">{{ __('All Conditions') }}</option>
                            <option value="Baik">{{ __('Good') }}</option>
                            <option value="Kurang Baik">{{ __('Fair') }}</option>
                            <option value="Rusak Berat">{{ __('Bad') }}</option>
                            <option value="Hilang">{{ __('Lost') }}</option>
                        </select>
                        <select id="filterKategori" class="form-select form-select-sm" style="width: 150px;">
                            <option value="">{{ __('All Categories') }}</option>
                            <option value="IT">IT</option>
                            <option value="Kendaraan">{{ __('Vehicle') }}</option>
                            <option value="Furnitur">{{ __('Furniture') }}</option>
                            <option value="Elektronik">{{ __('Electronic') }}</option>
                            <option value="Lainnya">{{ __('Others') }}</option>
                        </select>
                        <select id="filterStatus" class="form-select form-select-sm" style="width: 150px;">
                            <option value="active">{{ __('Active Inventory') }}</option>
                            <option value="disposed">{{ __('Disposed Assets') }}</option>
                        </select>
                    </div>
                    <div>
                        <a href="{{ route('aset.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Asset') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table id="asetTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Code') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Asset Name') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Category') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Purchase Date') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Current Value (Book)') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Condition') }}</th>
                                    <th class="text-center text-secondary small text-uppercase fw-bold py-3">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var table = $('#asetTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('aset.index') }}",
                data: function (d) {
                    d.kondisi = $('#filterKondisi').val();
                    d.kategori = $('#filterKategori').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {data: 'kode_aset', name: 'kode_aset'},
                {data: 'nama_aset', name: 'nama_aset'},
                {data: 'kategori', name: 'kategori'},
                {data: 'tanggal_pembelian', name: 'tanggal_pembelian'},
                {data: 'nilai_buku', name: 'nilai_buku', orderable: false, searchable: false},
                {
                    data: 'kondisi', 
                    name: 'kondisi',
                    render: function(data) {
                        let badgeClass = 'bg-baik';
                        if(data == 'Kurang Baik') badgeClass = 'bg-kurang';
                        if(data == 'Rusak Berat') badgeClass = 'bg-rusak';
                        if(data == 'Hilang') badgeClass = 'bg-hilang';
                        return `<span class="status-badge ${badgeClass}">${data}</span>`;
                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: {
                paginate: {
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                }
            }
        });

        $('#filterKondisi, #filterKategori, #filterStatus').change(function() {
            table.draw();
        });

        // AJAX Delete Handler
        $('body').on('click', '.delete-aset', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This action cannot be undone!') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/aset/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: "{{ __('Deleted!') }}",
                                text: "{{ __('Asset has been deleted successfully.') }}",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: "{{ __('Oops...') }}",
                                text: "{{ __('Something went wrong while deleting the asset.') }}"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
