@extends('layouts.main')

@section('title', 'Loan Details')

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
            <div class="card-dashboard h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Loan Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Loan Name</label>
                            <p class="fs-5 fw-semibold text-dark">{{ $pinjaman->nama_pinjaman }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Amount</label>
                            <p class="fs-5 fw-semibold text-primary">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold">Duration</label>
                            <p class="mb-0">{{ $pinjaman->jangka_waktu }} Months</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold">Start Date</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($pinjaman->start_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold">End Date</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($pinjaman->end_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Status</label>
                            @if ($pinjaman->status === 'belum_lunas')
                                <span class="badge bg-danger-light text-danger fs-6 px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Unpaid
                                </span>
                            @elseif ($pinjaman->status === 'lunas')
                                <span class="badge bg-success-light text-success fs-6 px-3 py-2">
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
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0 fw-bold">Payment History</h5>
                        @if ($pinjaman->status === 'belum_lunas')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bayarModal" data-pinjaman-id="{{ Vinkla\Hashids\Facades\Hashids::encode($pinjaman->id) }}">
                            <i class="bi bi-wallet2 me-1"></i> Pay Loan
                        </button>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Proof</th>
                                    <th style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pinjaman->bayar_pinjaman as $index => $bayar_pinjaman)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-success">Rp {{ number_format($bayar_pinjaman->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bayar_pinjaman->tgl_bayar)->format('d M Y') }}</td>
                                    <td>
                                        @if ($bayar_pinjaman->bukti_bayar)
                                            <a href="{{ asset('storage/' . $bayar_pinjaman->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="View Proof">
                                                <i class="bi bi-file-earmark-check"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">No file</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-bayar" 
                                            data-id="{{ $bayar_pinjaman->id_bayar }}"
                                            data-bs-toggle="tooltip" title="Edit Payment">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('bayar_pinjaman.destroy', $bayar_pinjaman->id_bayar) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Payment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-2"></i> No payment history found.
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
    // Since pinjaman.js handles the modal trigger logic, we ensure it's loaded.
    // Also, initializing tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush