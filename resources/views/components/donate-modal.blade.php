<!-- Donate Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-labelledby="donateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="donateModalLabel">
                    <i class="bi bi-heart-fill text-danger me-2"></i>{{ __('Donate') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted mb-3">{{ __('Support us by donating via QRIS below:') }}</p>
                <img src="{{ asset('img/qris_bimmo.jpg') }}" alt="QRIS Donate" class="img-fluid rounded border shadow-sm mb-3">
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{ asset('img/qris_bimmo.jpg') }}" download="qris_bimmo.jpg" class="btn btn-primary w-100 no-loader">
                    <i class="bi bi-download me-2"></i>{{ __('Simpan QRIS') }}
                </a>
            </div>
        </div>
    </div>
</div>
