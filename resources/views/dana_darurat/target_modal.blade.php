<!-- Modal Atur Target -->
<div class="modal fade" id="modalAturTarget" tabindex="-1" aria-labelledby="modalAturTargetLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAturTargetLabel">{{ __('Set Emergency Fund Target') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dana-darurat.update-target') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="metode_target" class="form-label">{{ __('Target Method') }}</label>
                        <select class="form-select" id="metode_target" name="metode_target">
                            <option value="otomatis" {{ $targetSettings['metode'] == 'otomatis' ? 'selected' : '' }}>{{ __('Automatic (Based on Expenses)') }}</option>
                            <option value="manual" {{ $targetSettings['metode'] == 'manual' ? 'selected' : '' }}>{{ __('Manual (Input Amount)') }}</option>
                        </select>
                    </div>

                    <div class="mb-3" id="inputManual" style="{{ $targetSettings['metode'] == 'manual' ? '' : 'display: none;' }}">
                        <label for="nominal_target" class="form-label">{{ __('Target Amount (Rp)') }}</label>
                        <input type="number" class="form-control" id="nominal_target" name="nominal_target" value="{{ $targetSettings['nominal'] }}">
                    </div>

                    <div class="mb-3" id="inputOtomatis" style="{{ $targetSettings['metode'] == 'otomatis' ? '' : 'display: none;' }}">
                        <label for="kelipatan_target" class="form-label">{{ __('Target How Many Months of Expenses?') }}</label>
                        <input type="number" class="form-control" id="kelipatan_target" name="kelipatan_target" value="{{ $targetSettings['kelipatan'] }}" min="1">
                        <div class="form-text">{{ __('Target will be calculated automatically based on average monthly expenses multiplied by this number of months.') }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
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
