<div class="modal fade" id="emailExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Export to Email') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Recipient Email') }}</label>
                    <input type="email" id="export_recipient_email" class="form-control" value="{{ auth()->user()->email }}" required>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        {{ __('Current filtered data will be sent to this email.') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btnConfirmExportEmail" class="btn btn-primary rounded-pill px-4">{{ __('Send') }}</button>
            </div>
        </div>
    </div>
</div>
