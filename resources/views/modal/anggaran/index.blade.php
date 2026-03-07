<div class="modal fade" id="anggaranModal" tabindex="-1" aria-labelledby="anggaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="anggaranModalLabel">{{ __('Add Data') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="required" for="nama" class="col-form-label">{{ __('Budget Name') }}</label>
                    <input type="text" id="nama_anggaran" class="form-control" name='nama_anggaran' placeholder="{{ __('Enter budget name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="required" for="nama" class="col-form-label">{{ __('Percentage') }}</label>
                    <input type="number" id="persentase_anggaran" class="form-control" name='persentase_anggaran' placeholder="{{ __('Enter budget percentage') }}" required>
                </div>
                <div class="mb-3">
                    <label for="id_pengeluaran" class="col-form-label">{{ __('Expense Types') }}</label>
                    <select name="id_pengeluaran[]" id="id_pengeluaran" class="form-select" multiple>
                        @foreach ($pengeluarans as $pengeluaran)
                        <option value="{{ $pengeluaran->id }}"
                            {{ in_array($pengeluaran->id, (array) $anggaran->id_pengeluaran) ? 'selected' : '' }}>
                            {{ $pengeluaran->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-success tombol-simpan-anggaran">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>