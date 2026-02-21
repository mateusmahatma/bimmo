<div class="modal fade" id="pinjamanModal" tabindex="-1" aria-labelledby="pinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="pinjamanModalLabel">Loan Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label for="nama_pinjaman" class="form-label small fw-bold text-muted text-uppercase">Loan Name</label>
                    <input type="text" id="nama_pinjaman" class="form-control rounded-3" name='nama_pinjaman' placeholder="Enter loan name" required>
                </div>
                <div class="mb-3">
                    <label for="jumlah_pinjaman" class="form-label small fw-bold text-muted text-uppercase">Loan Amount</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                        <input type="number" id="jumlah_pinjaman" class="form-control border-start-0 rounded-end-3" name='jumlah_pinjaman' placeholder="0" required>
                    </div>
                </div>
                
                {{-- Hidden fields kept for functional consistency --}}
                <input type="number" id="jangka_waktu" name='jangka_waktu' value="0" hidden>
                <input type="date" id="start_date" name='start_date' value="{{ date('Y-m-d') }}" hidden>
                <input type="date" id="end_date" name='end_date' value="{{ date('Y-m-d') }}" hidden>
                <input type="text" id="status" name='status' value="belum_lunas" hidden>

                <div class="mb-0">
                    <label for="keterangan" class="form-label small fw-bold text-muted text-uppercase">Description</label>
                    <textarea id="keterangan" class="form-control rounded-3" name="keterangan" rows="3" placeholder="Additional notes (optional)"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 tombol-simpan-pinjaman">Save</button>
            </div>
        </div>
    </div>
</div>
