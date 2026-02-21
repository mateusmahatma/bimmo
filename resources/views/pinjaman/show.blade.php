@extends('layouts.main')

@section('title', 'Loan Details')

@push('css')
<style>
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
    <h1>Loan Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Loans</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Loan Information Card -->
        <div class="col-lg-12 mb-4">
            <div class="card-dashboard h-100 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4 text-dark" style="font-size: 1.1rem;">Loan Information</h5>
                    <div class="row g-4">
                        <div class="col-md-6 border-start border-primary border-4 py-1 ps-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px;">Loan Name</label>
                            <p class="fs-5 fw-bold text-dark mb-0">{{ $pinjaman->nama_pinjaman }}</p>
                        </div>
                        <div class="col-md-6 border-start border-success border-4 py-1 ps-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="letter-spacing: 0.5px;">Current Balance</label>
                            <p class="fs-5 fw-bold text-primary mb-0">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Duration</label>
                            <p class="mb-0 fw-semibold">{{ $pinjaman->jangka_waktu }} Months</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Start Date</label>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($pinjaman->start_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">End Date</label>
                            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($pinjaman->end_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Status</label>
                            @if ($pinjaman->status === 'belum_lunas')
                                <span class="badge bg-danger-light text-danger px-3 py-2 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Unpaid
                                </span>
                            @elseif ($pinjaman->status === 'lunas')
                                <span class="badge bg-success-light text-success px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Paid
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Card -->
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">Payment History</h5>
                            <p class="text-muted small mb-0 mt-1">Track all payments made for this loan.</p>
                        </div>
                        @if ($pinjaman->status === 'belum_lunas')
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#bayarModal" data-pinjaman-id="{{ Vinkla\Hashids\Facades\Hashids::encode($pinjaman->id) }}">
                            <i class="bi bi-wallet2 me-1"></i> Pay Loan
                        </button>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table id="paymentHistoryTable" class="table table-hover table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th class="text-center">Proof</th>
                                    <th style="width: 15%; text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pinjaman->bayar_pinjaman as $index => $bayar_pinjaman)
                                <tr>
                                    <td data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Amount" class="fw-bold text-success">Rp {{ number_format($bayar_pinjaman->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td data-label="Date">{{ \Carbon\Carbon::parse($bayar_pinjaman->tgl_bayar)->format('d M Y') }}</td>
                                    <td data-label="Proof" class="text-center">
                                        @if ($bayar_pinjaman->bukti_bayar)
                                            <a href="{{ asset('storage/' . $bayar_pinjaman->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="tooltip" title="View Proof" style="width: 32px; height: 32px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-file-earmark-check"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">No file</span>
                                        @endif
                                    </td>
                                    <td data-label="Action" class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-circle edit-bayar" 
                                                data-id="{{ $bayar_pinjaman->id_bayar }}"
                                                data-bs-toggle="tooltip" title="Edit Payment" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('bayar_pinjaman.destroy', $bayar_pinjaman->id_bayar) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" data-bs-toggle="tooltip" title="Delete Payment" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;">
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
                                            No payment history found.
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

@endsection

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
