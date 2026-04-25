@extends('layouts.main')

@section('title', __('Edit Transaction'))

@section('container')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="{{ asset('css/transaksi-create.css') }}?v={{ filemtime(public_path('css/transaksi-create.css')) }}" rel="stylesheet">
@endpush

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Edit Transaction') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">{{ __('Transactions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center transaksi-form-page">
        <div class="col-12 col-xl-11 col-xxl-11">
            <div class="card-dashboard">
                <div class="card-body p-4">
                    
                    @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @php
                        // Encode ID for the route
                        $hashId = \Vinkla\Hashids\Facades\Hashids::encode($transaksi->id);
                    @endphp

                    <form action="{{ route('transaksi.update', $hashId) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                            $hasOldIncome = (bool) old('pemasukan') || (bool) old('nominal_pemasukan');
                            $hasOldExpense = (bool) old('pengeluaran') || (bool) old('nominal');
                            $hasIncome = $hasOldIncome || !empty(old('pemasukan', $transaksi->pemasukan));
                            $hasExpense = $hasOldExpense || !empty(old('pengeluaran', $transaksi->pengeluaran));
                            $initialMode = ($hasIncome && $hasExpense) ? 'both' : ($hasExpense ? 'expense' : 'income');
                        @endphp
                        
                        <div class="row g-4 mb-4">
                            <!-- Date Section -->
                            <div class="col-lg-6">
                                <label for="tgl_transaksi" class="form-label fw-bold small text-uppercase text-muted">
                                    {{ __('Transaction Date') }}
                                    <span class="text-danger">*</span>
                                    <span class="required-hint">wajib diisi</span>
                                </label>
                                <input type="date" class="form-control" id="tgl_transaksi" name="tgl_transaksi" 
                                    value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi) }}" required>
                            </div>

                            <!-- Wallet Selection -->
                            <div class="col-lg-6">
                                <label for="dompet_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Select Wallet') }}</label>
                                <select class="form-select" id="dompet_id" name="dompet_id">
                                    <option value="">- {{ __('Select Wallet') }} -</option>
                                    @foreach ($dompet as $d)
                                    <option value="{{ $d->id }}" {{ old('dompet_id', $transaksi->dompet_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }} (Rp {{ number_format((float)$d->saldo, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text small">{{ __('Changing the wallet will revert the balance update from the old wallet.') }}</div>
                            </div>
                        </div>

                        <!-- Transaction Type Selectors -->
                        <div class="mb-3 transaksi-type-toggle" data-initial-mode="{{ $initialMode }}">
                            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
                                <label class="form-label fw-bold small text-uppercase text-muted m-0">{{ __('Transaction Type') }}</label>
                                <div class="btn-group transaksi-type-toggle__group" role="group" aria-label="{{ __('Transaction Type') }}">
                                    <button type="button" class="btn btn-outline-secondary" id="modeIncome">
                                        <i class="bi bi-arrow-down-circle me-2"></i>{{ __('Income') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="modeExpense">
                                        <i class="bi bi-arrow-up-circle me-2"></i>{{ __('Expense') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="modeBoth">
                                        <i class="bi bi-arrows-collapse me-2"></i>{{ __('Both') }}
                                    </button>
                                </div>
                            </div>
                            <div class="form-text small">{{ __('Select one or both') }}</div>
                        </div>

                        <div class="row g-4 mb-4" id="transaksiTypePanelsRow">
                            <!-- Income Section -->
                            <div class="col-md-6 collapse {{ $initialMode !== 'expense' ? 'show' : '' }}" id="pemasukanSection">
                                <div class="transaksi-panel transaksi-panel--income p-4 border bg-white income-expense-card" style="border-top: 5px solid #198754 !important;">
                                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-arrow-down-circle me-2"></i> {{ __('Income') }}</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pemasukan" class="form-label small text-muted">
                                            {{ __('Category') }}
                                            <span class="required-hint">wajib diisi (jika pilih pemasukan)</span>
                                        </label>
                                        <select class="form-select" id="pemasukan" name="pemasukan">
                                            <option value="">- {{ __('Select Income') }} -</option>
                                            @foreach ($pemasukan as $item)
                                            <option value="{{ $item->id }}" {{ old('pemasukan', $transaksi->pemasukan) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal_pemasukan" class="form-label small text-muted">
                                            {{ __('Amount') }}
                                            <span class="required-hint">wajib diisi (jika pilih pemasukan)</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control fw-bold text-success" 
                                                placeholder="0" value="{{ old('nominal_pemasukan', $transaksi->nominal_pemasukan) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Section -->
                            <div class="col-md-6 collapse {{ $initialMode !== 'income' ? 'show' : '' }}" id="pengeluaranSection">
                                <div class="transaksi-panel transaksi-panel--expense p-4 border bg-white income-expense-card" style="border-top: 5px solid #dc3545 !important;">
                                    <h6 class="text-danger fw-bold mb-3"><i class="bi bi-arrow-up-circle me-2"></i> {{ __('Expense') }}</h6>
                                    
                                    <div class="mb-3">
                                        <label for="pengeluaran" class="form-label small text-muted">
                                            {{ __('Category') }}
                                            <span class="required-hint">wajib diisi (jika pilih pengeluaran)</span>
                                        </label>
                                        <select class="form-select" id="pengeluaran" name="pengeluaran">
                                            <option value="">- {{ __('Select Expense') }} -</option>
                                            @foreach ($pengeluaran as $item)
                                            <option value="{{ $item->id }}" {{ old('pengeluaran', $transaksi->pengeluaran) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label for="nominal" class="form-label small text-muted">
                                            {{ __('Amount') }}
                                            <span class="required-hint">wajib diisi (jika pilih pengeluaran)</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger text-white fw-bold">Rp</span>
                                            <input type="number" id="nominal" name="nominal" class="form-control fw-bold text-danger" 
                                                placeholder="0" value="{{ old('nominal', $transaksi->nominal) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Description / Notes') }}</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="{{ __('Additional details about this transaction...') }}">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> {{ __('Update Transaction') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="transaksi/update"]');
        let editorInstance;

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#keterangan'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote' ]
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        if (form) {
            form.addEventListener('submit', function() {
                if (editorInstance) {
                    editorInstance.updateSourceElement();
                }
            });
        }

        // Initialize TomSelect
        if (typeof TomSelect !== 'undefined') {
             if(document.getElementById('pemasukan')) new TomSelect('#pemasukan', { allowEmptyOption: true, placeholder: '- {{ __('Select Income') }} -' });
             if(document.getElementById('pengeluaran')) new TomSelect('#pengeluaran', { allowEmptyOption: true, placeholder: '- {{ __('Select Expense') }} -' });
        }

        // Transaction type segmented control
        const typeToggleEl = document.querySelector('.transaksi-type-toggle');
        const modeIncomeBtn = document.getElementById('modeIncome');
        const modeExpenseBtn = document.getElementById('modeExpense');
        const modeBothBtn = document.getElementById('modeBoth');
        const pemasukanSectionEl = document.getElementById('pemasukanSection');
        const pengeluaranSectionEl = document.getElementById('pengeluaranSection');

        const setActiveMode = (mode) => {
            const setActive = (btn, isActive) => {
                if (!btn) return;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            };
            setActive(modeIncomeBtn, mode === 'income');
            setActive(modeExpenseBtn, mode === 'expense');
            setActive(modeBothBtn, mode === 'both');
        };

        const setMode = (mode) => {
            if (!pemasukanSectionEl || !pengeluaranSectionEl || typeof bootstrap === 'undefined') return;

            const incomeCollapse = bootstrap.Collapse.getOrCreateInstance(pemasukanSectionEl, { toggle: false });
            const expenseCollapse = bootstrap.Collapse.getOrCreateInstance(pengeluaranSectionEl, { toggle: false });

            const isEditLayout =
                pemasukanSectionEl.classList.contains('col-md-6') ||
                pemasukanSectionEl.classList.contains('col-md-12') ||
                pengeluaranSectionEl.classList.contains('col-md-6') ||
                pengeluaranSectionEl.classList.contains('col-md-12');

            const setCols = (el, cls) => {
                el.classList.remove('col-md-6', 'col-md-12');
                el.classList.add(cls);
            };

            if (isEditLayout) {
                if (mode === 'both') {
                    setCols(pemasukanSectionEl, 'col-md-6');
                    setCols(pengeluaranSectionEl, 'col-md-6');
                } else {
                    setCols(pemasukanSectionEl, 'col-md-12');
                    setCols(pengeluaranSectionEl, 'col-md-12');
                }
            }

            if (mode === 'income') {
                incomeCollapse.show();
                expenseCollapse.hide();
            } else if (mode === 'expense') {
                incomeCollapse.hide();
                expenseCollapse.show();
            } else {
                incomeCollapse.show();
                expenseCollapse.show();
            }

            setActiveMode(mode);
        };

        if (modeIncomeBtn) modeIncomeBtn.addEventListener('click', () => setMode('income'));
        if (modeExpenseBtn) modeExpenseBtn.addEventListener('click', () => setMode('expense'));
        if (modeBothBtn) modeBothBtn.addEventListener('click', () => setMode('both'));

        if (typeToggleEl) {
            const initialMode = typeToggleEl.dataset.initialMode || 'income';
            setMode(initialMode);
        }
    });
</script>
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endpush
