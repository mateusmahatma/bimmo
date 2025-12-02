<div class="dropdown-sidebar">
    <button class="icon-elipsis" data-bs-toggle="dropdown" aria-expanded="false">
        &#8943;
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('pemasukan.edit', $request->id) }}">
                Edit
            </a>
        </li>
        <li><a class="dropdown-item tombol-del-pemasukan" href="#" data-id="{{ $request->id }}">Delete</a></li>
    </ul>
</div>