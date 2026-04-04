<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('transaksi.importTest') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Import Excel') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Select Excel File') }}</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text">{{ __('Supported formats: .xlsx, .xls, .csv') }}</div>
                </div>
                <div class="alert alert-info d-flex align-items-center small border-0 bg-info-light text-info-dark" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        {{ __('Use the provided template to ensure correct data formatting.') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Import Data') }}</button>
            </div>
        </form>
    </div>
</div>
