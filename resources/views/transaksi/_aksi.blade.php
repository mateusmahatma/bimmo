@php
use Vinkla\Hashids\Facades\Hashids;
$hash = Hashids::encode($row->id);
@endphp

@if($row->file)
<a href="{{ asset('storage/uploads/' . $row->file) }}" 
    target="_blank"
    class="btn btn-sm btn-info text-white" 
    title="Lihat Bukti">
    <i class="bi bi-eye"></i>
</a>
<button type="button" 
    class="btn btn-sm btn-outline-danger btn-delete-file" 
    data-id="{{ $row->id }}" 
    title="Hapus Bukti">
    <i class="bi bi-x-circle"></i>
</button>
@endif

<button type="button" 
    class="btn btn-sm btn-primary btn-upload" 
    data-id="{{ $row->id }}" 
    data-bs-toggle="modal" 
    data-bs-target="#uploadModal" 
    title="Upload Bukti">
    <i class="bi bi-upload"></i>
</button>

<a href="{{ route('transaksi.edit', $hash) }}"
    class="btn btn-sm btn-warning">
    <i class="bi bi-pencil-square"></i>
</a>

<form action="{{ route('transaksi.destroy', $hash) }}"
    method="POST"
    class="d-inline form-delete">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Hapus">
        <i class="bi bi-trash-fill"></i>
    </button>
</form>