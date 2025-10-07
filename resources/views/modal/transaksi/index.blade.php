<!-- Modal Create -->
<div class="modal fade" id="transaksiModal" tabindex="-1" aria-labelledby="transaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="transaksiModalLabel">Tambah Data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTransaksi" action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <div class="modal-body overflow-auto">
                    <div class="mb-3">
                        <label class="required" for="tgl_transaksi" class="col-form-label">Tanggal Transaksi</label>
                        <div class="position-relative">
                            <input type="date" id="tgl_transaksi" name="tgl_transaksi" class="form-control" required>
                            <span class="spinner-border spinner-border-sm d-none input-spinner" role="status"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pemasukan" class="col-form-label">Pemasukan</label>
                        <select class="form-select" id="pemasukan" name="pemasukan">
                            <option value="">- Pilih -</option>
                            @foreach ($pemasukan as $item)
                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="nominal_pemasukan" name="nominal_pemasukan" class="form-control" placeholder="Input nominal">
                        <span class="input-group-text">.00</span>
                    </div>
                    <div class="mb-3">
                        <label for="pengeluaran" class="col-form-label">Pengeluaran</label>
                        <select class="form-select" id="pengeluaran" name="pengeluaran">
                            <option value="">- Pilih -</option>
                            @foreach ($pengeluaran as $item)
                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="nominal" name="nominal" class="form-control" placeholder="Input nominal">
                        <span class="input-group-text">.00</span>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="asset_list" id="checkAssetList" name="kategori[]">
                        <label class="form-check-label" for="checkAssetList">
                            Asset List
                        </label>
                    </div>
                    <div class="mb-3" id="selectBarangContainer" style="display: none;">
                        <label for="barang_id" class="form-label">Select Asset</label>
                        <select id="barang_id" name="barang_id" class="form-select">
                            <option value="">-- Select Asset --</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="emergency_fund" id="checkEmergencyFund" name="kategori[]">
                        <label class="form-check-label" for="checkEmergencyFund">
                            Emergency Fund
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="col-form-label">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button id="btnSimpan" type="button" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editTransaksiModal" tabindex="-1" aria-labelledby="editTransaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-mg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editTransaksiModalLabel">Edit Data Transaksi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTransaksiForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tgl_transaksi" class="form-label">Tanggal Transaksi</label>
                        <input type="date" id="edit_tgl_transaksi" name="tgl_transaksi" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_pemasukan" class="form-label">Pemasukan</label>
                        <select id="edit_pemasukan" name="pemasukan" class="form-select">
                            <option value="">- Pilih -</option>
                            @foreach ($pemasukan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nominal_pemasukan" class="form-label">Nominal Pemasukan</label>
                        <input type="number" id="edit_nominal_pemasukan" name="nominal_pemasukan" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_pengeluaran" class="form-label">Pengeluaran</label>
                        <select id="edit_pengeluaran" name="pengeluaran" class="form-select">
                            <option value="">- Pilih -</option>
                            @foreach ($pengeluaran as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nominal" class="form-label">Nominal</label>
                        <input type="number" id="edit_nominal" name="nominal" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <textarea id="edit_keterangan" name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Tutup</button>
                    <button type="submit" id="btnSimpan" class="btn btn-success">
                        <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
                        <span id="btnText">Memperbarui</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>