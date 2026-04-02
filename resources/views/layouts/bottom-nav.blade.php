<style>
    /* Bottom Navigation App Styling */
    .mobile-bottom-nav {
        background-color: var(--bs-body-bg);
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-bottom: max(10px, env(safe-area-inset-bottom)) !important;
        z-index: 1040; /* Above offcanvas backdrop if needed */
    }
    [data-bs-theme="dark"] .mobile-bottom-nav {
        background-color: #1a1d20 !important;
        border-top-color: rgba(255,255,255,0.05) !important;
    }
    .mobile-nav-item {
        color: #64748b;
        transition: color 0.2s ease;
        flex: 1;
    }
    .mobile-nav-item.active {
        color: #2563eb !important;
        font-weight: 600;
    }
    [data-bs-theme="dark"] .mobile-nav-item {
        color: #94a3b8;
    }
    [data-bs-theme="dark"] .mobile-nav-item.active {
        color: #3b82f6 !important;
    }
    .center-action-btn {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .center-action-btn:active {
        transform: scale(0.95);
    }
    [data-bs-theme="dark"] .center-action-btn {
        border-color: #1a1d20 !important; /* matching dark theme body bg */
    }
</style>

<nav class="mobile-bottom-nav d-md-none d-flex justify-content-around align-items-center fixed-bottom shadow-sm pt-2">
    <a href="{{ url('dashboard') }}" class="mobile-nav-item text-center text-decoration-none {{ request()->is('dashboard*') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 fs-4 d-block mb-1"></i>
        <span style="font-size: 0.65rem;">{{ __('Home') }}</span>
    </a>
    <a href="{{ route('dompet.index') }}" class="mobile-nav-item text-center text-decoration-none {{ request()->is('dompet*') ? 'active' : '' }}">
        <i class="bi bi-wallet2 fs-4 d-block mb-1"></i>
        <span style="font-size: 0.65rem;">{{ __('Wallet') }}</span>
    </a>
    
    <!-- Central Action Button -->
    <a href="{{ url('transaksi') }}" class="mobile-nav-item text-center text-decoration-none position-relative" style="flex: 1.2;">
        <div class="center-action-btn bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 50px; height: 50px; margin: -25px auto 4px auto; border: 4px solid var(--bs-body-bg);">
            <i class="bi bi-arrow-down-up fs-4"></i>
        </div>
        <span style="font-size: 0.65rem;" class="{{ request()->is('transaksi*') ? 'text-primary fw-bold' : '' }}">{{ __('Cash Flow') }}</span>
    </a>

    <a href="{{ route('anggaran.index') }}" class="mobile-nav-item text-center text-decoration-none {{ request()->is('anggaran*') ? 'active' : '' }}">
        <i class="bi bi-calculator-fill fs-4 d-block mb-1"></i>
        <span style="font-size: 0.65rem;">{{ __('Budget') }}</span>
    </a>
    
    <!-- Trigger Offcanvas Menu -->
    <a href="#" class="mobile-nav-item text-center text-decoration-none" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="bi bi-list fs-4 d-block mb-1"></i>
        <span style="font-size: 0.65rem;">{{ __('Menu') }}</span>
    </a>
</nav>
