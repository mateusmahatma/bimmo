<div class="action-buttons">
    <button class="btn-sm btn-color tombol-edit-transaksi" data-id="{{ $request->id }}">
        <i class="fas fa-edit"></i>
    </button>
    <button class="btn-sm btn-color2 tombol-del-transaksi" data-id="{{ $request->id }}">
        <i class="fas fa-trash"></i>
    </button>
    <button class="btn-sm btn-color tombol-upload-file" data-id="{{ $request->id }}">
        <i class="fas fa-upload"></i>
    </button>
    @if (!is_null($request->file) && file_exists(storage_path('app/public/uploads/' . $request->file)))
    <a href="{{ asset('storage/uploads/' . $request->file) }}" target="_blank">
        <button class="btn-sm btn-color">
            <i class="fas fa-eye"></i>
        </button>
    </a>
    @else
    <button class="btn-sm btn-color" disabled title="File not found">
        <i class="fas fa-eye-slash"></i>
    </button>
    @endif
</div>