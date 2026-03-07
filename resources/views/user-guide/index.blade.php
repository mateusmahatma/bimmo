@extends('layouts.main')

@section('container')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Corporate Header Section -->
            <div class="text-center mb-5 pb-2">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3 fw-semibold">{{ __('BIMMO USER GUIDE') }}</span>
                <h1 class="display-5 fw-bold text-navy mb-3">{{ __('Welcome to Bimmo') }}</h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">{{ __('A comprehensive guide to help you optimize your personal financial management with efficiency, transparency, and measurable results.') }}</p>
                <div class="mt-4">
                    <hr class="mx-auto" style="width: 60px; height: 3px; background-color: #0d6efd; border: none; border-radius: 2px;">
                </div>
            </div>

            <!-- Steps Section -->
            <div class="row g-4 mb-5">
                <!-- Step 1: Core Records -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-md-4 bg-navy d-flex align-items-center justify-content-center p-4">
                                    <div class="text-center text-white">
                                        <div class="step-badge-white mb-3">01</div>
                                        <h4 class="fw-bold mb-0">{{ __('Fundamental') }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-8 p-4 p-lg-5">
                                    <h4 class="fw-bold text-navy mb-3">{{ __('Cash Flow Recording') }}</h4>
                                    <p class="text-muted mb-4">{{ __('The first crucial step is building discipline in recording every financial activity. Through this feature, you can monitor your liquidity in real-time.') }}</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <a href="{{ route('pemasukan.index') }}" target="_blank" class="corporate-link p-3 rounded-3 border d-flex align-items-center h-100">
                                                <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                                    <i class="bi bi-graph-up-arrow"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold text-dark mb-0">{{ __('Income') }}</span>
                                                    <small class="text-muted">{{ __('Manage revenue sources') }}</small>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm-6">
                                            <a href="{{ route('pengeluaran.index') }}" target="_blank" class="corporate-link p-3 rounded-3 border d-flex align-items-center h-100">
                                                <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                                                    <i class="bi bi-graph-down-arrow"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold text-dark mb-0">{{ __('Expense') }}</span>
                                                    <small class="text-muted">{{ __('Monitor cost burdens') }}</small>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column Split -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 corporate-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="step-badge me-3">02</div>
                                <h5 class="fw-bold text-navy mb-0">{{ __('Asset Allocation') }}</h5>
                            </div>
                            <h5 class="fw-bold mb-3"><a href="{{ route('dompet.index') }}" target="_blank" class="text-decoration-none text-navy hover-blue">{{ __('Wallet Management') }}</a></h5>
                            <p class="text-muted small mb-0">{{ __('Sructure your assets into various liquidity categories such as Cash, Bank Accounts, or Digital Wallets for higher reporting accuracy.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 corporate-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="step-badge me-3">03</div>
                                <h5 class="fw-bold text-navy mb-0">{{ __('Risk Management') }}</h5>
                            </div>
                            <h5 class="fw-bold mb-3"><a href="{{ route('dana-darurat.index') }}" target="_blank" class="text-decoration-none text-navy hover-blue">{{ __('Emergency Fund') }}</a></h5>
                            <p class="text-muted small mb-0">{{ __('Mitigate financial risks by building a cash reserve. The system will help calculate targets based on your spending profile.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm corporate-card">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="step-badge me-3">04</div>
                                <h5 class="fw-bold text-navy mb-0">{{ __('Financial Planning') }}</h5>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <h4 class="fw-bold text-dark mb-3">{{ __('Monitoring & Budgeting') }}</h4>
                                    <p class="text-muted mb-4 mb-lg-0">{{ __('Implement strict budget controls to ensure your financial operations stay within planned corridors.') }}</p>
                                </div>
                                <div class="col-lg-5">
                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                        <a href="{{ route('anggaran.index') }}" target="_blank" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center">
                                            <span class="fw-medium">{{ __('Budget Categories') }}</span>
                                            <i class="bi bi-chevron-right small opacity-50"></i>
                                        </a>
                                        <a href="{{ route('kalkulator.index') }}" target="_blank" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center">
                                            <span class="fw-medium">{{ __('Budget Monitoring') }}</span>
                                            <i class="bi bi-chevron-right small opacity-50"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Tools Grid -->
                <div class="col-12 mt-2">
                    <div class="p-4 bg-light rounded-4 border-style-dashed">
                        <h5 class="fw-bold text-navy mb-4 d-flex align-items-center">
                            <i class="bi bi-grid-fill me-2 opacity-50"></i> {{ __('Advanced Ecosystem Features') }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('tujuan-keuangan.index') }}" target="_blank" class="tool-card p-3 rounded-3 bg-white border text-decoration-none d-block">
                                    <div class="mb-2 text-primary opacity-75"><i class="bi bi-bullseye fs-4"></i></div>
                                    <span class="fw-bold text-navy d-block mb-1">{{ __('Financial Goals') }}</span>
                                    <small class="text-muted">{{ __('Future goal planning') }}</small>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('aset.index') }}" target="_blank" class="tool-card p-3 rounded-3 bg-white border text-decoration-none d-block">
                                    <div class="mb-2 text-primary opacity-75"><i class="bi bi-box-seam fs-4"></i></div>
                                    <span class="fw-bold text-navy d-block mb-1">{{ __('Inventory Assets') }}</span>
                                    <small class="text-muted">{{ __('Fixed asset management') }}</small>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('pinjaman.index') }}" target="_blank" class="tool-card p-3 rounded-3 bg-white border text-decoration-none d-block">
                                    <div class="mb-2 text-primary opacity-75"><i class="bi bi-arrow-down-up fs-4"></i></div>
                                    <span class="fw-bold text-navy d-block mb-1">{{ __('Liability') }}</span>
                                    <small class="text-muted">{{ __('Obligation & debt tracking') }}</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Footer -->
            <div class="text-center pb-5">
                <p class="text-muted small mb-0">© {{ date('Y') }} BIMMO - {{ __('Systematic Financial Management Solution.') }}</p>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --navy: #0f172a;
        --navy-light: #1e293b;
    }

    .text-navy { color: var(--navy); }
    .bg-navy { background-color: var(--navy); }

    .step-badge {
        width: 42px;
        height: 42px;
        background-color: #f1f5f9;
        color: #64748b;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        border: 1.5px solid #e2e8f0;
    }

    .step-badge-white {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        border: 1.5px solid rgba(255, 255, 255, 0.2);
    }

    .corporate-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.2s ease-in-out;
    }

    .corporate-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
    }

    .corporate-link {
        text-decoration: none;
        transition: all 0.2s ease;
        border-color: #e2e8f0 !important;
    }

    .corporate-link:hover {
        background-color: #f8fafc !important;
        border-color: #0d6efd !important;
        transform: translateY(-2px);
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .hover-blue:hover {
        color: #0d6efd !important;
    }

    .border-style-dashed {
        border: 2px dashed #e2e8f0;
    }

    .tool-card {
        transition: all 0.2s ease;
    }

    .tool-card:hover {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-3px);
    }

    [data-bs-theme="dark"] .bg-light {
        background-color: #1a1a1a !important;
    }

    [data-bs-theme="dark"] .bg-white {
        background-color: #242424 !important;
    }

    [data-bs-theme="dark"] .text-navy, 
    [data-bs-theme="dark"] .text-dark {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .corporate-card,
    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .list-group-item,
    [data-bs-theme="dark"] .border-style-dashed {
        border-color: #333 !important;
    }

    [data-bs-theme="dark"] .corporate-link:hover,
    [data-bs-theme="dark"] .tool-card:hover {
        background-color: #2d2d2d !important;
    }

    [data-bs-theme="dark"] .step-badge {
        background-color: #333;
        color: #94a3b8;
        border-color: #444;
    }
</style>
@endsection
