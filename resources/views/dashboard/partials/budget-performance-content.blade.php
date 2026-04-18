<div id="chartAnggaran" style="min-height: 350px;"></div>

<div id="burnRateSummary" class="mt-4 border-top pt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-primary"></i> {{ __('Burn Rate Summary') }}
        </h6>
        <span class="opacity-75 small" style="font-size: 0.7rem;">{{ __('Berdasarkan Periode Terpilih') }}</span>
    </div>
    <div class="row row-cols-1 row-cols-md-2 g-3" id="burnRateList"></div>
</div>
