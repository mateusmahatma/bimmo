<div class="modal fade" id="pinjamanModal" tabindex="-1" aria-labelledby="pinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pinjamanModalLabel">Input Data Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <label for="kode_pinjaman" class="col-sm-2 col-form-label">Kode Pinjaman</label>
                    <div class="col-sm-10">
                        <input type="text" id="kode_pinjaman" class="form-control" name='kode_pinjaman' placeholder="Input Kode Pinjaman" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="nama_pinjaman" class="col-sm-2 col-form-label">Nama Pinjaman</label>
                    <div class="col-sm-10">
                        <input type="text" id="nama_pinjaman" class="form-control" name='nama_pinjaman' placeholder="Input Nama Pinjaman" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="jumlah_pinjaman" class="col-sm-2 col-form-label">Jumlah Pinjaman</label>
                    <div class="col-sm-10">
                        <input type="number" id="jumlah_pinjaman" class="form-control" name='jumlah_pinjaman' placeholder="Input Jumlah Pinjaman" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="sisa_pinjaman" class="col-sm-2 col-form-label">Sisa Pinjaman</label>
                    <div class="col-sm-10">
                        <input type="number" id="sisa_pinjaman" class="form-control" name='sisa_pinjaman' placeholder="Input Jumlah Pinjaman" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-color2" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-sm btn-color tombol-simpan-pinjaman">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pinjaman -->
<div class="modal fade" id="editPinjamanModal{{ $pinjaman->id }}" tabindex="-1" role="dialog" aria-labelledby="editPinjamanModalLabel{{ $pinjaman->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPinjamanModalLabel{{ $pinjaman->id }}">Edit Pinjaman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pinjaman.update', $pinjaman->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nama_pinjaman">Nama Pinjaman</label>
                        <input type="text" class="form-control" id="nama_pinjaman" name="nama_pinjaman" value="{{ $pinjaman->nama_pinjaman }}" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_pinjaman">Jumlah Pinjaman</label>
                        <input type="number" class="form-control" id="jumlah_pinjaman" name="jumlah_pinjaman" value="{{ $pinjaman->jumlah_pinjaman }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>