@extends('layouts.main')

@section('title', __('Wallet'))

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

    /* PWA & Premium Enhancements (White Theme) */
    .card-summary {
        border: 1px solid rgba(0,0,0,0.05);
        background: #ffffff;
        color: #2d3436;
        overflow: hidden;
        position: relative;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .balance-amount {
        color: #4154f1;
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -1px;
    }
    .wallet-card {
        border: 1px solid rgba(0,0,0,0.05);
        background: #fff;
    }
    .wallet-card:hover {
        box-shadow: 0 15px 30px rgba(65, 84, 241, 0.1) !important;
        border-color: #4154f1;
    }
    .wallet-icon-wrapper {
        /* static icon */
    }
    .wallet-card:hover .wallet-icon-wrapper {
        /* no transform */
    }
    .fab-add {
        position: fixed;
        bottom: 2rem;
        right: 1.5rem;
        z-index: 1040;
        width: 60px;
        height: 60px;
        display: none; /* Desktop hidden */
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(65, 84, 241, 0.4);
    }

    @media (max-width: 767.98px) {
        .fab-add {
            display: flex;
        }
        .btn-add-desktop {
            display: none;
        }
        .balance-amount {
            font-size: 1.75rem;
        }
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
    [data-bs-theme="dark"] .wallet-card {
        background: #1e1e1e;
        border-color: rgba(255,255,255,0.05);
    }
    [data-bs-theme="dark"] .card-summary {
        background: linear-gradient(135deg, #2d3436 0%, #1a1a1a 100%);
    }
</style>
@endpush

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Wallet') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Wallet') }}</li>
        </ol>
    </nav>
</div>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<section class="section">
    <div class="row">
@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp

<div class="col-lg-12 mb-4">
    <div class="card {{ $uiStyle === 'milenial' ? 'm-wallet-balance-card border-0 shadow-lg' : 'card-summary' }} mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="card-title mb-1 fw-bold {{ $uiStyle === 'milenial' ? 'text-white text-uppercase ls-1' : 'text-dark opacity-75' }}" style="font-size: {{ $uiStyle === 'milenial' ? '0.9rem' : '1.1rem' }};">{{ __('Total Balance') }}</h5>
                    <p class="{{ $uiStyle === 'milenial' ? 'text-white opacity-75' : 'text-muted' }} small mb-0">{{ __('Combined balance from all your active wallets.') }}</p>
                </div>
                <div class="text-end">
                    <p class="small mb-0 {{ $uiStyle === 'milenial' ? 'text-white opacity-50' : 'text-muted' }} d-md-none text-start">{{ __('Current Total') }}</p>
                    <h2 class="{{ $uiStyle === 'milenial' ? 'text-white' : 'balance-amount text-dark' }} mb-0 fw-bold" style="font-size: {{ $uiStyle === 'milenial' ? '2.5rem' : '' }}; letter-spacing: -1px;">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Wallets Grid -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ __('Wallet Portfolio') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Manage your various funding sources here') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse">
                        <i class="bi bi-info-circle me-1"></i> {{ __('Instructions') }}
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm btn-add-desktop" data-bs-toggle="modal" data-bs-target="#addWalletModal">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Wallet') }}
                    </button>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="collapse mb-4" id="instructionsCollapse">
                <div class="card-body bg-light border-bottom mb-4">
                    <div class="alert alert-info border-0 bg-white shadow-sm mb-0 text-dark">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-lightbulb me-2"></i>{{ __('How to Use the Wallet Page') }}:</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3 small text-muted list-unstyled">
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">1</span> {!! __('Click **Add Wallet** to create a new account.') !!}</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">2</span> {{ __('Enter the wallet name, icon, and initial balance, if any.') }}</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">3</span> {!! __('Activate **"Input as income"** if you want the opening balance to be recorded in the cash flow.') !!}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3 small text-muted list-unstyled">
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">4</span> {!! __('Click on the wallet card to view **Transaction History**.') !!}</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">5</span> {!! __('Use the **Add Balance** button in the history to manually top up.') !!}</li>
                                    <li><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">6</span> {{ __('Balance is automatically updated when recording income/expense transactions.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                @if(count($wallets) > 0)
                    @foreach($wallets as $wallet)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card {{ $uiStyle === 'milenial' ? 'm-wallet-item-card glass-card' : 'wallet-card shadow-sm' }} border-0 h-100 position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="{{ $uiStyle === 'milenial' ? 'm-wallet-icon' : 'wallet-icon-wrapper rounded-4 bg-primary bg-opacity-10 text-primary' }} d-flex align-items-center justify-content-center shadow-sm" style="{{ $uiStyle === 'corporate' ? 'width: 56px; height: 56px; border: 1px solid rgba(65, 84, 241, 0.1);' : '' }}">
                                        @if($wallet->ikon)
                                            @if(str_starts_with($wallet->ikon, 'uploads/'))
                                                <img src="{{ asset('img/icons/' . $wallet->ikon) }}" alt="{{ $wallet->nama }}" style="width: 32px; height: 32px; object-fit: contain;">
                                            @else
                                                <img src="{{ asset('img/icons/' . $wallet->ikon . '.png') }}" alt="{{ $wallet->ikon }}" style="width: 32px; height: 32px; object-fit: contain;">
                                            @endif
                                        @else
                                            <i class="bi bi-wallet2 fs-3 {{ $uiStyle === 'milenial' ? 'text-primary' : '' }}"></i>
                                        @endif
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; z-index: 10;">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="z-index: 1050;">
                                            <li><a class="dropdown-item py-2 px-3 text-danger d-flex align-items-center" href="#" onclick="confirmDelete('{{ $wallet->id }}', '{{ $wallet->nama }}')">
                                                <i class="bi bi-trash me-2"></i> {{ __('Delete Wallet') }}
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <a href="{{ route('dompet.show', $wallet->id) }}" class="text-decoration-none stretched-link">
                                    <p class="{{ $uiStyle === 'milenial' ? 'm-wallet-name' : 'text-uppercase text-muted fw-bold mb-1' }}" style="{{ $uiStyle === 'corporate' ? 'font-size: 0.7rem; letter-spacing: 1px;' : '' }}">{{ $wallet->nama }}</p>
                                    <h4 class="{{ $uiStyle === 'milenial' ? 'm-wallet-amount' : 'fw-bold mb-0 text-dark' }}">Rp {{ number_format((float)$wallet->saldo, 0, ',', '.') }}</h4>
                                    
                                    <div class="mt-4 d-flex align-items-center text-primary fw-semibold small">
                                        <span>{{ __('View Details') }}</span>
                                        <i class="bi bi-arrow-right ms-2 transition-icon"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-wallet2 text-muted display-4"></i>
                        </div>
                        <h5 class="fw-bold text-dark">{{ __('No wallets yet') }}</h5>
                        <p class="text-muted">{{ __('Start managing your finances by adding your first wallet.') }}</p>
                        <button type="button" class="btn btn-primary rounded-pill mt-2 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addWalletModal">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Wallet Now') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Floating Action Button for Mobile -->
        <button type="button" class="btn btn-primary fab-add" data-bs-toggle="modal" data-bs-target="#addWalletModal" title="{{ __('Add Wallet') }}">
            <i class="bi bi-plus-lg fs-2"></i>
        </button>
    </section>

<!-- Add Wallet Modal -->
<div class="modal fade" id="addWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('Add New Wallet') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dompet.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">{{ __('Wallet Name') }}</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="{{ __('e.g. Bank Account, PayPal, Cash') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">{{ __('Initial Balance') }}</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text rounded-start-3 bg-light border-end-0">Rp</span>
                            <input type="number" name="saldo" class="form-control rounded-end-3 border-start-0" placeholder="0" required>
                        </div>
                        <div class="form-check form-switch small">
                            <input class="form-check-input" type="checkbox" name="record_income" id="recordIncome" value="1" checked>
                            <label class="form-check-label text-muted" for="recordIncome">
                                {{ __('Record as income in cash flow?') }}
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="ikon" value="wallet">
                        <label class="form-label small fw-bold text-muted">{{ __('Or Attach Custom Image/Icon') }}</label>
                        <input type="file" name="custom_ikon" class="form-control rounded-3" accept="image/*">
                        <div class="form-text small">{{ __('Maximum 2MB (Jpeg, Png, Svg, Gif)') }}</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('Delete Wallet') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteWalletForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-danger">
                        <i class="bi bi-exclamation-octagon display-4"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ __('Delete Wallet') }} <span id="deleteWalletName"></span>?</h5>
                    <p class="text-muted small px-4">{{ __('This action cannot be undone. Wallets can only be deleted if they have no transaction history.') }}</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">{{ __('Yes, Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        const modal = new bootstrap.Modal(document.getElementById('deleteWalletModal'));
        const form = document.getElementById('deleteWalletForm');
        const nameSpan = document.getElementById('deleteWalletName');
        
        form.action = `/dompet/${id}`;
        nameSpan.textContent = name;
        modal.show();
    }
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endsection
