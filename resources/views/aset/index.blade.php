@extends('layouts.main')

@section('title', __('Asset Inventory'))

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    /* Header Enhancements */
    /* Responsive Fab & Table */
    /* PWA & Premium Enhancements (White Theme) */
    .card-summary {
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
        color: #2d3436;
        overflow: hidden;
        position: relative;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .status-badge {
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .bg-baik {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .bg-kurang {
        background-color: #fff3cd;
        color: #664d03;
    }

    .bg-rusak {
        background-color: #f8d7da;
        color: #842029;
    }

    .bg-hilang {
        background-color: #e2e3e5;
        color: #41464b;
    }

    .fab-add {
        position: fixed;
        bottom: 2rem;
        right: 1.5rem;
        z-index: 1040;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: none;
        /* Desktop hidden */
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
    }

    @media (max-width: 767.98px) {
        .fab-add {
            display: flex;
        }

        .btn-add-desktop {
            display: none;
        }

        #asetTable,
        #asetTable thead,
        #asetTable tbody,
        #asetTable th,
        #asetTable td,
        #asetTable tr {
            display: block;
        }

        /* Hide table headers */
        #asetTable thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #asetTable tr {
            border: 0;
            margin-bottom: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            padding: 15px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        #asetTable td {
            border: none;
            border-bottom: 1px solid #f8f9fa;
            position: relative;
            padding-left: 45%;
            padding-top: 1rem;
            padding-bottom: 1rem;
            text-align: right;
            white-space: normal;
            min-height: 3rem;
        }

        #asetTable td:last-child {
            border-bottom: 0;
        }

        #asetTable td:before {
            position: absolute;
            top: 1rem;
            left: 15px;
            width: 40%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        /* Column Labels */
        #asetTable td:nth-of-type(1):before {
            content: "Code";
        }

        #asetTable td:nth-of-type(2):before {
            content: "Asset Name";
        }

        #asetTable td:nth-of-type(3):before {
            content: "Category";
        }

        #asetTable td:nth-of-type(4):before {
            content: "Location";
        }

        #asetTable td:nth-of-type(5):before {
            content: "Purchased";
        }

        #asetTable td:nth-of-type(6):before {
            content: "Value";
        }

        #asetTable td:nth-of-type(7):before {
            content: "Condition";
        }

        #asetTable td:nth-of-type(8):before {
            content: "Action";
            top: 1.1rem;
        }

        /* Special handling for Action cell */
        #asetTable td:nth-of-type(8) {
            padding-left: 10px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 10px;
            border-bottom: 0;
        }

        #asetTable td:nth-of-type(8):before {
            display: none;
        }
    }

    [data-bs-theme="dark"] .card-summary {
        background: linear-gradient(135deg, #2d1b4e 0%, #1a237e 100%);
    }

    [data-bs-theme="dark"] #asetTable tr {
        background-color: #1e1e1e;
        border-color: rgba(255, 255, 255, 0.05);
    }
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
    <div class="row">
        <!-- Summary Card -->
        <div class="col-lg-12 mb-4">
            <div class="card card-summary">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark opacity-75" style="font-size: 1.1rem;">{{ __('Asset Summary') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Overview of your physical and digital belongings.') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="small mb-0 text-muted d-md-none text-start">{{ __('Total Items') }}</p>
                        <h2 class="fw-bold mb-0 text-primary" id="totalAssetCount">--</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <select id="filterKondisi" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="">{{ __('All Conditions') }}</option>
                            <option value="Baik">{{ __('Good') }}</option>
                            <option value="Kurang Baik">{{ __('Fair') }}</option>
                            <option value="Rusak Berat">{{ __('Bad') }}</option>
                            <option value="Hilang">{{ __('Lost') }}</option>
                        </select>
                        <select id="filterKategori" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="">{{ __('All Categories') }}</option>
                            <option value="IT">IT</option>
                            <option value="Kendaraan">{{ __('Vehicle') }}</option>
                            <option value="Furnitur">{{ __('Furniture') }}</option>
                            <option value="Elektronik">{{ __('Electronic') }}</option>
                            <option value="Investasi / Emas">{{ __('Investment / Gold') }}</option>
                            <option value="Lainnya">{{ __('Others') }}</option>
                        </select>
                        <select id="filterStatus" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="active">{{ __('Active Inventory') }}</option>
                            <option value="disposed">{{ __('Disposed Assets') }}</option>
                        </select>
                    </div>
                    <div class="btn-add-desktop">
                        <a href="{{ route('aset.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Asset') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table id="asetTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Code') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Asset Name') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Category') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Location') }}</th>
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

    <!-- Floating Action Button for Mobile -->
    <a href="{{ route('aset.create') }}" class="btn btn-primary fab-add" title="{{ __('Add Asset') }}">
        <i class="bi bi-plus-lg fs-2"></i>
    </a>
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
                data: function(d) {
                    d.kondisi = $('#filterKondisi').val();
                    d.kategori = $('#filterKategori').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [{
                    data: 'kode_aset',
                    name: 'kode_aset'
                },
                {
                    data: 'nama_aset',
                    name: 'nama_aset'
                },
                {
                    data: 'kategori',
                    name: 'kategori'
                },
                {
                    data: 'lokasi',
                    name: 'lokasi'
                },
                {
                    data: 'tanggal_pembelian',
                    name: 'tanggal_pembelian'
                },
                {
                    data: 'nilai_buku',
                    name: 'nilai_buku',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kondisi',
                    name: 'kondisi',
                    render: function(data) {
                        let badgeClass = 'bg-baik';
                        if (data == 'Kurang Baik') badgeClass = 'bg-kurang';
                        if (data == 'Rusak Berat') badgeClass = 'bg-rusak';
                        if (data == 'Hilang') badgeClass = 'bg-hilang';
                        return `<span class="status-badge ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
            ],
            language: {
                paginate: {
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                }
            },
            drawCallback: function(settings) {
                // Update total count in summary card from dataTables total records
                $('#totalAssetCount').text(settings._iRecordsTotal);
            }
        });

        $('#filterKondisi, #filterKategori, #filterStatus').change(function() {
            table.draw();
        });

        // AJAX Delete Handler
        $('body').on('click', '.delete-aset', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            window.confirmAction({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Deleted data cannot be recovered!") }}',
                onConfirm: async () => {
                    try {
                        await $.ajax({
                            url: `/aset/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        showToast('{{ __("Asset has been deleted successfully.") }}', 'success');
                        table.ajax.reload(null, false);
                    } catch (e) {
                        showToast('{{ __("Something went wrong while deleting the asset.") }}', 'danger');
                    }
                }
            });
        });
    });
</script>
@endpush