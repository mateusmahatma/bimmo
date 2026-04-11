<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="overflow: hidden;">
    <div class="card-header {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">{{ __('Debt Service Ratio') }}</h5>
        <p class="text-muted small mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Monthly debt burden vs income.') }}</p>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-0 text-{{ $debtStatus['class'] }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.4rem; letter-spacing: -1px;">
                {{ number_format($debtServiceRatio, 1) }}%
            </h2>
            <span class="badge bg-{{ $debtStatus['class'] }} mt-1">{{ $debtStatus['label'] }}</span>
        </div>

        <div class="progress-clean mb-4" style="height: 12px; background: #f1f5f9; position: relative;">
            {{-- Marker for 30% --}}
            <div style="position: absolute; left: 30%; top: -5px; bottom: -5px; width: 2px; background: #94a3b8; z-index: 2;" title="Limit: 30%"></div>
            
            <div class="progress-bar"
                 role="progressbar"
                 style="width: {{ min(100, $debtServiceRatio) }}%; height: 100%; border-radius: 100px; background: {{ $uiStyle === 'milenial' ? ($debtServiceRatio <= 30 ? 'var(--m-primary-gradient)' : 'var(--m-danger-gradient)') : ($debtServiceRatio <= 30 ? '#2563eb' : '#dc2626') }};"
                 aria-valuenow="{{ $debtServiceRatio }}"
                 aria-valuemin="0"
                 aria-valuemax="100">
            </div>
        </div>

        <div class="row g-3 text-center mb-4">
            <div class="col-6">
                <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'stat-card-inner' }}">
                    <p class="section-label mb-1">{{ __('Monthly Debt') }}</p>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">Rp {{ number_format($totalCicilanMonth, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-0 stat-card-inner">
                    <p class="section-label mb-1">{{ __('Monthly Income') }}</p>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">Rp {{ number_format($totalNominalMonthInc, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>

        <div class="alert-minimal {{ $debtServiceRatio <= 30 ? ($uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success') : ($uiStyle === 'milenial' ? 'bg-danger bg-opacity-10 text-danger' : 'alert-danger') }} text-center">
            @if($debtServiceRatio <= 30)
                <i class="bi bi-shield-check-fill me-1"></i>
                <span>{{ __('Healthy! Your debt is below 30% of income.') }}</span>
            @else
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <span>{{ __('Warning! Your debt exceeds 30% of income.') }}</span>
            @endif
        </div>
    </div>
</div>
