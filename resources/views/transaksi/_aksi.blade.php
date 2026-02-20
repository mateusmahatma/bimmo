@php
use Vinkla\Hashids\Facades\Hashids;
$hash = Hashids::encode($row->id);
@endphp

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