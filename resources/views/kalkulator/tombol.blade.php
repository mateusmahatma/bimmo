<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('kalkulator.show', $request->id_proses_anggaran) }}">
                Detail
            </a>
        </li>
        <li><a class="dropdown-item tombol-update-proses-anggaran" href="#" data-id="{{ $request->id_proses_anggaran }}">Update</a></li>
        <li><a class="dropdown-item tombol-del-proses-anggaran" href="#" data-id="{{ $request->id_proses_anggaran }}">Delete</a></li>
    </ul>
</div>