@php
use Vinkla\Hashids\Facades\Hashids;
$hash = Hashids::encode($row->id);
@endphp

<div class="dropdown">
    <button class="btn btn-light btn-sm text-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; padding: 0;">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
        <li>
            <a class="dropdown-item" href="{{ route('transaksi.edit', $hash) }}">
                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit
            </a>
        </li>
        <li>
            <button type="button" class="dropdown-item btn-upload" data-id="{{ $row->id }}" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload me-2 text-primary"></i> Upload Proof
            </button>
        </li>
        @if($row->file)
        <li>
            <a href="{{ asset('storage/uploads/' . $row->file) }}" target="_blank" class="dropdown-item">
                <i class="bi bi-eye me-2 text-info"></i> View Proof
            </a>
        </li>
        <li>
            <button type="button" class="dropdown-item btn-delete-file text-danger" data-id="{{ $row->id }}">
                <i class="bi bi-x-circle me-2"></i> Delete Proof
            </button>
        </li>
        @endif
        <li><hr class="dropdown-divider"></li>
        <li>
            <form action="{{ route('transaksi.destroy', $hash) }}" method="POST" class="form-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item text-danger btn-delete">
                    <i class="bi bi-trash me-2"></i> Delete
                </button>
            </form>
        </li>
    </ul>
</div>
