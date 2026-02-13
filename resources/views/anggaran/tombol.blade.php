<div class="dropdown text-center">
    <button class="btn btn-light btn-sm"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-boundary="viewport"
        data-bs-display="static"
        aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li>
            <a class="dropdown-item"
                href="{{ route('anggaran.edit', $request->id_anggaran) }}"
                data-id="{{ $request->id_anggaran }}">
                <i class="bi bi-pencil me-2 text-warning"></i> Edit
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item tombol-del-anggaran text-danger"
                href="#"
                data-id="{{ $request->id_anggaran }}">
                <i class="bi bi-trash me-2"></i> Delete
            </a>
        </li>
    </ul>
</div>