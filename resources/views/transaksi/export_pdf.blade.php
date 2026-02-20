<!DOCTYPE html>
<html>
<head>
    <title>Laporan Arus Kas</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; font-size: 12px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Arus Kas</h3>
    <p>Periode: {{ request('start_date') }} s/d {{ request('end_date') }}</p>
    <p>Total Pendapatan: Rp {{ number_format($totalPemasukan,0,',','.') }}</p>
    <p>Total Pengeluaran: Rp {{ number_format($totalPengeluaran,0,',','.') }}</p>
    <p>Laba Bersih: Rp {{ number_format($netIncome,0,',','.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pemasukan</th>
                <th>Masuk</th>
                <th>Pengeluaran</th>
                <th>Keluar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->tgl_transaksi)->format('d-m-Y') }}</td>
                <td>{{ optional($row->pemasukanRelation)->nama ?? '-' }}</td>
                <td style="text-align: right;">{{ number_format($row->nominal_pemasukan,0,',','.') }}</td>
                <td>{{ optional($row->pengeluaranRelation)->nama ?? '-' }}</td>
                <td style="text-align: right;">{{ number_format($row->nominal,0,',','.') }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>