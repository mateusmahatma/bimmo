<div class="modal fade" id="pinjamanModal" tabindex="-1" aria-labelledby="pinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pinjamanModalLabel">Input Nama Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-auto">
                <div class="mb-3">
                    <label for="nama_pinjaman" class="col-form-label">Nama</label>
                    <div class="position-relative">
                        <input type="text" id="nama_pinjaman" class="form-control" name='nama_pinjaman' placeholder="Input Nama Pinjaman" required>
                    </div>
                    <div class="position-relative">
                        <label for="jumlah_pinjaman" class="col-form-label">Jumlah Pinjaman</label>
                        <input type="number" id="jumlah_pinjaman" class="form-control" name='jumlah_pinjaman' placeholder="Input Nama Pinjaman" required>
                    </div>
                    <label for="jangka_waktu" class="col-sm-2 col-form-label" hidden>Jangka Waktu</label>
                    <div class="col-sm-10">
                        <input type="number" id="jangka_waktu" class="form-control" name='jangka_waktu' placeholder="Input Nama Pinjaman" required hidden>
                    </div>
                    <label for="start_date" class="col-sm-2 col-form-label" hidden>Start Date</label>
                    <div class="col-sm-10">
                        <input type="date" id="start_date" class="form-control" name='start_date' placeholder="Input Nama Pinjaman" required hidden>
                    </div>
                    <label for="end_date" class="col-sm-2 col-form-label" hidden>End Date</label>
                    <div class="col-sm-10">
                        <input type="date" id="end_date" class="form-control" name='end_date' placeholder="Input Nama Pinjaman" required hidden>
                    </div>
                    <label for="status" class="col-sm-2 col-form-label" hidden>Status</label>
                    <div class="col-sm-10">
                        <input type="text" id="status" class="form-control" name='status' placeholder="Input Nama Pinjaman" required hidden>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="col-form-label">Keterangan</label>
                    <textarea id="keterangan" class="form-control" name="keterangan" placeholder="Input Keterangan (Opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success tombol-simpan-pinjaman">Simpan</button>
            </div>
        </div>
    </div>
</div>