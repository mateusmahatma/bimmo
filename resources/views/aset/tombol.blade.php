<div class="dropdown">
    <button class="btn btn-light btn-sm text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
        <li>
            <a class="dropdown-item" href="{{ route('aset.show', $row->id) }}">
                <i class="bi bi-eye me-2 text-primary"></i> Detail
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('aset.edit', $row->id) }}">
                <i class="bi bi-pencil me-2 text-warning"></i> Edit
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger delete-aset" href="#" data-id="{{ $row->id }}">
                <i class="bi bi-trash me-2"></i> Delete
            </a>
        </li>
    </ul>
</div>
