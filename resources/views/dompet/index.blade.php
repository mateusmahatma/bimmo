@extends('layouts.main')

@section('title', 'Wallet')

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
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">Wallet</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wallet</li>
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
<style>
    /* Custom style for summary card - clean corporate look matching Budget menu */
    .card-summary {
        border-radius: 12px;
        border: 0;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        background-color: #fff;
    }
    .balance-amount {
        color: #4154f1; /* primary color */
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
</style>

<div class="col-lg-12 mb-4">
    <div class="card card-summary shadow-sm">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="card-title mb-1 fw-bold text-dark" style="font-size: 1.1rem;">Total Current Balance</h5>
                <p class="text-muted small mb-0">Combined balance from all of your active wallet accounts.</p>
            </div>
            <div class="text-end">
                <h2 class="balance-amount mb-0">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

        <!-- Wallets Grid -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Wallet Portfolio</h5>
                    <p class="text-muted small mb-0">Manage your various funding sources here</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse">
                        <i class="bi bi-info-circle me-1"></i> Instructions
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addWalletModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Wallet
                    </button>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="collapse mb-4" id="instructionsCollapse">
                <div class="card-body bg-light border-bottom mb-4" style="border-radius: 12px;">
                    <div class="alert alert-info border-0 bg-white shadow-sm mb-0 text-dark">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-lightbulb me-2"></i>How to Use the Wallet Page:</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3 small text-muted list-unstyled">
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">1</span> Click <strong>Add Wallet</strong> to create a new account.</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">2</span> Enter the wallet name, icon, and initial balance, if any.</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">3</span> Activate <strong>"Input as income"</strong> if you want the opening balance to be recorded in the cash flow.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 ps-3 small text-muted list-unstyled">
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">4</span> Click on the wallet card to view <strong>Transaction History</strong>.</li>
                                    <li class="mb-2"><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">5</span> Use the <strong>Add Balance</strong> button in the history to manually top up.</li>
                                    <li><span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">6</span> Balance is automatically updated when recording income/expense transactions.</li>
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
                        <div class="card wallet-card border-0 shadow-sm h-100 position-relative" style="border-radius: 16px; border: 1px solid rgba(0,0,0,0.05) !important;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="wallet-icon-wrapper rounded-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 56px; height: 56px;">
                                        @if($wallet->ikon)
                                            @if(str_starts_with($wallet->ikon, 'uploads/'))
                                                <img src="{{ asset('img/icons/' . $wallet->ikon) }}" alt="{{ $wallet->nama }}" style="width: 32px; height: 32px; object-fit: contain;">
                                            @else
                                                <img src="{{ asset('img/icons/' . $wallet->ikon . '.png') }}" alt="{{ $wallet->ikon }}" style="width: 32px; height: 32px; object-fit: contain;">
                                            @endif
                                        @else
                                            <i class="bi bi-wallet2 fs-3"></i>
                                        @endif
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; z-index: 10;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; z-index: 1050;">
                                            <li><a class="dropdown-item text-danger d-flex align-items-center" href="#" onclick="confirmDelete('{{ $wallet->id }}', '{{ $wallet->nama }}')">
                                                <i class="bi bi-trash me-2"></i> Delete Wallet
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <a href="{{ route('dompet.show', $wallet->id) }}" class="text-decoration-none stretched-link">
                                    <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">{{ $wallet->nama }}</p>
                                    <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format((float)$wallet->saldo, 0, ',', '.') }}</h4>
                                    
                                    <div class="mt-4 d-flex align-items-center text-primary fw-semibold small">
                                        View Details <i class="bi bi-arrow-right ms-2 transition-icon"></i>
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
                        <h5 class="fw-bold text-dark">No wallets yet</h5>
                        <p class="text-muted">Start managing your finances by adding your first wallet.</p>
                        <button type="button" class="btn btn-primary rounded-pill mt-2 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addWalletModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Wallet Now
                        </button>
                    </div>
                @endif
            </div>
        </div>
</section>

<!-- Add Wallet Modal -->
<div class="modal fade" id="addWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dompet.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Wallet Name</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="e.g. Bank Account, PayPal, Cash" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Initial Balance</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text rounded-start-3 bg-light border-end-0">Rp</span>
                            <input type="number" name="saldo" class="form-control rounded-end-3 border-start-0" placeholder="0" required>
                        </div>
                        <div class="form-check form-switch small">
                            <input class="form-check-input" type="checkbox" name="record_income" id="recordIncome" value="1" checked>
                            <label class="form-check-label text-muted" for="recordIncome">
                                Record as income in cash flow?
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="ikon" value="wallet">
                        <label class="form-label small fw-bold text-muted">Or Attach Custom Image/Icon</label>
                        <input type="file" name="custom_ikon" class="form-control rounded-3" accept="image/*">
                        <div class="form-text small">Maximum 2MB (Jpeg, Png, Svg, Gif)</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Delete Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteWalletForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-danger">
                        <i class="bi bi-exclamation-octagon display-4"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Wallet <span id="deleteWalletName"></span>?</h5>
                    <p class="text-muted small px-4">This action cannot be undone. Wallets can only be deleted if they have no transaction history.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, Delete</button>
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
    .wallet-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-color: #4154f1 !important;
    }
</style>
@endsection
