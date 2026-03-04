<div class="d-flex justify-content-center gap-2">
    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" 
            onclick="updateProgress('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}')"
            title="Update Progress">
        <i class="bi bi-plus-circle"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" 
            onclick="simulateGoal('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}', {{ $goal->nominal_target }}, {{ $goal->nominal_terkumpul }})"
            title="Simulate">
        <i class="bi bi-graph-up"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" 
            onclick="viewHistory('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}', {{ $goal->nominal_target }})"
            title="History">
        <i class="bi bi-clock-history"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
            onclick="deleteGoal('{{ $goal->id_tujuan_keuangan }}')"
            title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</div>
