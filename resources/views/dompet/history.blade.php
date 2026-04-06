@extends('layouts.main')

@section('title', __('Wallet History') . ' - ' . $wallet->nama)

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

@section('container')
@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('History') }} {{ $wallet->nama }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dompet.index') }}">{{ __('Wallet') }}</a></li>
            <li class="breadcrumb-item active">{{ __('History') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row g-4">
        <!-- Balance Card -->
        <div class="col-12 col-lg-4">
            <div class="card {{ $uiStyle === 'milenial' ? 'm-wallet-balance-card border-0 shadow-lg' : 'border-0 shadow-sm' }} h-100" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }}; background: {{ $uiStyle === 'milenial' ? '' : 'linear-gradient(135deg, #2c384e 0%, #1a2233 100%)' }}; color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between shadow-lg">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle {{ $uiStyle === 'milenial' ? 'bg-white bg-opacity-20' : 'bg-white bg-opacity-10' }} p-2">
                                @if($wallet->ikon)
                                    @if(str_starts_with($wallet->ikon, 'uploads/'))
                                        <img src="{{ asset('img/icons/' . $wallet->ikon) }}" alt="{{ $wallet->nama }}" style="width: 32px; height: 32px; object-fit: contain;">
                                    @else
                                        <img src="{{ asset('img/icons/' . $wallet->ikon . '.png') }}" alt="{{ $wallet->ikon }}" style="width: 32px; height: 32px; object-fit: contain;" onerror="this.outerHTML='<i class=\'bi bi-wallet2 fs-4\'></i>'">
                                    @endif
                                @else
                                    <i class="bi bi-wallet2 fs-4"></i>
                                @endif
                            </div>
                            <h4 class="fw-bold mb-0">{{ $wallet->nama }}</h4>
                        </div>
                        <h6 class="text-white text-opacity-75 mb-1 small fw-medium text-uppercase" style="letter-spacing: 1px;">{{ __('Current Balance') }}</h6>
                        <h1 class="fw-bold mb-0">Rp {{ number_format((float)$wallet->saldo, 0, ',', '.') }}</h1>
                    </div>
                    
                    <button class="btn {{ $uiStyle === 'milenial' ? 'btn-light text-primary fw-bold border-0' : 'btn-outline-light border-2' }} rounded-pill w-100 mt-4" data-bs-toggle="modal" data-bs-target="#addBalanceModal">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Balance') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">{{ __('Transaction History') }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-pill" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-1"></i> {{ __('Filter') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 10px;">
                            <li><a class="dropdown-item {{ !request()->has('type') ? 'active' : '' }}" href="{{ route('dompet.show', $wallet->id) }}">{{ __('All') }}</a></li>
                            <li><a class="dropdown-item {{ request('type') === 'income' ? 'active' : '' }}" href="{{ route('dompet.show', [$wallet->id, 'type' => 'income']) }}">{{ __('Income') }}</a></li>
                            <li><a class="dropdown-item {{ request('type') === 'expense' ? 'active' : '' }}" href="{{ route('dompet.show', [$wallet->id, 'type' => 'expense']) }}">{{ __('Expense') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0 small text-uppercase text-muted fw-bold">{{ __('Date') }}</th>
                                    <th class="py-3 border-0 small text-uppercase text-muted fw-bold">{{ __('Description') }}</th>
                                    <th class="py-3 border-0 small text-uppercase text-muted fw-bold text-end pe-4">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-medium">{{ \Carbon\Carbon::parse($transaction->tgl_transaksi)->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            @if($transaction->nominal_pemasukan > 0)
                                                {{ $transaction->pemasukanRelation->nama ?? 'Income' }}
                                            @else
                                                {{ $transaction->pengeluaranRelation->nama ?? 'Expense' }}
                                            @endif
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $transaction->keterangan ?? __('No description') }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($transaction->nominal_pemasukan > 0)
                                            <span class="text-success fw-bold">+ Rp {{ number_format((float)$transaction->nominal_pemasukan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-danger fw-bold">- Rp {{ number_format((float)$transaction->nominal, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        {{ __('No transactions found for this wallet.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-3 pt-0 d-flex justify-content-end">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Balance Modal -->
<div class="modal fade" id="addBalanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('Manual Balance Top-Up') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dompet.add-balance', $wallet->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Current balance') }}: <strong class="text-primary">Rp {{ number_format((float)$wallet->saldo, 0, ',', '.') }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">{{ __('Amount to Add') }}</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text rounded-start-3 bg-light border-end-0">Rp</span>
                            <input type="number" name="nominal" class="form-control rounded-end-3 border-start-0" placeholder="0" required>
                        </div>
                        <div class="form-check form-switch small">
                            <input class="form-check-input" type="checkbox" name="record_income" id="recordIncome" value="1" checked>
                            <label class="form-check-label text-muted" for="recordIncome">
                                {{ __('Record as income in cash flow?') }}
                            </label>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">{{ __('Description (Optional)') }}</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="{{ __('e.g. Bonus, Salary, Gift') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Add Balance') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
