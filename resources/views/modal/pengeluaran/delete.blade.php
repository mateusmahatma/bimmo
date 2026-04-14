<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow border-0" style="border-radius: 16px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Are you sure?') }}</h5>
                <p class="text-muted small mb-4">{{ __('Deleted data cannot be recovered!') }}</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>