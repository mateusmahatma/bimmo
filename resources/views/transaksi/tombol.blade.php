<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton{{ $item->id }}">
        <a class="dropdown-item" href="{{ route('transaksi.edit', $item->hash) }}">
            Edit
        </a>

        <button class="dropdown-item tombol-del-transaksi" data-id="{{ $item->id }}">
            Hapus
        </button>

        <button class="dropdown-item tombol-toggle-status" data-id="{{ $item->id }}" data-status="{{ $item->status }}">
            {{ $item->status == 1 ? 'Non Aktif' : 'Aktifkan' }}
        </button>

        <button class="dropdown-item tombol-upload-file" data-id="{{ $item->id }}">
            Upload
        </button>

        @if (!is_null($item->file) && Storage::disk('public')->exists('uploads/' . $item->file))
        <a href="{{ asset('storage/uploads/' . $item->file) }}"
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