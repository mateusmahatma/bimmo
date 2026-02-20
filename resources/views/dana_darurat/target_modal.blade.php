<!-- Modal Atur Target -->
<div class="modal fade" id="modalAturTarget" tabindex="-1" aria-labelledby="modalAturTargetLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAturTargetLabel">Atur Target Dana Darurat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dana-darurat.update-target') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="metode_target" class="form-label">Metode Target</label>
                        <select class="form-select" id="metode_target" name="metode_target">
                            <option value="otomatis" {{ $targetSettings['metode'] == 'otomatis' ? 'selected' : '' }}>Otomatis (Berdasarkan Pengeluaran)</option>
                            <option value="manual" {{ $targetSettings['metode'] == 'manual' ? 'selected' : '' }}>Manual (Input Nominal)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="inputManual" style="{{ $targetSettings['metode'] == 'manual' ? '' : 'display: none;' }}">
                        <label for="nominal_target" class="form-label">Nominal Target (Rp)</label>
                        <input type="number" class="form-control" id="nominal_target" name="nominal_target" value="{{ $targetSettings['nominal'] }}">
                    </div>

                    <div class="mb-3" id="inputOtomatis" style="{{ $targetSettings['metode'] == 'otomatis' ? '' : 'display: none;' }}">
                        <label for="kelipatan_target" class="form-label">Target Berapa Bulan Pengeluaran?</label>
                        <input type="number" class="form-control" id="kelipatan_target" name="kelipatan_target" value="{{ $targetSettings['kelipatan'] }}" min="1">
                        <div class="form-text">Target akan dihitung otomatis dari rata-rata pengeluaran bulanan dikali jumlah bulan ini.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metodeSelect = document.getElementById('metode_target');
        const inputManual = document.getElementById('inputManual');
        const inputOtomatis = document.getElementById('inputOtomatis');

        function toggleInputs() {
            if (metodeSelect.value === 'manual') {
                inputManual.style.display = 'block';
                inputOtomatis.style.display = 'none';
            } else {
                inputManual.style.display = 'none';
                inputOtomatis.style.display = 'block';
            }
        }

        metodeSelect.addEventListener('change', toggleInputs);
        
        // Run on load to set initial state
        toggleInputs();
    });
</script>
