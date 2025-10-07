<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="importExcelModalLabel">Import Data Dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <ol type="1">
                        <li>Pastikan cell pada file Excel tidak ada yang di MERGE, jika ada yang di MERGE harap melakukan UNMERGE terlebih dahulu!</li>
                        <li>Pastikan file Excel tidak di Freeze</li>
                        <li>Pastikan file tidak dalam kondisi Protected View</li>
                        <li>
                            <button id="btn-download-template" class="btn btn-success" data-url=" {{ route('download-template') }}">
                                <span class="d-flex align-items-center gap-2">
                                    <span>Download Template Excel</span>
                                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </span>
                            </button>
                        </li>
                    </ol>
                </div>
                <form id="importForm" action="{{ route('import-transaksi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file">Pilih File Excel:</label>
                        <input type="file" name="file" id="file" class="form-control-file" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Tutup</button>
                        <button type="submit" class="btn btn-success" id="importBtn">
                            <i class="fa fa-download"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>