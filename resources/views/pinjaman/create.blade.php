@extends('layouts.main')

@section('title', __('Add New Loan'))

@section('container')
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

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Add New Loan') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">{{ __('Loans') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Add New') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">{{ __('Loan Details') }}</h5>
                    
                    <form action="{{ route('pinjaman.store') }}" method="POST" id="createLoanForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Loan Name -->
                            <div class="col-12">
                                <label for="nama_pinjaman" class="form-label fw-bold small text-uppercase text-muted">{{ __('Liability Name') }}</label>
                                <input type="text" class="form-control @error('nama_pinjaman') is-invalid @enderror" id="nama_pinjaman" name="nama_pinjaman" value="{{ old('nama_pinjaman') }}" placeholder="{{ __('e.g., Koperasi Loan, Bank Loan') }}" required>
                                @error('nama_pinjaman')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-4">
                                <label for="jumlah_pinjaman" class="form-label fw-bold small text-uppercase text-muted">{{ __('Amount (Rp)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" id="jumlah_pinjaman" name="jumlah_pinjaman" value="{{ old('jumlah_pinjaman') }}" placeholder="0" min="0" step="any" required>
                                </div>
                                @error('jumlah_pinjaman')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="col-md-4">
                                <label for="jangka_waktu" class="form-label fw-bold small text-uppercase text-muted">{{ __('Duration (Months)') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('jangka_waktu') is-invalid @enderror" id="jangka_waktu" name="jangka_waktu" value="{{ old('jangka_waktu') }}" placeholder="e.g., 12" min="1" required>
                                    <span class="input-group-text bg-light">{{ __('Months') }}</span>
                                </div>
                                <div class="form-text small text-muted">{{ __('Auto-calculated if installment is set') }}</div>
                                @error('jangka_waktu')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Monthly Installment -->
                            <div class="col-md-4">
                                <label for="nominal_angsuran" class="form-label fw-bold small text-uppercase text-muted">{{ __('Monthly Installment (Rp)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control @error('nominal_angsuran') is-invalid @enderror" id="nominal_angsuran" name="nominal_angsuran" value="{{ old('nominal_angsuran') }}" placeholder="0" min="0" step="any">
                                </div>
                                <div class="form-text small text-muted">{{ __('Auto-calculated or enter manually') }}</div>
                                @error('nominal_angsuran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-bold small text-uppercase text-muted">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date (Auto-calculated) -->
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-bold small text-uppercase text-muted">{{ __('End Date') }}</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror bg-light" id="end_date" name="end_date" value="{{ old('end_date') }}" required readonly>
                                <div class="form-text small text-muted">{{ __('Auto-calculated based on duration.') }}</div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <label for="status" class="form-label fw-bold small text-uppercase text-muted">{{ __('Status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>{{ __('Unpaid (Belum Lunas)') }}</option>
                                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>{{ __('Paid (Lunas)') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="col-12">
                                <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Keterangan') }}</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" placeholder="{{ __('Enter additional details (optional)') }}">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('pinjaman.index') }}" class="btn btn-light rounded-pill px-4">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-save me-1"></i> {{ __('Save Loan') }}
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
        const amountInput = document.getElementById('jumlah_pinjaman');
        const startDateInput = document.getElementById('start_date');
        const durationInput = document.getElementById('jangka_waktu');
        const installmentInput = document.getElementById('nominal_angsuran');
        const endDateInput = document.getElementById('end_date');

        let isManuallyEditingInstallment = false;

        function calculateInstallment() {
            if (isManuallyEditingInstallment) return;
            const amount = parseFloat(amountInput.value);
            const duration = parseInt(durationInput.value);
            
            if (!isNaN(amount) && !isNaN(duration) && duration > 0) {
                const installment = amount / duration;
                // Don't use toFixed if it ends with .00 to make it cleaner
                installmentInput.value = installment % 1 === 0 ? installment : installment.toFixed(2);
            }
        }

        function calculateDuration() {
            const amount = parseFloat(amountInput.value);
            const installment = parseFloat(installmentInput.value);
            
            if (!isNaN(amount) && !isNaN(installment) && installment > 0) {
                const duration = Math.ceil(amount / installment);
                durationInput.value = duration;
                calculateEndDate();
            }
        }

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

        if (startDateInput && durationInput && endDateInput && amountInput && installmentInput) {
            startDateInput.addEventListener('change', calculateEndDate);
            
            amountInput.addEventListener('input', () => {
                isManuallyEditingInstallment = false;
                calculateInstallment();
            });

            durationInput.addEventListener('input', () => {
                isManuallyEditingInstallment = false;
                calculateInstallment();
                calculateEndDate();
            });

            installmentInput.addEventListener('input', () => {
                isManuallyEditingInstallment = true;
                calculateDuration();
            });

            // Initial calc
            isManuallyEditingInstallment = false;
            calculateInstallment();
            calculateEndDate();
        }
    });
</script>
@endpush