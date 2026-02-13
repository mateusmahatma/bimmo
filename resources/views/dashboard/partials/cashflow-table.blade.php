<table class="table table-sm align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Month</th>
            <th class="text-end">Income</th>
            <th class="text-end">Expense</th>
            <th class="text-end">Difference</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($cashflow as $row)
        <tr>
            <td>{{ \Carbon\Carbon::parse($row->bulan.'-01')->translatedFormat('F Y') }}</td>
            <td class="text-end">
                Rp {{ number_format($row->total_pemasukan,0,',','.') }}
            </td>
            <td class="text-end">
                Rp {{ number_format($row->total_pengeluaran,0,',','.') }}
            </td>
            <td class="text-end fw-bold">
                Rp {{ number_format($row->selisih,0,',','.') }}
                @if($row->selisih < 0)
                    <span class="badge bg-danger ms-1">Defisit</span>
                    @elseif($row->selisih < 1000000)
                        <span class="badge bg-warning text-dark ms-1">Tipis</span>
                        @else
                        <span class="badge bg-success ms-1">Aman</span>
                        @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted">
                No cash flow data available yet
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
