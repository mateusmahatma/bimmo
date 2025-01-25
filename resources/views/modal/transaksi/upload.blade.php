<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="uploadModalLabel">Upload File</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <ul>
                        <li>File yang dapat diupload : jpg,jpeg,png,pdf,doc,docx</li>
                    </ul>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="transaksiId">
                    <div class="mb-3">
                        <input type="file" name="file" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-sm btn-color2" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Tutup</button>
                        <button type="submit" class="btn-sm btn-color">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>