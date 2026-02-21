<table>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f2f2f2;">No</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Loan Name</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Description</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Total Loan</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Paid Amount</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Remaining Balance</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pinjaman as $p)
        @php
            $paidAmount = $p->bayar_pinjaman->sum('jumlah_bayar');
            $totalLoan = $p->jumlah_pinjaman + $paidAmount;
            $status = $p->status == 'lunas' ? 'Paid Off' : 'Outstanding';
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->nama_pinjaman }}</td>
            <td>{{ $p->keterangan ?? '-' }}</td>
            <td>Rp {{ number_format($totalLoan, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($paidAmount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
            <td>{{ $status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
