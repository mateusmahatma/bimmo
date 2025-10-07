<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton{{ $request->id }}">
        <button class="dropdown-item tombol-edit-transaksi" data-id="{{ $request->id }}">
            Edit
        </button>
        <button class="dropdown-item tombol-del-transaksi" data-id="{{ $request->id }}">
            Hapus
        </button>
        <button class="dropdown-item tombol-toggle-status" data-id="{{ $request->id }}" data-status="{{ $request->status }}">
            {{ $request->status == 1 ? 'Non Aktif' : 'Aktifkan' }}
        </button>
        <button class="dropdown-item tombol-upload-file" data-id="{{ $request->id }}">
            Upload
        </button>

        @if (!is_null($request->file) && Storage::disk('public')->exists('uploads/' . $request->file))
        <a href="{{ asset('storage/uploads/' . $request->file) }}"
            class="dropdown-item"
            target="_blank"
            rel="noopener">
            Lihat File
        </a>
        @else
        <button class="dropdown-item" disabled title="File not found">
            File Tidak Ditemukan
        </button>
        @endif


    </div>
</div>

<!-- Modal hanya perlu ditulis sekali di file utama, jangan taruh dalam loop DataTables -->