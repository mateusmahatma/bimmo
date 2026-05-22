<div class="card shadow-none">
    <div class="card-header">
        <h5 class="card-title mb-0 fw-bold text-dark">{{ __('Emergency Fund Progress') }}</h5>
        <p class="text-muted small mb-0 mt-1">{{ __('Track your safety net status.') }}</p>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-0 text-primary" style="font-size: 2.4rem; letter-spacing: -1px;">
                {{ $persentaseDanaDarurat }}%
            </h2>
            <p class="section-label mt-1 mb-0">{{ __('Achieved') }}</p>
        </div>

        <div class="progress-clean mb-4">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: {{ $persentaseDanaDarurat }}%; height: 100%; border-radius: 100px; background: {{ $uiStyle === 'milenial' ? 'var(--m-primary-gradient)' : '#2563eb' }};"
                aria-valuenow="{{ $persentaseDanaDarurat }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>

        <div class="row g-3 text-center mb-4">
            {{-- Kolom Actual --}}
            <div class="col-6">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">{{ __('Actual') }}</p>
                    <h6 class="fw-bold mb-0 text-primary">Rp
                        {{ number_format($totalDanaDarurat, 0, ',', '.') }}</h6>
                </div>
            </div>
            {{-- Kolom Target --}}
            <div class="col-6">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">{{ __('Target') }}</p>
                    <h6 class="fw-bold mb-0 text-dark">Rp
                        {{ number_format($targetDanaDarurat, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>

        @if ($persentaseDanaDarurat >= 100)
            <div
                class="alert-minimal {{ $uiStyle === 'milenial' ? 'bg-success bg-opacity-10 text-success' : 'alert-success' }} text-center">
                <i class="bi bi-check-circle-fill me-1"></i>
                <span>{{ __('Goal Reached! You are financially safe.') }}</span>
            </div>
        @else
            <p class="text-muted text-center mb-0" style="font-size: 0.82rem;">
                {{ __('Keep up the good work! Just') }}
                <strong class="text-primary">Rp {{ number_format($sisaDanaDarurat, 0, ',', '.') }}</strong>
                {{ __('left to reach your target.') }}
            </p>
        @endif
    </div>
</div>
