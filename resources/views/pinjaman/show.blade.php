@extends('layouts.main')

@section('title', __('Liability Details'))

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

    @media screen and (max-width: 768px) {
        #paymentHistoryTable, 
        #paymentHistoryTable thead, 
        #paymentHistoryTable tbody, 
        #paymentHistoryTable th, 
        #paymentHistoryTable td, 
        #paymentHistoryTable tr { 
            display: block; 
        }

        #paymentHistoryTable thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        #paymentHistoryTable tr { 
            border: 1px solid #eef2f7; 
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 0.5rem;
        }

        #paymentHistoryTable td { 
            border: none;
            border-bottom: 1px solid #f8f9fa; 
            position: relative;
            padding-left: 45%; 
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: right; 
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        #paymentHistoryTable td:last-child {
            border-bottom: 0;
            justify-content: center;
            padding-left: 0;
            margin-top: 0.5rem;
        }

        #paymentHistoryTable td:before { 
            position: absolute;
            left: 0.75rem; 
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            content: attr(data-label);
        }
        
        #paymentHistoryTable td[data-label="No"] {
            display: none;
        }
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Liability Details') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">{{ __('Liabilitys') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Details') }}</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<section class="section">
    <div class="row">
        <!-- Liability Information Card -->
        <div class="col-lg-12 mb-4">
            <div class="card-dashboard h-100 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4 text-dark" style="font-size: 1.1rem;">{{ __('Liability Information') }}</h5>
                    <div class="row g-4">
                        <div class="col-md-6 border-start border-primary border-4 py-1 ps-3">
                             <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px;">{{ __('Liability Name') }}</label>
                            <p class="fs-5 fw-bold text-dark mb-0">{{ $pinjaman->nama_pinjaman }}</p>
                        </div>
                        <div class="col-md-6 border-start border-success border-4 py-1 ps-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px;">{{ __('Current Balance') }}</label>
                            <p class="fs-5 fw-bold text-primary mb-0">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">{{ __('Duration') }}</label>
                            <p class="mb-0 fw-semibold">{{ $pinjaman->jangka_waktu }} {{ __('Months') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">{{ __('Monthly Installment') }}</label>
                            <p class="mb-0 fw-semibold text-warning">Rp {{ number_format($pinjaman->nominal_angsuran, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">{{ __('Status') }}</label>
                            @if ($pinjaman->status === 'belum_lunas')
                                <span class="badge bg-danger-light text-danger px-3 py-2 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> {{ __('Unpaid') }}
                                </span>
                            @elseif ($pinjaman->status === 'lunas')
                                <span class="badge bg-success-light text-success px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('Paid') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">{{ __('Start Date') }}</label>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($pinjaman->start_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">{{ __('End Date') }}</label>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($pinjaman->end_date)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installment Schedule Summary -->
        @if ($pinjaman->status === 'belum_lunas' && $pinjaman->nominal_angsuran > 0)
        <div class="col-lg-12 mb-4">
            <div class="card card-dashboard border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border-top: 3px solid #4154f1 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">{{ __('Future Installment Schedule') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Projected repayment metrics based on current balance.') }}</p>
                        </div>
                    </div>

                    @php
                        $sisaJangkaWaktu = ceil($pinjaman->jumlah_pinjaman / $pinjaman->nominal_angsuran);
                    @endphp

                    <div class="row g-4 mb-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px; font-size: 0.7rem;">{{ __('Estimated Remaining') }}</label>
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-0 fw-bold text-dark me-2">{{ $sisaJangkaWaktu }}</h4>
                                    <span class="text-muted small">{{ __('Months') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px; font-size: 0.7rem;">{{ __('Next Installment') }}</label>
                                <h4 class="mb-0 fw-bold text-primary">Rp {{ number_format(min($pinjaman->nominal_angsuran, $pinjaman->jumlah_pinjaman), 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block">
                             <div class="p-3 h-100 d-flex align-items-center justify-content-center text-center">
                                <i class="bi bi-graph-down-arrow-fill text-primary opacity-25" style="font-size: 2.5rem;"></i>
                             </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center p-2 rounded-2" style="background: rgba(65, 84, 241, 0.05);">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        <span class="small text-muted">{{ __('Note: This is an estimation based on your current balance and monthly installment amount.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Payment History Card -->
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">{{ __('Payment History') }}</h5>
                            <p class="text-muted small mb-0 mt-1">{{ __('Track all payments made for this Liability.') }}</p>
                        </div>
                        @if ($pinjaman->status === 'belum_lunas')
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#bayarModal" data-pinjaman-id="{{ Vinkla\Hashids\Facades\Hashids::encode($pinjaman->id) }}">
                            <i class="bi bi-wallet2 me-1"></i> {{ __('Payment') }}
                        </button>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table id="paymentHistoryTable" class="table table-hover table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">{{ __('No') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th class="text-center">{{ __('Proof') }}</th>
                                    <th style="width: 15%; text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pinjaman->bayar_pinjaman as $index => $bayar_pinjaman)
                                <tr>
                                    <td data-label="{{ __('No') }}">{{ $index + 1 }}</td>
                                    <td data-label="{{ __('Amount') }}" class="fw-bold text-success">Rp {{ number_format($bayar_pinjaman->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td data-label="{{ __('Date') }}">{{ \Carbon\Carbon::parse($bayar_pinjaman->tgl_bayar)->format('d M Y') }}</td>
                                    <td data-label="{{ __('Proof') }}" class="text-center">
                                        @if ($bayar_pinjaman->bukti_bayar)
                                            <a href="{{ asset('storage/' . $bayar_pinjaman->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="tooltip" title="{{ __('View Proof') }}" style="width: 32px; height: 32px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-file-earmark-check"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">{{ __('No file') }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('Action') }}" class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-circle edit-bayar" 
                                                data-id="{{ $bayar_pinjaman->id_bayar }}"
                                                data-bs-toggle="tooltip" title="{{ __('Edit Payment') }}" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('bayar-pinjaman.destroy', $bayar_pinjaman->id_bayar) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this payment record?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" data-bs-toggle="tooltip" title="{{ __('Delete Payment') }}" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <div class="py-3">
                                            <i class="bi bi-info-circle fs-2 d-block mb-3 opacity-50"></i>
                                            {{ __('No payment history found.') }}
                                        </div>
                                    </td>
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

@include('modal.pinjaman.bayar')

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pinjaman.js') }}?v={{ filemtime(public_path('js/pinjaman.js')) }}"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
