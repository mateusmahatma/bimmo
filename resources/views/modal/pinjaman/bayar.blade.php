<div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bayarModalLabel">Bayar Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bayarForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_pinjaman" id="pinjamanId"> <!-- Hidden input untuk ID pinjaman -->

                    <div class="mb-3">
                        <label for="jumlah_bayar" class="col-form-label">Jumlah Bayar</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="form-control" placeholder="Input Jumlah Bayar Pinjaman" required>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_bayar" class="col-form-label">Tanggal Pembayaran</label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_bayar" class="col-form-label">Bukti Bayar (Opsional)</label>
                        <input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div id="current_file_container" class="mt-2 d-none">
                            <span class="small text-muted">File saat ini: </span>
                            <a id="current_file_link" href="#" target="_blank" class="small">Lihat Berkas</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-color2" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Tutup
                        </button>
                        <button type="submit" class="btn-sm btn-color">
                            <i class="fa fa-check"></i> Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>