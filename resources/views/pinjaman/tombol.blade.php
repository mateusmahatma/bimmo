<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item tombol-edit-detail" href="{{ route('pinjaman.show', $pinjaman->hash) }}" data-id="#">Detail</a></li>
        <li><a class="dropdown-item tombol-edit-pinjaman" href="#" data-id="{{ $pinjaman->hash }}">Edit</a></li>
        <li><a class="dropdown-item tombol-del-pinjaman" href="#" data-id="{{ $pinjaman->hash }}">Delete</a></li>
    </ul>
</div>