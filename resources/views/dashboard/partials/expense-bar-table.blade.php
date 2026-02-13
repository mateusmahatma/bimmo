<div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead class="table-light">
            <tr>
                <th>Category</th>
                <th class="text-end">Total</th>
                <th class="text-end">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengeluaranKategori as $row)
            <tr>
                <td>{{ $row->kategori }}</td>
                <td class="text-end">
                    Rp {{ number_format($row->total,0,',','.') }}
                </td>
                <td class="text-end fw-bold
                    {{ $row->persen > 40 ? 'text-danger' : ($row->persen > 25 ? 'text-warning' : 'text-success') }}">
                    {{ $row->persen }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">
                    No expense data available
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
