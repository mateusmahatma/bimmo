<div class="card networth-card onclick="window.location='{{ route('dashboard.net-worth') }}'" title="{{ __('Click to view Net Worth Details') }}">
    <div
        class="card-header d-flex justify-content-between align-items-center {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}"
                style="font-size: 1rem;">
                {{ __('Net Worth') }}
            </h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Your solvency and wealth status.') }}</p>
        </div>
        <a href="{{ route('dashboard.net-worth') }}"
            class="btn btn-sm btn-primary px-3 d-flex align-items-center gap-2"
            wire:navigate>
                <i class="bi bi-clock-history"></i>
                <span class="fw-semibold" style="font-size: 0.75rem;">{{ __('History') }}</span>
        </a>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1 {{ $netWorth >= 1 ? 'text-success' : 'text-danger' }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}"
                style="font-size: 2.2rem; letter-spacing: -1px;">
                {{ $netWorthFormatted }}
            </h2>
        </div>

        <div class="progress-clean mb-4">
            <div class="d-flex h-100">
                <div class="{{ $uiStyle !== 'milenial' ? 'bg-success' : '' }}"
                    style="width: {{ $assetPercent }}%; {{ $uiStyle === 'milenial' ? 'background: var(--m-secondary-gradient);' : '' }}">
                </div>
                <div class="{{ $uiStyle !== 'milenial' ? 'bg-danger' : '' }}"
                    style="width: {{ $debtPercent }}%; {{ $uiStyle === 'milenial' ? 'background: var(--m-danger-gradient);' : '' }}">
                </div>
            </div>
        </div>

        <div class="row g-3 text-center mb-4">
            <div class="col-6">
                <div
                    class="card p-3">
                    <p class="section-label mb-1">{{ __('Wealth') }}</p>
                    <h6 class="fw-bold mb-0 text-success">Rp
                        {{ number_format($totalAset + $totalDanaDarurat + $totalSaldoDompet, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="col-6">
                <div
                    class="card p-3">
                    <p class="section-label mb-1">{{ __('Debt') }}</p>
                    <h6 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>

                             @if ($netWorth >= 1)
    <div class="alert-minimal text-center">
        <i class="bi bi-shield-check-fill me-1"></i>
        <span>{{ __('Your assets comfortably cover your debts.') }}</span>
    </div>
@else
    <div class="alert-minimal text-center">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        <span>{{ __('Your debt level is higher than your assets.') }}</span>
    </div>
    @endif
</div>
</div>
