<div class="modal fade" id="pinjamanModal" tabindex="-1" aria-labelledby="pinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pinjamanModalLabel">Input Nama Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <label for="nama_pinjaman" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" id="nama_pinjaman" class="form-control" name='nama_pinjaman' placeholder="Input Nama Pinjaman" required>
                    </div>
                    <label for="jumlah_pinjaman" class="col-sm-2 col-form-label">Jumlah Pinjaman</label>
                    <div class="col-sm-10">
                        <input type="number" id="jumlah_pinjaman" class="form-control" name='jumlah_pinjaman' placeholder="Input Nama Pinjaman" required disabled>
                    </div>
                    <label for="jangka_waktu" class="col-sm-2 col-form-label">Jangka Waktu</label>
                    <div class="col-sm-10">
                        <input type="number" id="jangka_waktu" class="form-control" name='jangka_waktu' placeholder="Input Nama Pinjaman" required disabled>
                    </div>
                    <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                    <div class="col-sm-10">
                        <input type="date" id="start_date" class="form-control" name='start_date' placeholder="Input Nama Pinjaman" required disabled>
                    </div>
                    <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                    <div class="col-sm-10">
                        <input type="date" id="end_date" class="form-control" name='end_date' placeholder="Input Nama Pinjaman" required disabled>
                    </div>
                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <input type="text" id="status" class="form-control" name='status' placeholder="Input Nama Pinjaman" required disabled>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="cssbuttons-io-button_2" data-bs-dismiss="modal">Close</button>
                <button type="button" class="cssbuttons-io-button tombol-simpan-pinjaman">Simpan</button>
            </div>
        </div>
    </div>
</div>