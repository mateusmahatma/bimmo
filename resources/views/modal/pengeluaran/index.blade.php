<div class="modal fade" id="pengeluaranModal" tabindex="-1" aria-labelledby="pengeluaranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pengeluaranModalLabel">Input Jenis Pengeluaran</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                    <div>
                        <strong>Attention:</strong>
                        <p class="mb-0">
                            Do not use commas (,) when creating expense types </p>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" id="nama" class="form-control" name='nama' placeholder="Input Jenis Pengeluaran" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="cssbuttons-io-button_2" data-bs-dismiss="modal">Close</button>
                <button type="button" class="cssbuttons-io-button tombol-simpan-pengeluaran">Simpan</button>
            </div>
        </div>
    </div>
</div>