@extends('layouts.main')

@section('title', __('Add Transaction'))

@section('container')
@push('css')
<link href="{{ asset('css/tom-select.bootstrap5.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/transaksi-create.css') }}?v={{ filemtime(public_path('css/transaksi-create.css')) }}" rel="stylesheet">
@endpush

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Add Transaction') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">{{ __('Transactions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Add New') }}</li>
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

                    <div id="alertPlaceholder"></div>

                    <form action="{{ route('transaksi.store') }}" method="POST" id="transactionForm">
                        @csrf

                        @php
                            $hasOldIncome = (bool) old('pemasukan') || (bool) old('nominal_pemasukan');
                            $hasOldExpense = (bool) old('pengeluaran') || (bool) old('nominal');
                            $initialMode = $hasOldIncome && $hasOldExpense ? 'both' : ($hasOldExpense ? 'expense' : 'income');
                        @endphp
                        
                        <div class="row g-4 mb-4">
                            <!-- Date Section -->
                            <div class="col-lg-6">
                                <label for="tgl_transaksi" class="form-label fw-bold small text-uppercase text-muted">
                                    {{ __('Transaction Date') }}
                                    <span class="text-danger">*</span>
                                    <span class="required-hint">wajib diisi</span>
                                </label>
                                <input type="date" class="form-control" id="tgl_transaksi" name="tgl_transaksi" value="{{ old('tgl_transaksi', $defaultDate ?? date('Y-m-d')) }}" required>
                            </div>

                            <!-- Wallet Selection -->
                            <div class="col-lg-6">
                                <label for="dompet_id" class="form-label fw-bold small text-uppercase text-muted">{{ __('Select Wallet') }}</label>
                                <select class="form-select" id="dompet_id" name="dompet_id">
                                    <option value="">- {{ __('Select Wallet') }} -</option>
                                    @foreach ($dompet as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }} (Rp {{ number_format((float)$d->saldo, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text small">{{ __('Transaction will affect the balance of the selected wallet.') }}</div>
                            </div>
                        </div>

                        <!-- Transaction Type Selectors -->
                        <div class="mb-4 transaksi-type-toggle" data-initial-mode="{{ $initialMode }}">
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

                        <!-- Category & Amount Sections -->
                        <div class="row g-4 mb-4" id="transaksiTypePanelsRow">
                            <!-- Income Section -->
                            <div class="col-md-6 collapse {{ $initialMode !== 'expense' ? 'show' : '' }}" id="pemasukanSection">
                                <div class="transaksi-panel transaksi-panel--income p-4 border bg-white mb-3" style="border-top: 5px solid #198754 !important;">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label for="pemasukan" class="form-label fw-bold small text-muted text-uppercase">
                                                {{ __('Income Category') }}
                                                <span class="required-hint">wajib diisi (jika pilih pemasukan)</span>
                                            </label>
                                            <select class="form-select" id="pemasukan" name="pemasukan">
                                                <option value="">- {{ __('Select Income') }} -</option>
                                                @foreach ($pemasukan as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="nominal_pemasukan" class="form-label fw-bold small text-muted text-uppercase">
                                                {{ __('Amount') }}
                                                <span class="required-hint">wajib diisi (jika pilih pemasukan)</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-success text-white fw-bold border-0">Rp</span>
                                                <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control fw-bold text-success border-start-0" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Section -->
                            <div class="col-md-6 collapse {{ $initialMode !== 'income' ? 'show' : '' }}" id="pengeluaranSection">
                                <div class="transaksi-panel transaksi-panel--expense p-4 border bg-white" style="border-top: 5px solid #dc3545 !important;">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label for="pengeluaran" class="form-label fw-bold small text-muted text-uppercase">
                                                {{ __('Expense Category') }}
                                                <span class="required-hint">wajib diisi (jika pilih pengeluaran)</span>
                                            </label>
                                            <select class="form-select" id="pengeluaran" name="pengeluaran">
                                                <option value="">- {{ __('Select Expense') }} -</option>
                                                @foreach ($pengeluaran as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="nominal" class="form-label fw-bold small text-muted text-uppercase">
                                                {{ __('Amount') }}
                                                <span class="required-hint">wajib diisi (jika pilih pengeluaran)</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-danger text-white fw-bold border-0">Rp</span>
                                                <input type="number" id="nominal" name="nominal" class="form-control fw-bold text-danger border-start-0" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Description / Notes') }}</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="{{ __('Additional details about this transaction...') }}"></textarea>
                        </div>

                        <!-- Advanced Options -->
                        {{-- <div class="card bg-light border-0 mb-4">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center" role="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                                <span class="fw-bold text-muted small"><i class="bi bi-gear-fill me-2"></i> Advanced Options (Assets & Emergency Fund)</span>
                                <i class="bi bi-chevron-down text-muted"></i>
                            </div>
                            <div class="collapse show" id="advancedOptions">
                                <div class="card-body pt-0">
                                    
                                    <!-- Asset Option -->
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="checkAssetList" name="kategori[]" value="asset_list">
                                        <label class="form-check-label fw-medium" for="checkAssetList">Add to Asset List</label>
                                    </div>
                                    <div class="ms-4 mb-3 ps-3 border-start border-primary" id="selectBarangContainer" style="display: none;">
                                        <label for="barang_id" class="form-label small text-muted">Select Asset Item</label>
                                        <select class="form-select w-100" id="barang_id" name="barang_id">
                                            <option value="">- Select Asset -</option>
                                            @foreach ($barang as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr class="text-muted opacity-25">

                                    <!-- Emergency Fund Option -->
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="checkEmergencyFund" name="kategori[]" value="emergency_fund">
                                        <label class="form-check-label fw-medium" for="checkEmergencyFund">Add/Withdraw Emergency Fund</label>
                                    </div>
                                    <div class="ms-4 mb-3 ps-3 border-start border-danger" id="danaDaruratContainer" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Type</label>
                                                <select name="jenis_transaksi_dana_darurat" class="form-select">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="1">Fund In</option>
                                                    <option value="2">Fund Out</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Amount</label>
                                                <input type="number" name="nominal_dana_darurat" class="form-control" placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Note</label>
                                                <textarea name="keterangan_dana_darurat" class="form-control" rows="1" placeholder="Note..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div> --}}

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary shadow-sm">
                                <i class="bi bi-check-lg me-2"></i> {{ __('Save Transaction') }}
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
<script src="{{ asset('js/tom-select.complete.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TomSelect variables
        let incSelect, expSelect;
        
        // Assets
        const checkAsset = document.getElementById('checkAssetList');
        const assetContainer = document.getElementById('selectBarangContainer');
        if(checkAsset) {
            checkAsset.addEventListener('change', function() {
                // Determine if checked
                const isChecked = this.checked;
                assetContainer.style.display = isChecked ? 'block' : 'none';
            });
        }

        // Emergency Fund
        const checkEmergency = document.getElementById('checkEmergencyFund');
        const emergencyContainer = document.getElementById('danaDaruratContainer');

        if (checkEmergency) {
             checkEmergency.addEventListener('change', function() {
                const isChecked = this.checked;
                emergencyContainer.style.display = isChecked ? 'block' : 'none';
            });
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

            const isGridLayout =
                pemasukanSectionEl.classList.contains('col-md-6') ||
                pemasukanSectionEl.classList.contains('col-md-12') ||
                pengeluaranSectionEl.classList.contains('col-md-6') ||
                pengeluaranSectionEl.classList.contains('col-md-12');

            const setCols = (el, cls) => {
                el.classList.remove('col-md-6', 'col-md-12');
                el.classList.add(cls);
            };

            if (isGridLayout) {
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

        // Form Validation Logic
        let editorInstance;
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

        const form = document.getElementById('transactionForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const getVal = (id, ts) => {
                const el = document.getElementById(id);
                if (!el) return '';
                return ts ? ts.getValue() : el.value;
            };

            const nominalPemasukan = document.getElementById('nominal_pemasukan');
            const nominalPengeluaran = document.getElementById('nominal');

            const incomeCat = getVal('pemasukan', incSelect);
            const incomeAmt = nominalPemasukan.value;
            const expenseCat = getVal('pengeluaran', expSelect);
            const expenseAmt = nominalPengeluaran.value;

            const hasIncome = incomeCat && incomeAmt && parseFloat(incomeAmt) > 0;
            const hasExpense = expenseCat && expenseAmt && parseFloat(expenseAmt) > 0;

            const isIncomePartial = (incomeAmt && parseFloat(incomeAmt) > 0 && !incomeCat) || 
                                    (!incomeAmt && incomeCat);
            
            const isExpensePartial = (expenseAmt && parseFloat(expenseAmt) > 0 && !expenseCat) || 
                                     (!expenseAmt && expenseCat);

            if (!hasIncome && !hasExpense) {
                alert("{{ __('Please fill in at least one transaction type (Income or Expense) completely with an amount greater than 0.') }}");
                return;
            }

            if (isIncomePartial) {
                alert("{{ __('Please complete the Income section (both category and amount).') }}");
                return;
            }

            if (isExpensePartial) {
                alert("{{ __('Please complete the Expense section (both category and amount).') }}");
                return;
            }

            // Ajax Submission
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __('Saving...') }}';

            const formData = new FormData(form);
            if (editorInstance) {
                formData.set('keterangan', editorInstance.getData());
            }
            const alertPlaceholder = document.getElementById('alertPlaceholder');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    let errorMsg = data?.message || '{{ __('Terjadi kesalahan pada sistem.') }}';
                    
                    // Handle Laravel Validation Errors (422)
                    if (response.status === 422 && data?.errors) {
                        const errorList = Object.values(data.errors).flat();
                        errorMsg = errorList.map(msg => `<li>${msg}</li>`).join('');
                        errorMsg = `<ul class="mb-0 mt-2 ps-3 small text-start">${errorMsg}</ul>`;
                    }
                    
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(data => {
                // Render Success Alert (Migration Guide Style)
                alertPlaceholder.innerHTML = `
                    <div class="alert alert-success border-0 p-4 mb-4 d-flex align-items-center justify-content-between text-start scale-in">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success-light text-success me-3">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">${data.message || '{{ __('Data Berhasil Disimpan!') }}'}</h5>
                                <p class="mb-0 text-muted small">{{ __('Data processed successfully') }}</p>
                            </div>
                        </div>
                        <a href="${data.redirect_url}" class="btn btn-success px-4 py-2 fw-bold">
                            <i class="bi bi-eye me-2"></i> {{ __('Lihat Data') }} ${data.redirect_name}
                        </a>
                    </div>
                `;
                
                // Reset Form
                form.reset();
                if(incSelect) incSelect.clear();
                if(expSelect) expSelect.clear();
                if(editorInstance) editorInstance.setData('');
                
                // Scroll to Top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                alertPlaceholder.innerHTML = `
                    <div class="alert alert-danger border-0 p-4 mb-4 d-flex align-items-center text-start scale-in">
                        <div class="icon-box bg-danger-light text-danger me-3">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ __('Gagal Menyimpan') }}</h5>
                            <div class="mb-0 text-muted small">${error.message}</div>
                        </div>
                    </div>
                `;
                // Scroll to Top for visibility
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            });
        });

        // Initialize TomSelect if validation didn't fail and elements exist
        if (typeof TomSelect !== 'undefined') {
             if(document.getElementById('pemasukan')) incSelect = new TomSelect('#pemasukan', { allowEmptyOption: true, placeholder: '- {{ __('Select Income') }} -' });
             if(document.getElementById('pengeluaran')) expSelect = new TomSelect('#pengeluaran', { allowEmptyOption: true, placeholder: '- {{ __('Select Expense') }} -' });
             if(document.getElementById('barang_id')) new TomSelect('#barang_id', { allowEmptyOption: true, placeholder: '- {{ __('Select Asset') }} -' });
        }
    });
</script>
<script src="{{ asset('js/transaksi.js') }}?v={{ filemtime(public_path('js/transaksi.js')) }}"></script>
@endpush
