<!-- Modal Detail Pertumbuhan Finansial -->
<div class="modal fade" id="growthDetailModal" tabindex="-1" aria-labelledby="growthDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: {{ auth()->user()->ui_style === 'milenial' ? 'var(--m-radius-lg)' : '15px' }}; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center {{ auth()->user()->ui_style === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
                <h5 class="modal-title fw-bold" id="growthDetailModalLabel">
                    <span id="growthDetailTitle">Detail Perbandingan</span>
                </h5>
                <button type="button" class="btn-close {{ auth()->user()->ui_style === 'milenial' ? 'm-btn-close-modern' : '' }}" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <div id="growthPercentBadge" class="d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 fw-bold fs-5 shadow-sm">
                        <i id="growthIcon" class="bi"></i>
                        <span id="growthPercentValue">0%</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">{{ __('Compared to the same period last month') }}</p>
                </div>

                <div class="comparison-container rounded-4 p-3 {{ auth()->user()->ui_style === 'milenial' ? 'bg-primary bg-opacity-5' : 'bg-light border' }}">
                    <div class="row align-items-center g-0">
                        <div class="col-5">
                            <div class="text-center">
                                <p class="text-muted small text-uppercase fw-bold ls-1 mb-1 opacity-75" style="font-size: 0.65rem;">{{ __('This Month (MTD)') }}</p>
                                <h6 class="fw-bold mb-0 text-dark" id="currentPeriodValue">Rp 0</h6>
                                <p class="text-muted extra-small mb-0" style="font-size: 0.6rem;">{{ \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->translatedFormat('d M') }} - {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M') }}</p>
                            </div>
                        </div>
                        <div class="col-2 text-center text-muted">
                            <i class="bi bi-arrow-left-right fs-5 opacity-50"></i>
                        </div>
                        <div class="col-5">
                            <div class="text-center">
                                <p class="text-muted small text-uppercase fw-bold ls-1 mb-1 opacity-75" style="font-size: 0.65rem;">{{ __('Last Month (MTD)') }}</p>
                                <h6 class="fw-bold mb-0 text-dark" id="lastPeriodValue">Rp 0</h6>
                                <p class="text-muted extra-small mb-0" style="font-size: 0.6rem;">{{ \Carbon\Carbon::now('Asia/Jakarta')->subMonthsNoOverflow(1)->startOfMonth()->translatedFormat('d M') }} - {{ \Carbon\Carbon::now('Asia/Jakarta')->subMonthsNoOverflow(1)->translatedFormat('d M') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <span class="text-muted small fw-semibold">{{ __('Difference') }}</span>
                        <span id="growthDiffValue" class="fw-bold">Rp 0</span>
                    </div>
                    <div class="alert border-0 py-2 mb-0 rounded-3 d-flex align-items-start gap-2" id="growthSummaryAlert">
                        <i id="growthSummaryIcon" class="bi fs-5"></i>
                        <span id="growthSummaryText" class="small fw-medium"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn {{ auth()->user()->ui_style === 'milenial' ? 'btn-primary rounded-pill w-100' : 'btn-secondary rounded-pill px-4' }}" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
.extra-small {
    font-size: 0.75rem;
}
#growthPercentBadge.bg-success-light {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}
#growthPercentBadge.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}
#growthPercentBadge.bg-secondary-light {
    background-color: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}
</style>
