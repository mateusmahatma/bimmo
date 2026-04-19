<div class="modal fade" id="openDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Pilih Tanggal') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Tanggal') }}</label>
                    <input type="date" id="input_open_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4"
                    data-bs-dismiss="modal">{{ __('Batal') }}</button>
                <button type="button" id="btnGoToDate"
                    class="btn btn-primary rounded-pill px-4">
                    {{ __('Buka') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnGoToDate').addEventListener('click', function() {
        const date = document.getElementById('input_open_date').value;
        window.location.href = '{{ url("transaksi/date") }}/' + date;
    });
</script>
</div>
</div>
</div>