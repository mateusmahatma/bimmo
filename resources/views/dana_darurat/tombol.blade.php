<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('dana-darurat.edit', $request->id_dana_darurat) }}">
                Edit
            </a>
        </li>
        <li><a class="dropdown-item tombol-del-dana-darurat" href="#" data-id="{{ $request->id_dana_darurat }}">Delete</a></li>
    </ul>
</div>