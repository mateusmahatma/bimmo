<div class="d-flex justify-content-center gap-1">
    <a href="{{ route('pinjaman.show', $pinjaman->hash) }}" class="btn btn-sm btn-outline-info rounded-circle" title="Detail" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-eye"></i>
    </a>
    <a href="#" class="btn btn-sm btn-outline-primary rounded-circle tombol-edit-pinjaman" data-id="{{ $pinjaman->hash }}" title="Edit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-pencil"></i>
    </a>
    <a href="#" class="btn btn-sm btn-outline-danger rounded-circle tombol-del-pinjaman" data-id="{{ $pinjaman->hash }}" title="Delete" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-trash"></i>
    </a>
</div>