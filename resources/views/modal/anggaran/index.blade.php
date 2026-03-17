<div class="modal fade" id="anggaranModal" tabindex="-1" aria-labelledby="anggaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="anggaranModalLabel">{{ __('Add Data') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <label class="form-label fw-medium small text-uppercase text-muted required" for="nama_anggaran">{{ __('Budget Name') }}</label>
                    <input type="text" id="nama_anggaran" class="form-control" name='nama_anggaran' placeholder="{{ __('Enter budget name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium small text-uppercase text-muted required" for="persentase_anggaran">{{ __('Percentage') }}</label>
                    <input type="number" id="persentase_anggaran" class="form-control" name='persentase_anggaran' placeholder="{{ __('Enter budget percentage') }}" required>
                </div>
                <div class="mb-3">
                    <label for="id_pengeluaran" class="form-label fw-medium small text-uppercase text-muted">{{ __('Expense Types') }}</label>
                    <select name="id_pengeluaran[]" id="id_pengeluaran" class="form-select" multiple>
                        @foreach ($pengeluarans as $pengeluaran)
                        <option value="{{ $pengeluaran->id }}"
                            {{ in_array($pengeluaran->id, (array) ($anggaran->id_pengeluaran ?? [])) ? 'selected' : '' }}>
                            {{ $pengeluaran->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary px-4 tombol-simpan-anggaran">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>