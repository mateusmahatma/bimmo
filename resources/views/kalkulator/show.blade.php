@extends('layouts.main')

@section('title', 'Budget Monitoring Detail')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
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
    <h1 class="fw-bold mb-1">Budget Monitoring Detail</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kalkulator.index') }}">Budget Monitoring</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Overview Card -->
        <div class="col-lg-12">
            <div class="card-dashboard mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Budget Information</h5>
                        <a href="{{ route('kalkulator.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold" style="width: 140px;">Budget Name</td>
                                        <td class="fw-medium">: {{ $HasilProsesAnggaran->nama_anggaran }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold">Period</td>
                                        <td class="fw-medium">: 
                                            {{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_mulai)->locale('en')->isoFormat('D MMM Y') }} 
                                            to 
                                            {{ \Carbon\Carbon::parse($HasilProsesAnggaran->tanggal_selesai)->locale('en')->isoFormat('D MMM Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small text-uppercase fw-bold">Expense Type</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($namaPengeluaran as $index => $nama)
                                                    @if($index < 5)
                                                        <li><i class="bi bi-dot text-secondary"></i> {{ $nama }}</li>
                                                    @endif
                                                @endforeach
                                                @if(count($namaPengeluaran) > 5)
                                                    <li class="text-muted fst-italic ms-3 small">+{{ count($namaPengeluaran) - 5 }} others</li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Financial Summary</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Budget Amount:</span>
                                        <span class="fw-bold">Rp {{ number_format($HasilProsesAnggaran->nominal_anggaran, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Used:</span>
                                        <span class="fw-bold text-danger">Rp {{ number_format($HasilProsesAnggaran->anggaran_yang_digunakan, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Remaining Budget:</span>
                                        <div class="text-end">
                                            @php $sisa = $HasilProsesAnggaran->sisa_anggaran; @endphp
                                            <h5 class="mb-0 fw-bold {{ $sisa < 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($sisa, 0, ',', '.') }}
                                            </h5>
                                            @if ($sisa < 0)
                                                <span class="badge bg-danger-subtle text-danger small">Over Budget</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success small">Within Budget</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="col-lg-12">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Related Transaction Details</h5>
                    
                    <input type="hidden" id="kalkulator-id" value="{{ $HasilProsesAnggaran->hash }}">

                    <div class="table-responsive">
                        <table id="detailAnggaran" class="table table-hover table-borderless align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Date</th>
                                    <th>Expense Category</th>
                                    <th class="text-end">Amount</th>
                                    <th>Description</th>
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
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
<script src="{{ asset('js/kalkulator.js') }}?v={{ filemtime(public_path('js/kalkulator.js')) }}"></script>
@endpush