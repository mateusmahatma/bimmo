<div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="bayarModalLabel">Pay Loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form id="bayarForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_pinjaman" id="pinjamanId">

                    <div class="mb-3">
                        <label for="jumlah_bayar" class="form-label small fw-bold text-muted text-uppercase">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                            <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="form-control border-start-0 rounded-end-3" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_bayar" class="form-label small fw-bold text-muted text-uppercase">Payment Date</label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-0">
                        <label for="bukti_bayar" class="form-label small fw-bold text-muted text-uppercase">Payment Proof (Optional)</label>
                        <input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="current_file_container" class="mt-2 d-none p-2 bg-light rounded-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-check text-success me-2 fs-5"></i>
                                <span class="small text-muted">Current proof attached</span>
                            </div>
                            <a id="current_file_link" href="#" target="_blank" class="btn btn-sm btn-link text-primary p-0 text-decoration-none small fw-bold">View File</a>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-4 px-0 pb-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 btn-color shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Pay Loan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>