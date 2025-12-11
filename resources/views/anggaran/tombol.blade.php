<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('anggaran.edit', $request->id_anggaran) }}">
                Edit
            </a>
        </li>
        <li>
            <a class="dropdown-item tombol-del-anggaran" href="#" data-id="{{ $request->id_anggaran }}">
                Delete
            </a>
        </li>
    </ul>
</div>