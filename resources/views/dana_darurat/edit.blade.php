@extends('layouts.main')

@section('title', __('Edit Emergency Fund'))

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

    /* CKEditor Dark Mode Styles */
    [data-bs-theme="dark"] .ck.ck-editor__main > .ck-editor__editable {
        background-color: #212529 !important;
        border-color: #3b4248 !important;
        color: #dee2e6 !important;
    }
    [data-bs-theme="dark"] .ck.ck-toolbar {
        background-color: #343a40 !important;
        border-color: #3b4248 !important;
    }
    [data-bs-theme="dark"] .ck.ck-toolbar__items .ck.ck-button {
        color: #dee2e6 !important;
    }
    [data-bs-theme="dark"] .ck.ck-toolbar__items .ck.ck-button:hover,
    [data-bs-theme="dark"] .ck.ck-toolbar__items .ck.ck-button.ck-on {
        background-color: #495057 !important;
    }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Edit Emergency Fund') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dana-darurat.index') }}">{{ __('Emergency Fund') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Data') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem;">{{ __('Edit Emergency Fund Data') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Modify the details for this emergency fund transaction.') }}</p>
                </div>
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

                    <form action="{{ route('dana-darurat.update', $dana->id_dana_darurat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="tgl_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Transaction Date') }} <span class="text-danger">*</span></label>
                            <input name="tgl_transaksi_dana_darurat" type="date" class="form-control form-control-lg" id="tgl_transaksi_dana_darurat" 
                                value="{{ old('tgl_transaksi_dana_darurat', $dana->tgl_transaksi_dana_darurat) }}" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="jenis_transaksi_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Transaction Type') }} <span class="text-danger">*</span></label>
                                <select name="jenis_transaksi_dana_darurat" id="jenis_transaksi_dana_darurat" class="form-select form-select-lg" required>
                                     <option value="1" {{ old('jenis_transaksi_dana_darurat', $dana->jenis_transaksi_dana_darurat) == '1' ? 'selected' : '' }}>{{ __('Deposit') }}</option>
                                     <option value="2" {{ old('jenis_transaksi_dana_darurat', $dana->jenis_transaksi_dana_darurat) == '2' ? 'selected' : '' }}>{{ __('Withdrawal') }}</option>
                                 </select>
                                 <div id="infoSaldo" class="mt-2 p-2 bg-light rounded border-start border-4 border-warning small {{ old('jenis_transaksi_dana_darurat', $dana->jenis_transaksi_dana_darurat) == '2' ? '' : 'd-none' }}">
                                     <i class="bi bi-info-circle me-1 text-warning"></i> {{ __('Current balance') }}: <span class="fw-bold text-dark">Rp {{ number_format($totalDanaDarurat, 0, ',', '.') }}</span>
                                 </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nominal_dana_darurat" class="form-label fw-bold small text-uppercase text-muted">{{ __('Amount (Rp)') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" class="form-control" name="nominal_dana_darurat" id="nominal_dana_darurat" placeholder="0"
                                        value="{{ old('nominal_dana_darurat', $dana->nominal_dana_darurat) }}" inputmode="numeric" required>
                                </div>
                                <div id="containerTarikSemua" class="mt-2 {{ old('jenis_transaksi_dana_darurat', $dana->jenis_transaksi_dana_darurat) == '2' ? '' : 'd-none' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="tarikSemuaSaldo" style="cursor: pointer;">
                                        <label class="form-check-label small" for="tarikSemuaSaldo" style="cursor: pointer;">
                                            {{ __('Withdraw all balance') }} (Tarik semua saldo)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold small text-uppercase text-muted">{{ __('Note') }}</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="{{ __('Add a note...') }}">{{ old('keterangan', $dana->keterangan)}}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('dana-darurat.index') }}" class="btn btn-light px-4 rounded-pill">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> {{ __('Update Data') }}
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#keterangan'), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote' ]
                })
                .catch(error => {
                    console.error('Error initializing CKEditor:', error);
                });
        }

        const typeSelect = document.getElementById('jenis_transaksi_dana_darurat');
        const nominalInput = document.getElementById('nominal_dana_darurat');
        const tarikSemuaCheckbox = document.getElementById('tarikSemuaSaldo');
        const infoSaldo = document.getElementById('infoSaldo');
        const containerTarikSemua = document.getElementById('containerTarikSemua');
        const currentBalance = {{ $totalDanaDarurat }};

        // Format Rupiah Function
        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        nominalInput.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });

        // Format initial value if exists
        if (nominalInput.value) {
            nominalInput.value = formatRupiah(nominalInput.value);
        }

        typeSelect.addEventListener('change', function() {
            if (this.value == '2') {
                infoSaldo.classList.remove('d-none');
                containerTarikSemua.classList.remove('d-none');
            } else {
                infoSaldo.classList.add('d-none');
                containerTarikSemua.classList.add('d-none');
                tarikSemuaCheckbox.checked = false;
                nominalInput.readOnly = false;
            }
        });

        tarikSemuaCheckbox.addEventListener('change', function() {
            if (this.checked) {
                nominalInput.value = formatRupiah(currentBalance.toString());
                nominalInput.readOnly = true;
            } else {
                nominalInput.readOnly = false;
            }
        });

        // Clean dots before submit
        document.querySelector('form').addEventListener('submit', function() {
            nominalInput.value = nominalInput.value.replace(/\./g, '');
        });
    });
</script>
@endpush
