<div class="modal fade" id="barangModal" tabindex="-1" aria-labelledby="barangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="barangModalLabel">Tambah Data Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="required" for="nama" class="col-form-label">Nama Barang</label>
                    <input type="text" id="nama_barang" class="form-control" name='nama_barang' placeholder="Input Nama Barang" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="col-form-label">Status</label>
                    <select class="form-select" id="status">
                        <!-- name="status" delete filter berjalan -->
                        <option value="belum terbeli">Belum Terbeli</option>
                        <option value="terbeli">Terbeli</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nama" class="col-form-label">Nama Toko</label>
                    <input type="text" id="nama_toko" class="form-control" name='nama_toko' placeholder="Input Nama Toko" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="col-form-label">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" step="0.01" placeholder="Masukkan Harga">
                </div>
                <div class="mb-3">
                    <label for="jumlah" class="col-form-label">Jumlah</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan Jumlah">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-color2" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup</button>
                <button type="button" class="btn-sm btn-color tombol-simpan-barang">
                    <i class="fa fa-paper-plane"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>