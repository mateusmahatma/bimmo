<div class="dropdown text-center">
    <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; padding: 0;">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
        <li>
            <a class="dropdown-item py-2" href="{{ route('pinjaman.show', $pinjaman->hash) }}">
                <i class="bi bi-eye me-2 text-info"></i> {{ __('Detail') }}
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2 tombol-edit-pinjaman" href="#" data-id="{{ $pinjaman->hash }}">
                <i class="bi bi-pencil me-2 text-primary"></i> {{ __('Edit') }}
            </a>
        </li>
        <li><hr class="dropdown-divider opacity-50"></li>
        <li>
            <a class="dropdown-item py-2 text-danger tombol-del-pinjaman" href="#" data-id="{{ $pinjaman->hash }}">
                <i class="bi bi-trash me-2"></i> {{ __('Delete') }}
            </a>
        </li>
    </ul>
</div>