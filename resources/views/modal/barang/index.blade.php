<div class="modal fade" id="barangModal" tabindex="-1" aria-labelledby="barangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="barangModalLabel">Add Asset</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Nama Barang --}}
                <div class="mb-3">
                    <label for="nama_barang" class="form-label required">Name</label>
                    <input type="text" id="nama_barang" class="form-control" name="nama_barang" placeholder="Input Nama Barang" required>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label for="status_barang" class="form-label">Status</label>
                    <select id="status_barang" class="form-control">
                        <option value="1">Assets Owned</option>
                        <option value="0">Mortgaged Assets</option>
                    </select>
                </div>

                {{-- Nama Toko --}}
                <div class="mb-3">
                    <label for="nama_toko" class="form-label">Store</label>
                    <input type="text" id="nama_toko" class="form-control" name="nama_toko" placeholder="Input Nama Toko" required>
                </div>

                {{-- Harga --}}
                <div class="mb-3">
                    <label for="harga" class="form-label">Price</label>
                    <input type="number" class="form-control" id="harga" name="harga" step="0.01" placeholder="Masukkan Harga">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="cssbuttons-io-button_2" data-bs-dismiss="modal">Back</button>
                <button type="button" class="cssbuttons-io-button tombol-simpan-barang">Save</button>
            </div>
        </div>
    </div>
</div>