<div class="dropdown text-center">
    <button class="btn btn-light btn-sm text-secondary" 
            type="button" 
            data-bs-toggle="dropdown" 
            data-bs-boundary="viewport"
            aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
        <li>
            <a class="dropdown-item" href="#" onclick="updateProgress('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}')">
                <i class="bi bi-plus-circle me-2 text-success"></i> Update Progress
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="simulateGoal('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}', {{ $goal->nominal_target }}, {{ $goal->nominal_terkumpul }})">
                <i class="bi bi-graph-up me-2 text-info"></i> Simulate
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="viewHistory('{{ $goal->id_tujuan_keuangan }}', '{{ $goal->nama_target }}', {{ $goal->nominal_target }})">
                <i class="bi bi-clock-history me-2 text-secondary"></i> History
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger" href="#" onclick="deleteGoal('{{ $goal->id_tujuan_keuangan }}')">
                <i class="bi bi-trash me-2"></i> Delete
            </a>
        </li>
    </ul>
</div>
