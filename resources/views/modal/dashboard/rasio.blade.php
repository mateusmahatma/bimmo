<!-- Modal Kompas Rasio Keuangan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalTitle">
                        <i class="bx bx-analyse text-primary fs-4"></i>
                        Detail Rasio Keuangan
                    </h5>
                    <small class="text-muted" id="detailModal"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="display">
                    <tbody>
                        <tr>
                            <th width="30%">Formula</th>
                            <td><span id="modalRumus"></span></td>
                        </tr>
                        <tr>
                            <th>Target</th>
                            <td><span id="modalTarget"></span></td>
                        </tr>
                        <tr>
                            <th>Saat Ini</th>
                            <td><span id="modalNominal"></span></td>
                        </tr>
                        <tr>
                            <th>Analisis</th>
                            <td><span id="modalAnalisis"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>