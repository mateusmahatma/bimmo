<div class="modal fade" id="anggaranModal" tabindex="-1" aria-labelledby="anggaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="anggaranModalLabel">Tambah Data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="required" for="nama" class="col-form-label">Nama Anggaran</label>
                    <input type="text" id="nama_anggaran" class="form-control" name='nama_anggaran' placeholder="Masukkan nama anggaran" required>
                </div>
                <div class="mb-3">
                    <label class="required" for="nama" class="col-form-label">Persentase</label>
                    <input type="number" id="persentase_anggaran" class="form-control" name='persentase_anggaran' placeholder="Masukkan persentase anggaran" required>
                </div>
                <div class="mb-3">
                    <label for="id_pengeluaran" class="col-form-label">Jenis Pengeluaran</label>
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
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success tombol-simpan-anggaran">Simpan</button>
            </div>
        </div>
    </div>
</div>