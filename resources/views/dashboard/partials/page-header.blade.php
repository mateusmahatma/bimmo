<div class="pagetitle dashboard-pagetitle mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="{{ $uiStyle === 'milenial' ? 'fw-extrabold ls-1' : '' }}">{{ __('Dashboard') }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted" style="font-size: 0.88rem;">
                {{ __('Hai') }}, <strong class="{{ $uiStyle === 'milenial' ? 'text-primary' : 'text-dark' }}">{{ auth()->user()->name }}</strong> 👋
            </span>
            <span class="dashboard-date-badge">
                <i class="bi bi-calendar3"></i>
                {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-light glass-card border-0' : 'btn-outline-secondary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#notesModal">
            <i class="bi bi-sticky fs-6 text-primary"></i>
            <span class="fw-semibold">{{ __('Notes') }}</span>
        </button>
        <a href="{{ route('transaksi.create') }}"
            class="btn btn-primary btn-sm rounded-0 px-3 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill fs-6"></i>
            <span class="fw-semibold">{{ __('Record Now') }}</span>
        </a>
    </div>
</div>
