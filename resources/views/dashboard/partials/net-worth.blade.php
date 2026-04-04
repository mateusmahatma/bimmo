<div class="card card-dashboard border-0 shadow-none networth-card {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}"
     style="cursor: pointer;"
     data-bs-toggle="modal" data-bs-target="#netWorthModal"
     title="{{ __('Click to view Net Worth Details') }}">
    <div class="card-header d-flex justify-content-between align-items-center {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                {{ __('Net Worth') }}
            </h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Your solvency and wealth status.') }}</p>
        </div>
        <button type="button"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-primary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-1"
            data-bs-toggle="modal" data-bs-target="#netWorthModal">
            <i class="bi bi-clock-history"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">{{ __('History') }}</span>
        </button>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1 {{ $netWorth >= 1 ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.2rem; letter-spacing: -1px;">
                {{ $netWorthFormatted }}
            </h2>
        </div>

        <div class="progress-clean mb-4">
            <div class="d-flex h-100">
                <div class="{{ $uiStyle !== 'milenial' ? 'bg-success' : '' }}"
                     style="width: {{ $assetPercent }}%; border-radius: 100px 0 0 100px; {{ $uiStyle === 'milenial' ? 'background: var(--m-secondary-gradient);' : '' }}">
                </div>
                <div class="{{ $uiStyle !== 'milenial' ? 'bg-danger' : '' }}"
                     style="width: {{ $debtPercent }}%; border-radius: 0 100px 100px 0; {{ $uiStyle === 'milenial' ? 'background: var(--m-danger-gradient);' : '' }}">
                </div>
            </div>
        </div>

        <div class="row g-3 text-center mb-4">
            <div class="col-6">
                <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'stat-card-inner' }}">
                    <p class="section-label mb-1">{{ __('Wealth') }}</p>
                    <h6 class="fw-bold mb-0 text-success">Rp {{ number_format($totalAset + $totalDanaDarurat, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-danger bg-opacity-5' : 'stat-card-inner' }}">
                    <p class="section-label mb-1">{{ __('Debt') }}</p>
                    <h6 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>

        @if($netWorth >= 1)
            <div class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success' }} text-center">
                <i class="bi bi-shield-check-fill me-1"></i>
                <span>{{ __('Your assets comfortably cover your debts.') }}</span>
            </div>
        @else
            <div class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-warning bg-opacity-10 text-warning' : 'alert-warning' }} text-center">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <span>{{ __('Your debt level is higher than your assets.') }}</span>
            </div>
        @endif
    </div>
</div>
