@extends('layouts.main')

@section('title', 'Add New Loan')

@section('container')

<div class="pagetitle mb-4">
    <h1>Add New Loan</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Loans</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Loan Details</h5>
                    
                    <form action="{{ route('pinjaman.store') }}" method="POST" id="createLoanForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Loan Name -->
                            <div class="col-12">
                                <label for="nama_pinjaman" class="form-label fw-bold small text-uppercase text-muted">Loan Name</label>
                                <input type="text" class="form-control @error('nama_pinjaman') is-invalid @enderror" id="nama_pinjaman" name="nama_pinjaman" value="{{ old('nama_pinjaman') }}" placeholder="e.g., Koperasi Loan, Bank Loan" required>
                                @error('nama_pinjaman')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="jumlah_pinjaman" class="form-label fw-bold small text-uppercase text-muted">Amount (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" id="jumlah_pinjaman" name="jumlah_pinjaman" value="{{ old('jumlah_pinjaman') }}" placeholder="0" min="0" step="1000" required>
                                </div>
                                @error('jumlah_pinjaman')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6">
                                <label for="jangka_waktu" class="form-label fw-bold small text-uppercase text-muted">Duration (Months)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('jangka_waktu') is-invalid @enderror" id="jangka_waktu" name="jangka_waktu" value="{{ old('jangka_waktu') }}" placeholder="e.g., 12" min="1" required>
                                    <span class="input-group-text bg-light">Months</span>
                                </div>
                                @error('jangka_waktu')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-bold small text-uppercase text-muted">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date (Auto-calculated) -->
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-bold small text-uppercase text-muted">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror bg-light" id="end_date" name="end_date" value="{{ old('end_date') }}" required readonly>
                                <div class="form-text small text-muted">Auto-calculated based on duration.</div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <label for="status" class="form-label fw-bold small text-uppercase text-muted">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Unpaid (Belum Lunas)</option>
                                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Paid (Lunas)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('pinjaman.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save Loan
                                </button>
                            </div>
                        </div>
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
        const startDateInput = document.getElementById('start_date');
        const durationInput = document.getElementById('jangka_waktu');
        const endDateInput = document.getElementById('end_date');

        function calculateEndDate() {
            const startDate = new Date(startDateInput.value);
            const duration = parseInt(durationInput.value);

            if (!isNaN(startDate.getTime()) && !isNaN(duration) && duration > 0) {
                // Add months to start date
                startDate.setMonth(startDate.getMonth() + duration);
                
                // Format to YYYY-MM-DD
                const year = startDate.getFullYear();
                const month = String(startDate.getMonth() + 1).padStart(2, '0');
                const day = String(startDate.getDate()).padStart(2, '0');
                
                endDateInput.value = `${year}-${month}-${day}`;
            }
        }

        if (startDateInput && durationInput && endDateInput) {
            startDateInput.addEventListener('change', calculateEndDate);
            durationInput.addEventListener('input', calculateEndDate);
            // Initial calc
            calculateEndDate();
        }
    });
</script>
@endpush