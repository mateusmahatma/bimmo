@php
use Vinkla\Hashids\Facades\Hashids;
$hash = Hashids::encode($row->id);
@endphp

<a href="{{ route('transaksi.edit', $hash) }}"
    class="btn btn-sm btn-warning">
    Edit
</a>

<form action="{{ route('transaksi.destroy', $hash) }}"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Hapus
    </button>
</form>