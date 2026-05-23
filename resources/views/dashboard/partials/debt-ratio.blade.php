<div class="card shadow-none">
    <div class="card-header">
        <h5 class="card-title mb-0 fw-bold text-dark">{{ __('Debt Service Ratio') }}</h5>
        <p class="text-muted small mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Monthly debt burden vs income.') }}</p>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-0 text-{{ $debtStatus['class'] }} {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}"
                style="font-size: 2.4rem; letter-spacing: -1px;">
                {{ number_format($debtServiceRatio, 1) }}%
            </h2>
            <span class="badge bg-{{ $debtStatus['class'] }} mt-1">{{ $debtStatus['label'] }}</span>
        </div>

        <div class="progress progress-clean mb-4" role="progressbar" aria-label="Debt Service Ratio"
            aria-valuenow="{{ round($debtServiceRatio) }}" aria-valuemin="0" aria-valuemax="100"
            style="height: 12px; position: relative; overflow: visible;">

            {{-- Garis Penanda Batas 30% --}}
            <div style="position: absolute; left: 30%; top: -4px; height: 20px; width: 2px; background: #94a3b8; z-index: 3;"
                title="Limit: 30%"></div>

            {{-- Batang Progress Dinamis (Mengikuti struktur Dana Darurat Anda) --}}
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                style="width: {{ min(100, round($debtServiceRatio)) }}%; border-radius: 100px; background: {{ $debtServiceRatio <= 30 ? '#2563eb' : '#dc2626' }};">
            </div>

        </div>

        <div class="row g-3 text-center mb-4">
            <div class="col-6">
                <div class="card p-3">
                    <p class="section-label mb-1">{{ __('Monthly Debt') }}</p>
                    <h6 class="fw-bold mb-0 text-dark">Rp
                        {{ number_format($totalCicilanMonth, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3">
                    <p class="section-label mb-1">{{ __('Monthly Income') }}</p>
                    <h6 class="fw-bold mb-0 text-dark">Rp
                        {{ number_format($totalNominalMonthInc, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>

        <div class="alert-minimal text-center">
            @if ($debtServiceRatio <= 30)
                <i class="bi bi-shield-check-fill me-1"></i>
                <span>{{ __('Healthy! Your debt is below 30% of income.') }}</span>
            @else
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <span>{{ __('Warning! Your debt exceeds 30% of income.') }}</span>
            @endif
        </div>
    </div>
</div>
