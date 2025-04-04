<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="importExcelModalLabel">Import Data Dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <ul>
                        <li>Pastikan cell pada file Excel tidak ada yang di MERGE, jika ada yang di MERGE harap melakukan UNMERGE terlebih dahulu!</li>
                        <li>Pastikan file Excel tidak di Freeze</li>
                        <li>Pastikan file tidak dalam kondisi Protected View</li>
                    </ul>
                    <a href="{{ route('download-template') }}" download>Download Template Excel</a>
                </div>
                <form id="importForm" action="{{ route('import-transaksi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file">Pilih File Excel:</label>
                        <input type="file" name="file" id="file" class="form-control-file" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cssbuttons-io-button_2" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Tutup</button>
                        <button type="submit" class="cssbuttons-io-button" id="importBtn">
                            <i class="fa fa-download"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>