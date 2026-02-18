<div class="dropdown">
    <button class="btn btn-light btn-sm text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
        <li>
            <a class="dropdown-item" href="{{ route('kalkulator.show', $request->hash) }}">
                <i class="bi bi-eye me-2 text-primary"></i> Detail
            </a>
        </li>
        <li>
             <!-- Update Button -->
            <a class="dropdown-item tombol-update-proses-anggaran" href="#" data-id="{{ $request->hash }}">
                <i class="bi bi-arrow-repeat me-2 text-warning"></i> Update
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item tombol-del-proses-anggaran text-danger" href="#" data-id="{{ $request->hash }}">
                <i class="bi bi-trash me-2"></i> Delete
            </a>
        </li>
    </ul>
</div>