<div class="modal fade" id="pinjamanModal" tabindex="-1" aria-labelledby="pinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="pinjamanModalLabel">{{ __('Loan Information') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="nama_pinjaman" class="form-label small fw-bold text-muted text-uppercase">{{ __('Liability Name') }}</label>
                        <input type="text" id="nama_pinjaman" class="form-control rounded-3" name='nama_pinjaman' placeholder="{{ __('Enter loan name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="jumlah_pinjaman" class="form-label small fw-bold text-muted text-uppercase">{{ __('Loan Amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                            <input type="text" id="jumlah_pinjaman" class="form-control border-start-0 rounded-end-3" name='jumlah_pinjaman' placeholder="0" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="nominal_angsuran" class="form-label small fw-bold text-muted text-uppercase">{{ __('Monthly Installment') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                            <input type="text" id="nominal_angsuran" class="form-control border-start-0 rounded-end-3" name='nominal_angsuran' placeholder="0">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="jangka_waktu" class="form-label small fw-bold text-muted text-uppercase">{{ __('Duration (Months)') }}</label>
                        <input type="number" id="jangka_waktu" class="form-control rounded-3" name='jangka_waktu' placeholder="0">
                    </div>

                    <div class="col-md-4">
                        <label for="start_date" class="form-label small fw-bold text-muted text-uppercase">{{ __('Start Date') }}</label>
                        <input type="date" id="start_date" class="form-control rounded-3" name='start_date' value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="end_date" class="form-label small fw-bold text-muted text-uppercase">{{ __('End Date') }}</label>
                        <input type="date" id="end_date" class="form-control rounded-3" name='end_date'>
                    </div>

                    <div class="col-12">
                        <label for="status" class="form-label small fw-bold text-muted text-uppercase">{{ __('Status') }}</label>
                        <select id="status" class="form-select rounded-3" name="status">
                            <option value="belum_lunas">{{ __('Unpaid') }}</option>
                            <option value="lunas">{{ __('Paid') }}</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="keterangan" class="form-label small fw-bold text-muted text-uppercase">{{ __('Keterangan') }}</label>
                        <textarea id="keterangan" class="form-control rounded-3" name="keterangan" rows="3" placeholder="{{ __('Additional notes (optional)') }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 tombol-simpan-pinjaman">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
