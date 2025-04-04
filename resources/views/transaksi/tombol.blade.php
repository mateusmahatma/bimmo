<div class="dropdown dropstart">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton{{ $request->id }}">
        <button class="dropdown-item tombol-edit-transaksi" data-id="{{ $request->id }}">
            <i class="fas fa-edit mr-2"></i> Edit
        </button>
        <button class="dropdown-item tombol-del-transaksi" data-id="{{ $request->id }}">
            <i class="fas fa-trash mr-2"></i> Hapus
        </button>
        <button class="dropdown-item tombol-upload-file" data-id="{{ $request->id }}">
            <i class="fas fa-upload mr-2"></i> Upload
        </button>
        @if (!is_null($request->file) && file_exists(storage_path('app/public/uploads/' . $request->file)))
        <a class="dropdown-item" href="{{ asset('storage/uploads/' . $request->file) }}" target="_blank">
            <i class="fas fa-eye mr-2"></i> Lihat File
        </a>
        @else
        <button class="dropdown-item" disabled title="File not found">
            <i class="fas fa-eye-slash mr-2"></i> File Tidak Ditemukan
        </button>
        @endif
    </div>
</div>