<div class="modal fade" id="danaDaruratModal" tabindex="-1" aria-labelledby="danaDaruratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="danaDaruratModalLabel">Add Emergency Fund</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="required" for="tgl_transaksi_dana_darurat" class="col-form-label">Transaction Date</label>
                    <div class="position-relative">
                        <input type="date" id="tgl_transaksi_dana_darurat" name="tgl_transaksi_dana_darurat" class="form-control" required>
                        <span class="spinner-border spinner-border-sm d-none input-spinner" role="status"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jenis_transaksi_dana_darurat" class="form-label">Transaction Type</label>
                    <select id="jenis_transaksi_dana_darurat" name="jenis_transaksi_dana_darurat" class="form-control">
                        <option value="1">In</option>
                        <option value="2">Out</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nominal_dana_darurat" class="form-label">Nominal</label>
                    <input type="number" id="nominal_dana_darurat" class="form-control" name="nominal_dana_darurat" placeholder="Input Nominal" required>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Description</label>
                    <textarea type="text" class="form-control" id="keterangan" name="keterangan" step="0.01" placeholder="Input Description"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="cssbuttons-io-button_2" data-bs-dismiss="modal">Back</button>
                <button type="button" class="cssbuttons-io-button tombol-simpan-dana-darurat">Save</button>
            </div>
        </div>
    </div>
</div>